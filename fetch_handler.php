<?php
session_start();

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

if (!isset($_SESSION['player_id']) || !isset($_GET['action'])) {
    echo json_encode(["success" => false, "message" => !isset($_SESSION['player_id']) ? "Unauthorized access" : "No action"]);
    exit();
}
$verified_player_id = $_SESSION['player_id'];
$action = $_GET['action'];
$response = ["success" => false];
switch ($action) {
    case "inventory":
        $response = get_inventory_by_player_id($verified_player_id);
        break;
    case "player":
        $player_profile = get_player_by_id($verified_player_id);
        $response = $player_profile
            ? ["success" => true, "player" => $player_profile]
            : ["success" => false, "message" => "Player not found"];
        break;
    default:
        $response["message"] = "Invalid action";
}
echo json_encode($response);


// Fetch Functions
function get_inventory_by_player_id($player_id) {
    global $itemData;
    $query = "SELECT item_id, item_qty FROM BasicInventory WHERE player_id = " . intval($player_id);
    $inventory = run_query($query);
    if (!$inventory) {
        return [];
    }
    foreach ($inventory as &$item) {
        $category = $itemData[$item["item_id"]]["category"];
        $item["name"] = $itemData[$item["item_id"]]["name"];
        if ($category === "Fish") {
            $filename = "Frame_Fish_{$itemData[$item["item_id"]]["tier"]}.png";
        } elseif ($category === "Essence") {
            $filename = "Frame_Essence_{$itemData[$item["item_id"]]["tier"]}.png";
        } else {
            $filename = "Frame_{$item['item_id']}.png";
        }
        $item["icon"] = "./botimages/NonGear_Icon/{$category}/{$filename}";
    }
    return ["success" => (bool) $inventory, "items" => $inventory ?: []];
}

?>
