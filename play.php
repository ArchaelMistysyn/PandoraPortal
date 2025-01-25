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
    <main id="play-main">
        <div id="left-interface">Optional Space/Banner, hideable on smaller screen</div>
        <div id="main-interface">
            <div id="primary-content"></div>
            <div id="bottom-menu">
                <?php if ($logged_in){
                    $menu = "<div id='status-id'>UID: " . htmlspecialchars($player_profile->discord_id) . "</div>";
                    $menu .= '<a href="#" class="button-green"><span>Travel</span></a>';
                    $menu .= '<a href="#" class="button-amethyst"><span>Quest</span></a>';
                    $menu .= '<a href="#" class="button-ruby"><span>Battle</span></a>';
                    $menu .= '<a href="#" class="button-azure"><span>Gear</span></a>';
                    $menu .= '<a href="#" class="button-gold"><span>Inventory</span></a>';
                    $menu .= '<a href="#" class="button-pink"><span>Lore</span></a>';
                    echo $menu;
                } else {
                    echo "<div id='status-error'>Login Required</div>";
                } ?>
                
            </div>
        </div>
        <div id="right-interface">Primary Select Menu</div>
    </main>
</body>
</html>