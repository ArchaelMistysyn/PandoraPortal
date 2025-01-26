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

    $logged_in = False;
    $verified_user_id = 23; /* Apply login system later. */
    $player_profile = get_player_by_id($verified_user_id);
	if ($player_profile && $player_profile->player_id != 0) {
        $logged_in = True;
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Pandora Bot</title>
	<link rel="stylesheet" href="CSS/play.css">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="play-body">
    <div id="interface-screen"></div>
    <main id="play-main">   
        <div id="left-spacer"></div>
        <div id="main-interface">
            <div id="primary-content">
                <?php if ($logged_in){
                    echo "<div id='loadscreen'></div>";
                    echo "<div id='status-id'>UID: " . htmlspecialchars($player_profile->discord_id) . "</div>";
                } ?>
            </div>
            <div id="bottom-menu">
                <?php if ($logged_in){
                    $menu = '<a href="#" class="button-green" onclick="onTravel()"><span>Travel</span></a>';
                    $menu .= '<a href="#" class="button-amethyst" onclick="onQuest()"><span>Quest</span></a>';
                    $menu .= '<a href="#" class="button-ruby" onclick="onBattle()"><span>Battle</span></a>';
                    $menu .= '<a href="#" class="button-azure" onclick="onGear()"><span>Gear</span></a>';
                    $menu .= '<a href="#" class="button-gold" onclick="onInventory()"><span>Inventory</span></a>';
                    $menu .= '<a href="#" class="button-pink" onclick="onLore()"><span>Lore</span></a>';
                    echo $menu;
                } else {
                    echo "<div id='status-error'>Login Required</div>";
                } ?>
                
            </div>
        </div>
        <div id="right-spacer"></div>
    </main>
    <script src="./scripts/play_buttons.js"></script>
</body>
</html>