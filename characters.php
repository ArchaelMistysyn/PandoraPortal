<?php
	session_start();
	/* Enable PHP reporting if needed
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL); */
?>
<!DOCTYPE html>
<html lang="en">
<?php
	// Inclusions
	include_once('./bot_php/db_queries.php');
	include_once('./bot_php/globals.php');
	include_once('./bot_php/player.php');
	include_once('./bot_php/tarot.php');
	include_once('./bot_php/insignia.php');
	include_once('./bot_php/pact.php');
	include_once('./bot_php/path.php');
	include_once('./bot_php/inventory.php');
	include_once('./bot_php/itemrolls.php');
	
	// Initializations
	$player_profile = null;
	$tarot_card = null;
	$error = '';
	$equipped_items = [];
	$equipped_gems= [];
	$player_main_html = '';
	$element_stats_html = '';
	$resist_stats_html = '';
	$resist_stats_html = '';
	$defence_stats_html = '';
	$details_stats_html = '';
	$misc_stats_html = '';
	$total_gear_score  = 0;
	
	if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search_input'])) {
		$search_input = trim($_GET['search_input']);
		$search_input = htmlspecialchars($search_input, ENT_QUOTES, 'UTF-8');
		if (ctype_digit($search_input)) {
			$search_input = (int)$search_input;
		}
		$player_profile = get_player_by_id($search_input, "all");
		if ($player_profile && $player_profile->player_id != 0) {
			$player_id = $player_profile->player_id;
			$non_zero_equipped = array_filter($player_profile->player_equipped, function($value) { return $value != '0'; });
			$item_type_map = [];
			if (!empty($non_zero_equipped)) {
				$equipped_ids = array_map('intval', $non_zero_equipped);
				$equipped_placeholder = implode(',', $equipped_ids);
				if ($equipped_placeholder) {
					$result_items = read_custom_item(null, $equipped_placeholder);
					foreach ($result_items as $row) {
						$equipped_items[$row->item_type] = $row;
						if ($row->item_inlaid_gem_id != 0) {
							$equipped_gem_ids[] = $row->item_inlaid_gem_id;
							$item_type_map[$row->item_inlaid_gem_id] = $row->item_type;
						}
					}
				}
				if (!empty($equipped_gem_ids)) {
					$equipped_gem_ids_placeholder = implode(',', array_map('intval', $equipped_gem_ids));
					$result_gems = read_custom_item(null, $equipped_gem_ids_placeholder);
					foreach ($result_gems as $gem_row) {
						$gem_id = $gem_row->item_id;
						if (isset($item_type_map[$gem_id])) {
							$type = $item_type_map[$gem_id];
							$equipped_gems[$type] = $gem_row; 
						}
					}
				}
				foreach ($equipped_items as $item) {
					$total_gear_score += $item->get_gear_score();
				}
				foreach ($equipped_gems as $gem) {
					$total_gear_score += $gem->get_gear_score();
				}
			}
			$resonance = check_resonance($equipped_items["W"] ?? null, $equipped_items["R"] ?? null);
			$tarot_card = get_tarot_by_id($player_profile, $resonance);
			$player_profile->get_player_multipliers();
			$player_main_html = $player_profile->display_player($equipped_items["W"] ?? null, $total_gear_score);
			$element_stats_html = $player_profile->display_element_stats($equipped_items["W"] ?? null);
			$resist_stats_html = $player_profile->display_resistances();
			$defence_stats_html = $player_profile->display_defences();
			$details_stats_html = $player_profile->display_details();
			$misc_stats_html = $player_profile->display_misc_stats();
		} else {
			$error = 'No player found.';
		}
	}
	
	// Item Data
	function display_equipment($type, $equipped_items, $equipped_gems) {
		global $tag_dict;
		$html = '<div class="item-slot card1 active" id="item-' . $type . '">';
		if (isset($equipped_items[$type])) {
			$html .= $equipped_items[$type]->display_item();
		} else {
			$type_name = isset($tag_dict[$type]) ? $tag_dict[$type] : "Unknown";
			$html .= "Empty Slot: " . $type_name;
		}
		$html .= '</div>';
		// Gem Slot
		$html .= '<div class="item-slot card2" id="gem-' . $type . '">';
		if (isset($equipped_gems[$type])) {
			$html .= $equipped_gems[$type]->display_item(true);
		} else {
			$html .= "Empty Slot: Gem";
		}
		$html .= '</div>';
		return $html;
	}
	
	
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Characters</title>
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
	<link rel="stylesheet" href="CSS/characterCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="page-body">
	<header id="header"></header>
    <!-- Main Content Section -->
    <main id="character-main">
		<div id="top-container">
			<div id="content-container">
				<div id="detail-buttons">
					<button type="button" id="player-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(0)"><span class="player-button-img"></span></button>
					<button type="button" id="elemental-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(1)"><span class="elemental-button-img"></span></button>
					<button type="button" id="resist-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(2)"><span class="resist-button-img"></span></button>
					<button type="button" id="defense-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(3)"><span class="defense-button-img"></span></button>
					<button type="button" id="details-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(4)"><span class="details-button-img"></span></button>
					<button type="button" id="misc-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(5)"><span class="misc-button-img"></span></button>
					<button type="button" id="reload-button" class="char-nav-button char-nav-hover" onclick="refreshPlayerData()"><span class="reload-button-img"></span></button>
				</div>
				<div id="character-box-container">
					<div id="detail-box"><div id="player-info">
						<div id="char-name-section">
							<form id="filter-form" method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
								<input type="text" autocomplete="off" name="search_input" placeholder="Enter Player ID/Username or Discord ID" required>
								<button type="submit" class="input-button">Search</button>
							</form>
						</div>
						<h1 id="character-error"><?php echo $error === '' ? "No Character Loaded" : $error; ?></h1>
					</div></div>
				</div>
			</div>
			<div id="slot-display">
				<?php 
					if ($player_profile) { 
						foreach ($slot_types as $slot_id => $type) {
							if ($slot_id === 'Pact') {
								echo display_pact($player_profile);
							} elseif ($slot_id === 'Insignia') {
								echo display_insignia($player_profile);
							} elseif ($slot_id === 'Tarot') {
								echo display_tarot($tarot_card);
							} else {
								echo display_equipment($slot_id, $equipped_items, $equipped_gems);
							}
						}
					}
				?>
			</div>
		</div>
		<div id="bottom-container"><div id="slot-buttons-container">
			<?php
				foreach ($slot_types as $slot_id => $type) {
				if ($slot_id === 'Pact' && !empty($player_profile->player_pact)) {
					$icon_path = (new Pact($player_profile))->pact_link;
				} elseif ($slot_id === 'Insignia' && !empty($player_profile->player_insignia)) {
					$icon_path = (new Insignia($player_profile))->insignia_link;
				} elseif ($slot_id === 'Tarot' && !empty($player_profile->equipped_tarot)) {
					$icon_path = (get_tarot_by_id($player_profile, $resonance))->essence_link;
				} else {
					$icon_path = isset($equipped_items[$slot_id]) ? $equipped_items[$slot_id]->get_gear_thumbnail($encode_filename = true) : '';
				}						
				$background_style = $icon_path ? "background-image: url(\"$icon_path\");" : '';
				$slot_condition = !empty($icon_path);
				$class_name = $slot_condition ? 'item-slot-icon' : 'item-slot-icon-empty';
				$inner_text = $slot_condition ? '' : 'Empty';
				echo "<button type='button' id='item-slot-{$slot_id}' class='item-slot-button' onclick='showEquipmentSlot(\"{$slot_id}\")'><span class='{$class_name}' style='{$background_style}'>{$inner_text}</span></button>";
				}
			?>
		</div></div>
    </main>
	<script src="scripts/charDetails.js"></script>
	<script>
		let playerProfileExists = <?php echo $player_profile ? 'true' : 'false'; ?>;
		let hoverEventListeners = [];
		const sectionContent = {
			0: `<?php echo $player_main_html; ?>`,
			1: `<?php echo $element_stats_html; ?>`,
			2: `<?php echo $resist_stats_html; ?>`,
			3: `<?php echo $defence_stats_html; ?>`,
			4: `<?php echo $details_stats_html; ?>`,
			5: `<?php echo $misc_stats_html; ?>`
		};
		if (playerProfileExists) {
			document.getElementById('detail-buttons').style.display = "flex";
			document.getElementById('slot-buttons-container').style.display = "flex";
			handleButtonClick(0);
			showEquipmentSlot("W");
		}
	</script>
	<script src="scripts/header.js"></script>
	<script src="scripts/screensizeWarning.js"></script>
</body>
</html>