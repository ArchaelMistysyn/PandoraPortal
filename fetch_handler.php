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
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null;
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
    case "displaygear":
        $response = handle_gear($verified_player_id);
        break;
    case "showGearItem":
        $response = create_gear_lightbox($item_id);
        break;
    default:
        $response["message"] = "Invalid action";
}
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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

function handle_gear($player_id) {
    $player_profile = get_player_by_id($player_id);
    $gearData = get_gear_by_player_id($player_id);
    if (!$gearData["success"]) {
        return $gearData;
    }
    $response = [
        "success" => true,
        "items" => []
    ];
    foreach ($gearData["items"] as $gear) {
        $response["items"][] = [
            "item_id" => $gear->item_id,
            "name" => $gear->item_name,
            "tier" => $gear->item_tier,
            "quality" => $gear->item_quality_tier,
            "base_stat" => $gear->item_base_stat,
            "bonus_stat" => $gear->item_bonus_stat,
            "min_dmg" => $gear->item_damage_min,
            "max_dmg" => $gear->item_damage_max,
            "icon" => $gear->get_gear_thumbnail(),
            "item_type" => $gear->item_type,
            "equipped" => in_array((int) $gear->item_id, $player_profile->player_equipped, true),
            "num_sockets" => $gear->item_num_sockets,
            "inlaid_id" => $gear->item_inlaid_gem_id
        ];
    }
    return $response;
}

function create_gear_lightbox($item_id) {
    $item = read_custom_item($item_id);
    if (!$item) {
        return ["success" => false, "message" => "Item not found"];
    }
    $is_gem = strpos($item->item_type, "D") !== false;
    $item_html = "<div class='item-slot'>";
    $item_html .= $item->display_item($is_gem, "basic");
    $item_html .= "</div>";
    return ["success" => true, "html" => $item_html];
}

function get_gear_by_player_id($player_id, $specific_id = null) {
    $query = "SELECT * FROM CustomInventory WHERE player_id = " . intval($player_id);
    if ($specific_id !== null) {
        $query .= " AND item_id = " . intval($specific_id);
    }
    $gear_items = run_query($query);
    if (!$gear_items) {
        return ["success" => false, "message" => "No gear found"];
    }
    $filteredGear = [];
    foreach ($gear_items as &$gear) {
        $customItem = new CustomItem(
            $gear["player_id"],
            $gear["item_type"],
            $gear["item_tier"],
            $gear["item_base_type"]
        );
        foreach (array_intersect_key($gear, get_object_vars($customItem)) as $key => $value) {
            $customItem->$key = $value;
        }
        $customItem->item_elements = array_map('intval', explode(';', (string) $gear["item_elements"]));
        $customItem->item_roll_values = explode(';', (string) $gear["item_roll_values"]);
        $customItem->update_damage();
        $customItem->set_item_name();
        if ($specific_id !== null) {
            return ["success" => true, "item" => $customItem];
        }
        $filteredGear[] = $customItem;
    }
    return ["success" => true, "items" => $filteredGear];
}

?>
