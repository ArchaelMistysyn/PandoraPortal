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

    $logged_in = false;
    $player_profile = null;
    if (isset($_SESSION['player_id'])) {
        $player_profile = get_player_by_id($_SESSION['player_id']);
        if ($player_profile) {
            $logged_in = true;
        }
    } elseif (isset($_COOKIE['player_id'])) {
        $player_profile = get_player_by_id($_COOKIE['player_id']);
        if ($player_profile) {
            $logged_in = true;
            $_SESSION['player_id'] = $player_profile->player_id;
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['discord_id'], $_POST['login_key'])) {
        $webEnvFile = "/home/kylep910/PandoraPortalEnv/.env";
        $localEnvFile = "./nonpublic/.env";
        $envFile = file_exists($localEnvFile) ? $localEnvFile : $webEnvFile;
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                putenv($line);
            }
        }
        $SECRET_KEY = getenv('SECRET_KEY');
        $discord_id = $_POST['discord_id'];
        $login_key = $_POST['login_key'];
        $player_profile = get_player_by_id($discord_id, "discord");
        if ($player_profile && password_verify_key($discord_id, $login_key, $SECRET_KEY)) {
            $_SESSION['player_id'] = $player_profile->player_id;
            $logged_in = true;
            if (isset($_POST['remember_me'])) {
                setcookie("player_id", $player_profile->player_id, time() + (30 * 24 * 60 * 60), "/");
            }
        }
    }

    function password_verify_key($discord_id, $login_key, $secret_key) {
        $query = "SELECT encrypted_key FROM LoginKeys WHERE discord_id = '$discord_id'";
        $result = run_query($query);
        if (empty($result)) {
            return false;
        }
        $encrypted_b64 = $result[0]['encrypted_key'];
        $encrypted_data = base64_decode($encrypted_b64);
        $iv = substr($encrypted_data, 0, 16);
        $cipher_text = substr($encrypted_data, 16);
        $cipher = "aes-256-cbc";
        $decrypted_key = openssl_decrypt($cipher_text, $cipher, $secret_key, OPENSSL_RAW_DATA, $iv);
        return hash_equals($decrypted_key, $login_key);
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
        $gearContainerHTML .= '<div id="gear-screen-container">';
            $gearContainerHTML .= '<div id="gear-screen"></div>';
            $gearContainerHTML .= '<div id="equipped-gear"></div>';
        $gearContainerHTML .= '</div>';
    $gearContainerHTML .= '</div>';

    // Interfaces - Login
    $login_form = '<form id="login-form" method="POST">';
        $login_form .= '<label for="discord_id">Discord ID:</label>';
        $login_form .= '<input type="text" name="discord_id" required>';
        $login_form .= '<label for="login_key">Login Key:</label>';
        $login_form .= '<input type="password" name="login_key" required>';
        $login_form .= '<label><input type="checkbox" name="remember_me"> Remember Me</label>';
        $login_form .= '<button type="submit">Login</button>';
    $login_form .= '</form>';


?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Pandora Bot</title>
	<link rel="stylesheet" href="CSS/play.css">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="play-body">
    <div id="lightbox-screen"><div id="lightbox-container"><div id="lightbox-display"></div><div id="lightbox-menu"></div></div></div>
    <div id="blocking-screen"></div>
    <div id="interface-screen"></div>
    <main id="play-main">   
        <div id="left-spacer"></div>
        <div id="main-interface">
            <div id="primary-content">
                <?php if ($logged_in){
                    echo "<div id='loadscreen'></div>";
                    echo "<div id='status-id'>" . $player_profile->discord_id . "<a href='./bot_php/logout.php' class='logout-button'> X</a></div>";
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
                    echo $login_form;
                } ?>
                
            </div>
        </div>
        <div id="right-spacer"></div>
    </main>
    <script src="./scripts/inventory_button.js"></script>
    <script src="./scripts/gear_button.js"></script>
    <script src="./scripts/play_buttons.js"></script>
</body>
</html>