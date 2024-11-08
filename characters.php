<?php
	session_start();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
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
	$defence_stats_html = '';
	$details_stats_html = '';
	$misc_stats_html = '';
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$search_input = $_POST['search_input'];
		$player_profile = get_player_by_id($search_input, "all");
		if ($player_profile->player_id != 0) {
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
			}
			$resonance = check_resonance($equipped_items["W"] ?? null, $equipped_items["R"] ?? null);
			$tarot_card = get_tarot_by_id($player_profile, $resonance);
			$player_profile->get_player_multipliers();
			$player_main_html = $player_profile->display_player($equipped_items["W"] ?? null);
			$element_stats_html = $player_profile->display_element_stats($equipped_items["W"] ?? null);
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
		$html = '<div class="item-slot" id="item-' . $type . '">';
		if (isset($equipped_items[$type])) {
			$html .= $equipped_items[$type]->display_item();
		} else {
			$type_name = isset($tag_dict[$type]) ? $tag_dict[$type] : "Unknown";
			$html .= "Empty Slot: " . $type_name;
		}
		$html .= '</div>';
		// Gem Slot
		$html .= '<div class="item-slot display-off" id="gem-' . $type . '">';
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
	<form id="filter-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<input type="text" name="search_input" placeholder="Enter Player ID/Username or Discord ID" required>
		<button type="submit" class="input-button">Search</button>
	</form>

    <!-- Main Content Section -->
    <main>
      <div id="content-container">
        <div id="detail-box"><h1>No Character Loaded</h1></div>
        <div id="detail-buttons">
          <button type="button" id="player-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(0)"><span class="player-button-img"></span></button>
          <button type="button" id="elemental-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(1)"><span class="elemental-button-img"></span></button>
          <button type="button" id="defense-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(2)"><span class="defense-button-img"></span></button>
          <button type="button" id="details-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(3)"><span class="details-button-img"></span></button>
          <button type="button" id="misc-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(4)"><span class="misc-button-img"></span></button>
          <button type="button" id="reload-button" class="char-nav-button char-nav-hover" onclick="refreshPlayerData()"><span class="reload-button-img"></span></button>
        </div>
			<div id=slot-buttons></div>
		  </div>
		<div id="slot-display">
			<?php 
				if ($player_profile) { 
					echo display_equipment('W', $equipped_items, $equipped_gems);
				} 
			?>
		</div>
    </main>
	<script src="scripts/charDetails.js"></script>
	<script>
		let playerProfileExists = <?php echo $player_profile ? 'true' : 'false'; ?>;
		let hoverEventListeners = [];
		const sectionContent = {
			0: `<?php echo $player_main_html; ?>`,
			1: `<?php echo $element_stats_html; ?>`,
			2: `<?php echo $defence_stats_html; ?>`,
			3: `<?php echo $details_stats_html; ?>`,
			4: `<?php echo $misc_stats_html; ?>`
		};

		if (playerProfileExists) {
			document.getElementById('detail-buttons').style.display = "flex";
			document.getElementById('detail-box').style.display = "flex";
			// document.getElementById('details-box').innerHTML = sectionContent[0];
			handleButtonClick(0);
		}
	</script>
	<script src="scripts/header.js"></script>
</body>
</html>
