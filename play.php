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
    // Environment Variables
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
    
    // Check if session exists
    if (isset($_SESSION['player_id'])) {
        $player_profile = get_player_by_id($_SESSION['player_id']);
        $logged_in = ($player_profile !== null);
    } 
    elseif (isset($_COOKIE['login_key'], $_COOKIE['discord_id'])) {
        $player_profile = authenticate_user($_COOKIE['discord_id'], $_COOKIE['login_key'], $SECRET_KEY);
        $logged_in = ($player_profile !== null);
        if (!$logged_in) {
            setcookie("login_key", "", time() - 3600, "/");
            setcookie("discord_id", "", time() - 3600, "/");
        }
    } 
    elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['discord_id'], $_POST['login_key'])) {
        $player_profile = authenticate_user($_POST['discord_id'], $_POST['login_key'], $SECRET_KEY, isset($_POST['remember_me']));
        $logged_in = ($player_profile !== null);
    }


    function authenticate_user($discord_id, $login_key, $secret_key, $remember_me = false) {
        $player = get_player_by_id($discord_id, "discord");
        if ($player && password_verify_key($discord_id, $login_key, $secret_key)) {
            $_SESSION['player_id'] = $player->player_id;
            if ($remember_me) {
                setcookie("login_key", $login_key, time() + (30 * 24 * 60 * 60), "/", "", true, true);
                setcookie("discord_id", $discord_id, time() + (30 * 24 * 60 * 60), "/", "", true, true);
            }
            return $player;
        }
        return null;
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
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Crafting" onclick="onInventory(\'Crafting\')">Crafting</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Fae Cores" onclick="onInventory(\'Fae Cores\')">Fae Cores</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Materials" onclick="onInventory(\'Materials\')">Materials</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Unprocessed" onclick="onInventory(\'Unprocessed\')">Unprocessed</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Essences" onclick="onInventory(\'Essences\')">Essences</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Summoning" onclick="onInventory(\'Summoning\')">Summoning</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Gemstone" onclick="onInventory(\'Gemstone\')">Gemstone</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Fish" onclick="onInventory(\'Fish\')">Fish</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Misc" onclick="onInventory(\'Misc\')">Misc</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Ultra Rare" onclick="onInventory(\'Ultra Rare\')">Ultra Rare</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="" onclick="onInventory()">Show All</button>';
        $inventoryContainerHTML .= '</div>';
        $inventoryContainerHTML .= '<div id="inventory-screen"></div>';
    $inventoryContainerHTML .= '</div>';

    // Interfaces - Gear Container
    $gearContainerHTML = '<div id="gear-container">';
        $gearContainerHTML .= '<div id="gear-menu">';
            $gearContainerHTML .= '<button class="sort-button" data-value="Weapon" onclick="onGear(\'Weapon\')">Weapon</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Armour" onclick="onGear(\'Armour\')">Armour</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Greaves" onclick="onGear(\'Greaves\')">Greaves</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Amulet" onclick="onGear(\'Amulet\')">Amulet</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Wings" onclick="onGear(\'Wings\')">Wings</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Crest" onclick="onGear(\'Crest\')">Crest</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Ring" onclick="onGear(\'Ring\')">Ring</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Gem" onclick="onGear(\'Gem\')">Gem</button>';
        $gearContainerHTML .= '</div>';
        $gearContainerHTML .= '<div id="gear-screen-container">';
            $gearContainerHTML .= '<div id="gear-screen"></div>';
            $gearContainerHTML .= '<div id="equipped-gear"></div>';
        $gearContainerHTML .= '</div>';
    $gearContainerHTML .= '</div>';


    // Interfaces - Forge Container
    $forgeContainerHTML = '<div id="forge-container">';
        $forgeContainerHTML .= '<div id="gear-menu">';
            $forgeContainerHTML .= '<button class="sort-button" data-value="W" onclick="onForge(\'W\')">Weapon</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="A" onclick="onForge(\'A\')">Armour</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="V" onclick="onForge(\'V\')">Greaves</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="Y" onclick="onForge(\'Y\')">Amulet</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="G" onclick="onForge(\'G\')">Wings</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="C" onclick="onForge(\'C\')">Crest</button>';
        $forgeContainerHTML .= '</div>';
        $forgeContainerHTML .= '<div id="forge-screen-container">';
            $forgeContainerHTML .= '<div id="forge-item-screen"></div>';
            $forgeContainerHTML .= '<div id="forge-menu"></div>';
        $forgeContainerHTML .= '</div>';
    $forgeContainerHTML .= '</div>';

    // Interfaces - Login
    $login_form = '<div id="login-container">';
        $login_form .= '<div id="login-header">Login Required</div>';
        $login_form .= '<ol id="login-instructions">';
            $login_form .= '<li>Join the discord.</li>';
            $login_form .= '<li>/Register with the bot.</li>';
            $login_form .= '<li>Direct Message the bot "login" to get your id and key.</li>';
            $login_form .= '<li>Directly Messaging "reset" to the bot will give you a new key.</li>';
        $login_form .= '</ol>';
        $login_form .= '<form id="login-form" method="POST">';
            $login_form .= '<label for="discord_id">Discord ID:</label>';
            $login_form .= '<input type="text" name="discord_id" required>';
            $login_form .= '<label for="login_key">Login Key:</label>';
            $login_form .= '<input type="password" name="login_key" required>';
            $login_form .= '<label><input type="checkbox" name="remember_me"> Remember Me</label>';
            $login_form .= '<button type="submit">Login</button>';
        $login_form .= '</form>';
    $login_form .= '</div>';

    // Interfaces - Battle Container
    $battleContainerHTML = '<div id="battle-container">';
        $battleContainerHTML .= '<div id="battle-menu">';
            $battleContainerHTML .= '<div id="battle-menu-toggle" class="battle-button-red" onclick="battleToggle()">Solo Encounter</div>';
            $battleContainerHTML .= '<div id="battle-slider-container">';
                $battleContainerHTML .= '<label for="magnitude-slider" class="slider-label">Magnitude: <span id="magnitude-value">0</span></label>';
                $battleContainerHTML .= '<input type="range" id="magnitude-slider" min="0" max="10" value="0" step="1" oninput="document.getElementById(\'magnitude-value\').innerText = this.value">';
            $battleContainerHTML .= '</div>';
            // Solo Menu Buttons
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="0" data-type="Any">Random</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="0" data-type="Fortress">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="1" data-type="Dragon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="3" data-type="Demon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="5" data-type="Paragon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="7" data-type="Summon1">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="8" data-type="Summon2">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="9" data-type="Arbiter">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-quest="48" data-type="Summon3">Locked</div>';
            // Special Modes Buttons
            // $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-echelon="1" data-type="Arena">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="49" data-type="Palace1">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="50" data-type="Palace2">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-level="200" data-type="Palace3">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="36" data-type="Gauntlet">Locked</div>';
            // $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-echelon="5" data-type="Ruler">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
        $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '<div id="battle-screen">';
            $battleContainerHTML .= '<div id="battle-screen-bg"></div>';
            $battleContainerHTML .= '<div id="action-box">';
                $battleContainerHTML .= '<div id="action-box-name"></div><div id="action-box-value"></div><div id="action-box-image"></div>';
                $battleContainerHTML .= '<div id="action-box-menu"></div>';
            $battleContainerHTML .= '</div>';
            $battleContainerHTML .= '<div id="battle-detail-box">';
                $battleContainerHTML .= '<div id="log-boss-header"><span id="log-boss-name" class="highlight-text"></span><span id="log-boss-lvl"></span></div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-top">';
                    $battleContainerHTML .= '<div id="log-boss-hp"></div>';
                    $battleContainerHTML .= '<div id="log-boss-details"></div>';
                    $battleContainerHTML .= '<div id="log-boss-status"></div>';
                    $battleContainerHTML .= '<div id="weakness-tag"><u>Weakness Types</u></div>';
                    $battleContainerHTML .= '<div id="log-boss-weakness"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-mid">';
                    $battleContainerHTML .= '<div id="log-cycles"></div>';
                    $battleContainerHTML .= '<div id="log-dps"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-player-section">';
                    $battleContainerHTML .= '<div id="log-player-hp"></div>';
                    $battleContainerHTML .= '<div id="log-player-recovery"></div>';
                    $battleContainerHTML .= '<div id="log-player-status"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-actions-section"></div>';
            $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '<div id="battle-cover"></div>';
    $battleContainerHTML .= '</div>';

    // Interfaces - Lore Container
    $loreContainerHTML = '<div id="lore-container">';
    $loreContainerHTML .= '<div id="lore-menu">';
        $loreContainerHTML .= '<button id="lore-menu-toggle" class="lore-button-red" onclick="loreToggle()">Hide</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story1" onclick="onLore(\'story1\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story2" onclick="onLore(\'story2\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story3" onclick="onLore(\'story3\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story4" onclick="onLore(\'story4\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel1" onclick="onLore(\'prequel1\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel2" onclick="onLore(\'prequel2\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel3" onclick="onLore(\'prequel3\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel4" onclick="onLore(\'prequel4\')">Locked</button>';
    $loreContainerHTML .= '</div>';
    $loreContainerHTML .= '<div id="lore-screen"></div>';
    $loreContainerHTML .= '</div>';

    // Default Containers
    $containersHTML = $inventoryContainerHTML . $gearContainerHTML . $forgeContainerHTML . $loreContainerHTML . $battleContainerHTML;

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
        <div id="main-interface">
            <div id="primary-content">
                <?php if ($logged_in){
                    echo "<div id='loadscreen'></div>";
                    echo "<div id='status-id'>" . $player_profile->discord_id . "<a href='./bot_php/logout.php' class='logout-button'> X</a></div>";
                    echo $containersHTML;
                } else {
                    echo $login_form;
                } ?>
            </div>
            <div id="bottom-menu">
                <?php if ($logged_in){
                    $menu = '<a href="#" class="button-green" onclick="onForge()"><span>Travel</span></a>'; // Temporarily skip to forge.
                    $menu .= '<a href="#" class="button-amethyst" onclick="onQuest()"><span>Quest</span></a>';
                    $menu .= '<a href="#" class="button-ruby" onclick="onBattle()"><span>Battle</span></a>';
                    $menu .= '<a href="#" class="button-azure" onclick="onGear()"><span>Gear</span></a>';
                    $menu .= '<a href="#" class="button-gold" onclick="onInventory()"><span>Inventory</span></a>';
                    $menu .= '<a href="#" class="button-pink" onclick="onLore()"><span>Lore</span></a>';
                    echo $menu;
                } ?>
                
            </div>
        </div>
    </main>
    <script>
        let itemData = {};
        document.addEventListener('DOMContentLoaded', () => {
            fetch('./bot_php/itemData.json')
                .then(response => response.json())
                .then(data => {
                    itemData = data;
                })
                .catch(error => console.error('Failed to load item data:', error));
        });
    </script>
    <script src="./scripts/sharedmethods.js"></script>
    <script src="./scripts/inventory_button.js"></script>
    <script src="./scripts/gear_button.js"></script>
    <script src="./scripts/forge.js"></script>
    <script src="./scripts/battle.js"></script>
    <script src="./scripts/lore.js"></script>
    <script src="./scripts/play_buttons.js"></script>
</body>
</html>