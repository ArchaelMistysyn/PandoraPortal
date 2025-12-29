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
    include_once('./bot_php/containers.php');
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

    $lore = [];
    $lore["prequel1"] = file_get_contents("./Lore/PrequelAct1.html");
    $lore["prequel2"] = file_get_contents("./Lore/PrequelAct2.html");
    $lore["prequel3"] = file_get_contents("./Lore/PrequelAct3.html");
    $lore["prequel4"] = file_get_contents("./Lore/PrequelAct4.html");
    $lore["story1"] = file_get_contents("./Lore/StoryAct1.html");
    $lore["story2"] = file_get_contents("./Lore/StoryAct2.html");
    $lore["story3"] = file_get_contents("./Lore/StoryAct3.html");

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
    <div id="banner-screen"></div>
    <main id="play-main">   
        <div id="main-interface">
            <div id="primary-content">
                <?php if ($logged_in){
                    echo "<div id='loadscreen'></div>";
                    echo "<div id='status-message'>Pandora Web Play &lpar;Alpha&rpar;</div>";
                    echo "<div id='status-id'>" . $player_profile->discord_id . "<a href='./bot_php/logout.php' class='logout-button'> X</a></div>";
                    echo $containersHTML;
                } else {
                    echo $login_form;
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
                } ?>
                
            </div>
        </div>
    </main>
    <script>
        let itemData = {};
        let tarotData = {};
        const storyData = {
            story1: <?php echo json_encode($lore["story1"]); ?>,
            story2: <?php echo json_encode($lore["story2"]); ?>,
            story3: <?php echo json_encode($lore["story3"]); ?>,
            prequel1: <?php echo json_encode($lore["prequel1"]); ?>,
            prequel2: <?php echo json_encode($lore["prequel2"]); ?>,
            prequel3: <?php echo json_encode($lore["prequel3"]); ?>,
            prequel4: <?php echo json_encode($lore["prequel4"]); ?>
        };
        document.addEventListener('DOMContentLoaded', () => {
            fetch('./bot_php/itemData.json')
                .then(response => response.json())
                .then(data => { itemData = data; })
                .catch(error => console.error('Failed to load item data:', error));
            fetch('./bot_php/tarot.json')
                .then(response => response.json())
                .then(data => { tarotData = data; })
                .catch(error => console.error('Failed to load tarot data:', error));
        });
    </script>
    <script src="./scripts/sharedmethods.js"></script>
    <script src="./scripts/inventory_button.js"></script>
    <script src="./scripts/gear_button.js"></script>
    <script src="./scripts/forge.js"></script>
    <script src="./scripts/refine.js"></script>
    <script src="./scripts/battle.js"></script>
    <script src="./scripts/quest.js"></script>
    <script src="./scripts/travel.js"></script>
    <script src="./scripts/lore.js"></script>
    <script src="./scripts/shop.js"></script>
    <script src="./scripts/map.js"></script>
    <script src="./scripts/play_buttons.js"></script>
</body>
</html>