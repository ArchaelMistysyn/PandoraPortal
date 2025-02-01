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
	// Web Inclusions
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
        $_SESSION['player_id'] = $verified_user_id;
        $logged_in = True;
    }

    // Interfaces - Inventory Container
    $inventoryContainerHTML = '<div id="inventory-container">';
        $inventoryContainerHTML .= '<div id="inventory-menu">';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Crafting\')">Crafting</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Fae Cores\')">Fae Cores</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Materials\')">Materials</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Unprocessed\')">Unprocessed</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Essences\')">Essences</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Summoning\')">Summoning</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Gemstone\')">Gemstone</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Fish\')">Fish</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Misc\')">Misc</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory(\'Ultra Rare\')">Ultra Rare</button>';
            $inventoryContainerHTML .= '<button class="sort-button" onclick="onInventory()">Show All</button>';
        $inventoryContainerHTML .= '</div>';
        $inventoryContainerHTML .= '<div id="inventory-screen"></div>';
    $inventoryContainerHTML .= '</div>';

    // Interfaces - Gear Container
    $gearContainerHTML = '<div id="gear-container">';
        $gearContainerHTML .= '<div id="gear-menu">';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Weapon\')">Weapon</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Armour\')">Armour</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Greaves\')">Greaves</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Amulet\')">Amulet</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Wings\')">Wings</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Crest\')">Crest</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Ring\')">Ring</button>';
            $gearContainerHTML .= '<button class="sort-button" onclick="onGear(\'Gem\')">Gem</button>';
        $gearContainerHTML .= '</div>';
        $gearContainerHTML .= '<div id="gear-screen"></div>';
        $gearContainerHTML .= '<div id="equipped-gear"></div>';
    $gearContainerHTML .= '</div>';


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
                    echo $inventoryContainerHTML;
                    echo $gearContainerHTML;
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