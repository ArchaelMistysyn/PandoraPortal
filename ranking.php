<!DOCTYPE html>
<html lang="en">
<?php
	session_start();
	include_once('./bot_php/db_queries.php');
	include_once('./bot_php/player.php');
	$player_data = null;
	$player_id = null;
	$search_data = false;
	$error = '';
	$leaderboard_type = 'dps';
	// Pull selected leaderboard
	$leaderboard_type = isset($_POST['leaderboard_type']) && $_POST['leaderboard_type'] === 'damage' ? 'damage' : 'dps';
	$rank_column = $leaderboard_type === 'dps' ? 'player_dps_rank' : 'player_damage_rank';
	$value_column = $leaderboard_type === 'dps' ? 'player_dps' : 'player_damage';
	$query_leaderboard = "SELECT Leaderboard.player_id, $rank_column, $value_column, 
							PlayerList.player_class, PlayerList.player_level, PlayerList.player_username 
							FROM Leaderboard 
							JOIN PlayerList ON Leaderboard.player_id = PlayerList.player_id 
							ORDER BY $rank_column ASC";
	$leaderboard_data = run_query($query_leaderboard);
	// Handle player input
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$search_input = trim($_POST['search_input']);
		if (!empty($search_input)) {
			$player_profile = get_player_by_id($search_input);
			if ($player_profile->player_id != 0) {
				$search_data = true;
				$player_data = $player_profile;
				$player_id = $player_profile->player_id;
				$top_10_data = array_slice($leaderboard_data, 0, 10);
				$user_in_top_10 = array_search($player_id, array_column($top_10_data, 'player_id')) !== false;
				if (!$user_in_top_10) {
					$leaderboard_data = array_filter($leaderboard_data, function($entry) use ($player_id) {
						return $entry['player_id'] == $player_id;
					});
				}
			} else {
				$error = "Player not found.";
			}
		}
	}
	// Scale numeric values
	function number_conversion($input_number) {
		$labels = ['', 'K', 'M', 'B', 'T', 'Q', 'Qt', 'Z', 'Z+', 'Z++', 'Z+++', 'ZZ', 'ZZ+', 'ZZ++', 'ZZ+++', 'ZZZ', 'ZZZ+', 'ZZZ++', 'ZZZ+++'];
		if ($input_number < 1000) {
			return (string)$input_number;
		}
		$num_digits = strlen((string)(int)$input_number);
		$idx = (int)(($num_digits - 1) / 3);
		$scaled_number = $input_number / pow(10, 3 * $idx);
		$truncated_scaled_number = floor($scaled_number * 100) / 100.0;
		$number_msg = ($scaled_number == (int)$scaled_number) ? (string)(int)$scaled_number : number_format($truncated_scaled_number, 2);
		if ($idx != 0) {
			$number_msg .= ' ' . $labels[$idx];
		}
		return $number_msg;
	}

	function display_leaderboard_table($leaderboard_data, $leaderboard_type, $rank_column, $value_column, $player_id) {
		$type_label = $leaderboard_type == 'dps' ? 'DPS' : 'Damage';
		echo "<div class='table-box rank-table'><h3 class='highlight-text'>$type_label Leaderboard</h3>";
		echo "<table class='style-table'><thead class='style-header'><tr>";
		echo "<th><div>Ranking</div></th>";
		echo "<th><div>Username</div></th>";
		echo "<th><div>Player ID</div></th>";
		echo "<th><div>Level</div></th>";
		echo "<th><div>Class</div></th>";
		echo "<th><div>Scaled $type_label</div></th>";
		echo "<th><div>Raw $type_label</div></th>";
		echo "<th><div>Char Page</div></th>";
		echo "</tr></thead><tbody>";
		$count = 0;
		foreach ($leaderboard_data as $data) {
			$player_rank = $data[$rank_column];
			if ($count >= 10 && $data['player_id'] != $player_id) break;
			$highlight_class = ($data['player_id'] == $player_id) ? 'highlight-row' : '';
			$formatted_value = number_format($data[$value_column]);
			$scaled_value = number_conversion($data[$value_column]);
			$rank_data = [
				1 => ['class' => 'ultimate', 'icon' => 'rank_1.png', 'alt' => 'Rank 1 Icon'],
				2 => ['class' => 'uber', 'icon' => 'rank_2.png', 'alt' => 'Rank 2 Icon'],
				3 => ['class' => 'ultra', 'icon' => 'rank_3.png', 'alt' => 'Rank 3 Icon']
			];
			if (isset($rank_data[$player_rank])) {
				$rank_info = $rank_data[$player_rank];
				echo "<tr class='{$highlight_class} {$rank_info['class']}'>";
				echo "<td class='icon-cell-big'><div><img src='./images/Icons/{$rank_info['icon']}' class='rank-icon-{$player_rank}' alt='{$rank_info['alt']}'/></div></td>";
			} else {
				echo "<tr class='{$highlight_class}'>";
				echo "<td>{$player_rank}</td>";
			}			
			echo "<td>{$data['player_username']}</td>";
			echo "<td>{$data['player_id']}</td>";
			echo "<td>{$data['player_level']}</td>";
			echo "<td class='icon-cell-big'><div><img src='./gallery/Icons/Classes/{$data['player_class']}.webp'/></div></td>";
			echo "<td>$scaled_value</td>";
			echo "<td>$formatted_value</td>";
			echo "<td class='button-cell'>
				<div><form method='post' action='characters.php'>
					<input type='hidden' name='search_input' value='{$data['player_id']}'>
					<button type='submit' class='input-button'>View</button>
				</form></div>
			  </td>";
			echo "</tr>";
			$count++;
		}
		echo "</tbody></table></div>";
	}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Leaderboards</title>
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/rankingCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <header id="header"></header>
    <main>
		<div id="leaderboard-container">
			<?php display_leaderboard_table($leaderboard_data, $leaderboard_type, $rank_column, $value_column, $player_id); ?>
		</div>
    </main>
	<script src="scripts/header.js"></script>
</body>
</html>
