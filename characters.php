<!DOCTYPE html>
<html lang="en">
<?php
	session_start();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

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
		if (isset($equipped_items[$type]) && $equipped_items[$type]->item_num_sockets == 0) {
			$html .= '<div class="bottom-button"><button type="button" class="toggle-button empty-socket" disabled>No Socket</button></div>';
		} elseif (isset($equipped_gems[$type])) {
			$html .= '<div class="bottom-button"><button type="button" class="toggle-button toggle-hover" onclick="toggleItem(\'' . $type . '\')">Toggle Gem</button></div>';
		} else {
			$html .= '<div class="bottom-button"><button type="button" class="toggle-button empty-socket" disabled>Empty Socket</button></div>';
		}
		$html .= '</div>';
		// Gem Slot
		$html .= '<div class="item-slot display-off" id="gem-' . $type . '">';
		if (isset($equipped_gems[$type])) {
			$html .= $equipped_gems[$type]->display_item(true);
		} else {
			$html .= "Empty Slot: Gem";
		}
		$html .= '<div class="bottom-button"><button type="button" class="toggle-button toggle-hover" onclick="toggleItem(\'' . $type . '\')">Toggle Item</button></div>';
		$html .= '</div>';
		return $html;
	}
	
	
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Characters</title>
    <link rel="stylesheet" href="pandoraCSS.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
			<a href="index.php">
				<img src="./images/icon.png" alt="Website Icon">
				<h1>Characters</h1>
			</a>
        </div>
		<nav id="primary-nav">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="search_input" placeholder="Enter Player ID/Username or Discord ID" required>
                <button type="submit" class="input-button">Search</button>
            </form>
        </nav>
        <nav id="page-nav">
            <ul>
				<li><a href="wiki.php">Wiki</a></li>
                <li><a href="characters.php" class="selected">Character</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="ranking.php">Ranking</a></li>
                <li><a href="https://www.ArchDragonStore.ca" target="_blank">Store</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Content Section -->
    <main class="no-footer">
        <div class="content-container character-flex">
			<div class="top-container">
				<!-- Character Section (Top Left) -->
				<?php 
					if ($player_profile) {
						echo "<div id='character-section-visible'>" . $player_main_html . "</div>";
						echo '<div id="char-nav-buttons">';
						echo '<button type="button" id="player-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(0)">Player</button>';
						echo '<button type="button" id="elemental-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(1)">Element</button>';
						echo '<button type="button" id="defense-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(2)">Defence</button>';
						echo '<button type="button" id="details-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(3)">Details</button>';
						echo '<button type="button" id="misc-button" class="char-nav-button char-nav-hover" onclick="handleButtonClick(4)">Misc</button>';
						echo '<button type="button" id="reload-button" class="char-nav-button char-nav-hover" onclick="refreshPlayerData()">Reload</button>';
						echo '</div>';
					} else {
						echo "<div id='character-section'><div id='character-name-section' class='center-msg'><h1>No Character Loaded</h1></div></div>";
					}
				?>
				<!-- Special Equips (Top Mid/Right) -->
				<div id="special-info">
					<div class="equipped-misc">
						<?php 
							if ($player_profile) { 
								// Pact Section
								echo '<div class="item-slot" id="item-pact">';
									echo display_pact($player_profile);
								echo '</div>';
								// Insignia Section
								echo '<div class="item-slot" id="item-insignia">';
									echo display_insignia($player_profile);
								echo '</div>';
								// Tarot Section
								echo '<div class="item-slot" id="tarot-container">';
									echo display_tarot($tarot_card);
								echo '</div>';
								echo '<div id="tarot-card">';
									echo display_card_img($tarot_card);
								echo '</div>';
							}
						?>
					</div>    
				</div>		
			</div>
			<!-- Equipment (Bottom) -->
			<div class="bottom-container">
				<div class="equipped-items">
					<?php 
					if ($player_profile) { 
						$item_types = ['W', 'A', 'V', 'Y', 'R', 'G', 'C'];
						foreach ($item_types as $type) {
							echo display_equipment($type, $equipped_items, $equipped_gems);
						}
					} 
					?>
				</div>
			</div>
        </div>
    </main>
	<script>
		const applicationSections = document.querySelectorAll('.detail-section.element-section');
		const sideDetailLists = document.querySelectorAll('.side-detail-list');
		let hoverEventListeners = [];
		
		function resetSideDetails() {
			sideDetailLists.forEach(list => list.classList.remove('active'));
			if (sideDetailLists.length > 0) {
				sideDetailLists[0].classList.add('active');
			}
		}

		applicationSections.forEach((section) => {
			const sectionId = section.id.replace('section-', 'side-detail-');
			section.addEventListener('mouseenter', () => {
				resetSideDetails();
				const targetDetail = document.getElementById(sectionId);
				if (targetDetail) {
					targetDetail.classList.add('active');
				}
			});
		});
		
		function refreshPlayerData() {
			location.reload();
		}
		
		function toggleItem(type) {
			const itemSlot = document.getElementById('item-' + type);
			const gemSlot = document.getElementById('gem-' + type);
			if (itemSlot.classList.contains('display-off')) {
				itemSlot.classList.remove('display-off');
				gemSlot.classList.add('display-off');
			} else {
				itemSlot.classList.add('display-off');
				gemSlot.classList.remove('display-off');
			}
		}

		function checkImageExists(url, callback) {
			const img = new Image();
			img.onload = () => callback(true);
			img.onerror = () => callback(false);
			img.src = url;
		}
		
		const sectionContent = {
			0: `<?php echo $player_main_html; ?>`,
			1: `<?php echo $element_stats_html; ?>`,
			2: `<?php echo $defence_stats_html; ?>`,
			3: `<?php echo $details_stats_html; ?>`,
			4: `<?php echo $misc_stats_html; ?>`
		};

		function handleButtonClick(index) {
			removeHoverLogic();
			const buttons = document.querySelectorAll('#char-nav-buttons .char-nav-button');
			buttons.forEach((button, i) => {
				if (i < buttons.length - 1) {
					button.classList.remove('current-button');
					button.onclick = () => handleButtonClick(i);
				}
			});
			buttons[index].classList.add('current-button');
			buttons[index].onclick = null;
			document.getElementById('character-section-visible').innerHTML = sectionContent[index];
			if (index == 3) {
				applyHoverLogic();
				const elementSections = document.querySelectorAll('.detail-section.element-section');
				const sideDetailLists = document.querySelectorAll('.side-detail-list');
				if (elementSections.length > 0 && sideDetailLists.length > 0) {
					elementSections[0].classList.add('active');
					sideDetailLists[0].classList.add('side-detail-list-active');
				}
			}
		}
		
		function applyHoverLogic() {
			const elementSections = document.querySelectorAll('.detail-section.element-section');
			const sideDetailLists = document.querySelectorAll('.side-detail-list');
			elementSections.forEach((section, index) => {
				section.addEventListener('mouseenter', () => {
					elementSections.forEach(sec => sec.classList.remove('active'));
					sideDetailLists.forEach(list => list.classList.remove('side-detail-list-active'));
					section.classList.add('active');
					if (sideDetailLists[index]) {
						sideDetailLists[index].classList.add('side-detail-list-active');
					}
				});
			});
		}

		
		function removeHoverLogic() {
			hoverEventListeners.forEach(({ element, handler }) => {
				element.removeEventListener('mouseover', handler);
			});
			hoverEventListeners = [];
		}
		
		var playerProfileExists = <?php echo $player_profile ? 'true' : 'false'; ?>;
		if (playerProfileExists) {
			handleButtonClick(0);
		}
	</script>
</body>
</html>
