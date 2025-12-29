<!DOCTYPE html>
<html lang="en">
<?php
	session_start();
	include_once('./bot_php/db_queries.php');
	include_once('./bot_php/player.php');
	include_once('./bot_php/shared_methods.php');
	$player_data = null;
	$player_id = null;
	$search_data = false;
	$error = '';
	$leaderboard_type = isset($_POST['leaderboard_type']) ? $_POST['leaderboard_type'] : 'dps';

	// Fetch all data for tables
	$query_leaderboard = "SELECT Leaderboard.player_id, player_dps_rank, player_damage_rank, player_dps, player_damage, 
							PlayerList.player_class, PlayerList.player_level, PlayerList.player_exp, PlayerList.player_username
							FROM Leaderboard
							JOIN PlayerList ON Leaderboard.player_id = PlayerList.player_id";
	$leaderboard_data = run_query($query_leaderboard);
	// Separate data for each table
	$dps_data = $leaderboard_data;
	usort($dps_data, fn($a, $b) => $a['player_dps_rank'] <=> $b['player_dps_rank']);
	$damage_data = $leaderboard_data;
	usort($damage_data, fn($a, $b) => $a['player_damage_rank'] <=> $b['player_damage_rank']);
	$level_data = $leaderboard_data;
	usort($level_data, fn($a, $b) => $b['player_level'] <=> $a['player_level']);

	// Table Configeration
	$rank_data = [
		1 => ['class' => 'ultimate', 'icon' => 'rank_1.png', 'alt' => 'Rank 1 Icon'],
		2 => ['class' => 'uber', 'icon' => 'rank_2.png', 'alt' => 'Rank 2 Icon'],
		3 => ['class' => 'ultra', 'icon' => 'rank_3.png', 'alt' => 'Rank 3 Icon']
	];
	$table_config = [
		'static_fields' => [
			'rank' => fn($data, $icon_html) => "<td class='rank-cell icon-cell-big'><div>$icon_html</div></td>",
			'player_username' => fn($data) => "<td>{$data['player_username']}</td>",
			'player_id' => fn($data) => "<td>{$data['player_id']}</td>",
			'player_level' => fn($data) => "<td>{$data['player_level']}</td>",
			'player_class' => fn($data) => "<td class='icon-cell-medium'><div><img src='./gallery/Icons/Classes/{$data['player_class']}.webp'/></div></td>"],
		'dynamic_fields' => [
			'DPS' => [
				'scaled_value' => fn($data) => "<td>" . number_conversion($data['player_dps']) . "</td>",
				'raw_value' => fn($data) => "<td>" . number_format($data['player_dps']) . "</td>"],
			'Damage' => [
				'scaled_value' => fn($data) => "<td>" . number_conversion($data['player_damage']) . "</td>",
				'raw_value' => fn($data) => "<td>" . number_format($data['player_damage']) . "</td>"],
			'Level' => [
				'current_exp' => fn($data) => "<td>" . number_format($data['player_exp']) . "</td>",
				'max_exp' => fn($data) => "<td>" . number_format(get_max_exp($data['player_level'])) . "</td>"]],
		'end_fields' => [
        	'action' => fn($data) => "<td class='button-cell'><div><form method='get' action='characters.php'>
                                    		<input type='hidden' name='search_input' value='{$data['player_id']}'>
                                    		<button type='submit' class='input-button'>View</button>
                                  		</form></div></td>"]
	];
	$field_labels = [
		'rank' => 'Ranking', 'player_username' => 'Username', 'player_id' => 'Player ID',
		'player_level' => 'Level', 'player_class' => 'Class', 'action' => 'Char Page',
		'scaled_value' => 'Scaled Value', 'raw_value' => 'Raw Value',
		'current_exp' => 'Current EXP', 'max_exp' => 'Max EXP',
	];	

	function display_leaderboard_table($leaderboard_data, $type_label, $rank_column, $player_id, $is_active = false) {
		global $table_config, $field_labels, $rank_data;
		$static_fields = $table_config['static_fields'];
		$dynamic_fields = $table_config['dynamic_fields'][$type_label] ?? [];
		$end_fields = $table_config['end_fields'] ?? [];
		$fields = array_merge($static_fields, $dynamic_fields, $end_fields);
		$active_class = $is_active ? ' active' : '';
	
		echo "<div class='table-box rank-table$active_class' id='table-$type_label'>";
		echo "<h3 class='highlight-text'>$type_label Leaderboard</h3>";
		echo "<table class='style-table'><thead class='style-header'><tr>";
		foreach (array_keys($fields) as $field) {
			$label = $field_labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
			echo "<th><div>$label</div></th>";
		}
		echo "</tr></thead><tbody>";
		if (!empty($leaderboard_data)) {
			$rank_counter = 1;
			foreach ($leaderboard_data as $data) {
				$highlight_class = isset($data['player_id']) && $data['player_id'] == $player_id ? 'highlight-row' : '';
				$rank = ($rank_column && isset($data[$rank_column])) ? $data[$rank_column] : $rank_counter;
				$rank_class = isset($rank_data[$rank]['class']) ? $rank_data[$rank]['class'] : '';
				$icon_html = isset($rank_data[$rank])
					? "<img src='./images/Icons/{$rank_data[$rank]['icon']}' alt='{$rank_data[$rank]['alt']}' class='rank-icon-$rank'/>" : $rank;
				$hidden_class = $rank_counter > 10 ? 'hidden-tag' : '';
				$playerUsernameLower = strtolower($data['player_username']);
				echo "<tr class='$highlight_class $rank_class $hidden_class' data-player-id='{$data['player_id']}' data-player-username='{$playerUsernameLower}'>";
				foreach ($fields as $field => $render) {
					echo $render($data, $icon_html);
				}
				echo "</tr>";
				$rank_counter++;
			}
		} else {
			echo "<tr><td colspan='" . count($fields) . "'>No data available</td></tr>";
		}
		echo "</tbody></table></div>";
	}	
	
	function render_table_cell($field, $data, $icon_html, $fields) {
		return $fields[$field]['render']($data, $icon_html ?? null);
	}	

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Leaderboards</title>
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/rankingCSS.css">
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/rankingCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <header id="header"></header>
    <main id="ranking-main">
		<div id="ranking-main-container">
			<div id="tab-menu">
				<input type="text" id="filter-input" placeholder="Filter Content" oninput="filterItems()">
				<div class="tab-menu" id="tabMenu"></div>
			</div>
			<div id="leaderboard-container">
			<?php
				display_leaderboard_table($dps_data, 'DPS', 'player_dps_rank', 'player_dps', $player_id, true);
				display_leaderboard_table($damage_data, 'Damage', 'player_damage_rank', 'player_damage', $player_id);
				display_leaderboard_table($level_data, 'Level', '', '', $player_id);
			?>
			</div>
		</div>
    </main>
	<script src="scripts/header.js"></script>
	<script src="scripts/buttonMenu.js"></script>
	<script src="scripts/screensizeWarning.js"></script>
</body>
</html>
