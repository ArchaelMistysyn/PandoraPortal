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
include_once('./bot_php/forge.php');

if (!isset($_SESSION['player_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}
$verified_player_id = $_SESSION['player_id'];
$input = json_decode(file_get_contents("php://input"), true);
$action = isset($input['action']) && $input['action'] !== 'null' ? $input['action'] : null;
$slot_type = isset($input['slot_type']) && $input['slot_type'] !== 'null' ? $input['slot_type'] : null;
$element = isset($input['element']) && $input['element'] !== 'null' ? $input['element'] : null;
$item_id = isset($input['item_id']) && $input['item_id'] !== 'null' ? $input['item_id'] : null;
if (!$action) {
    echo json_encode(["success" => false, "message" => "No action provided"]);
    exit();
}
if ($item_id !== null) {
    if (ctype_digit((string) $item_id)) {
        $item_id = (int) $item_id;
    } elseif (!isset($itemData[$item_id])) {
        echo json_encode(["success" => false, "message" => "Invalid item ID"]);
        exit();
    }
}
if ($slot_type && !isset($slot_types[$slot_type])){
    echo json_encode(["success" => false, "message" => "Invalid slot type"]);
    exit();
}
if ($element !== null && $element !== '' && (!ctype_digit((string) $element) || $element < 0 || $element > 8)) {
    echo json_encode(["success" => false, "message" => "Invalid element provided: " . $element]);
    exit();
}

$forge_actions = [
    "Fae Enchant", "Gemstone Enchant", "Reinforce Quality", "Create Socket", "Hellfire Reforge",
    "Abyssfire Reforge", "Mutate Reforge", "Attune Rolls", "Star Fusion (Add/Reroll)",
    "Radiant Fusion (Defensive)", "Chaos Fusion (All)", "Void Fusion (Damage)",
    "Wish Fusion (Penetration)", "Abyss Fusion (Curse)", "Divine Fusion (Unique)", "Salvation (Class Skill)", "Implant"
];

$response = ["success" => false];
if (in_array($action, $forge_actions)) {
    $working_item = null;
    $response = getForgeItemDetails($verified_player_id, $slot_type, $working_item);
    if ($response['success']) {
        $player_profile = get_player_by_id($verified_player_id);
        $cost = getForgeActionCost($action, $element, $working_item);
        $stock = checkUserStock($verified_player_id, array_column($cost, 'item_id'));
        forgeQualifyAndRate($response, $working_item, $action, $stock, $cost);
        $response['cost'] = $cost;
        $response['stock'] = $stock;
        if (!empty($input['execute'])) {
            $execution_result = processForgeExecution($player_profile, $working_item, $action, $element, $response);
            forgeQualifyAndRate($execution_result, $working_item, $action, $stock, $cost);
            echo json_encode($execution_result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit();
        }
        
    }
} else {
    switch ($action) {
        case "inventory":
            $response = get_inventory_by_player_id($verified_player_id);
            break;
        case "showInventoryItem":
            $response = create_inventory_lightbox($verified_player_id, $item_id);
            break;
        case "player":
            $player_profile = get_player_by_id($verified_player_id);
            $response = $player_profile
                ? ["success" => true, "player" => $player_profile]
                : ["success" => false, "message" => "Player not found"];
            break;
        case "displaygear":
            $player_profile = get_player_by_id($verified_player_id);
            $response = handle_gear($player_profile);
            break;
        case "showGearItem":
            $response = create_gear_lightbox($verified_player_id, $item_id);
            break;
        case "equipItem":
            if ($item_id == null) {
                $response = ["success" => false, "message" => "Missing item ID"];
            } else {
                $response = equip_gear($verified_player_id, $item_id);
            }
            break;
        case "inlayItem":
            $slot_type = $input['slot_type'] ?? null;
            if (!$item_id || !$slot_type) {
                $response = ["success" => false, "message" => "Missing gem ID or slot type"];
            } else {
                $response = inlay_gem($verified_player_id, $item_id, $slot_type);
            }
            break; 
        case "removeItemScrap":
            $response = process_gear_removal($verified_player_id, $item_id, 'Scrap');
            break;
        case "removeItemSell":
            $response = process_gear_removal($verified_player_id, $item_id, 'Sell');
            break;
        case "displayForge":
            if (!$slot_type) {
                $response = ["success" => false, "message" => "Missing slot type"];
            } else {
                $response = getForgeItemDetails($verified_player_id, $slot_type);
            }
            break;  
        default:
            $response["message"] = "Invalid action";
    }
}
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit();

// Fetch Functions
function get_inventory_by_player_id($player_id, $specific_item_id = null) {
    global $itemData;
    if ($specific_item_id !== null) {
        if (!isset($itemData[$specific_item_id])) {
            return ["success" => false, "message" => "Invalid item ID"];
        }
        $specific_item_id = "'" . addslashes($specific_item_id) . "'";
    }
    $query = "SELECT item_id, item_qty FROM BasicInventory WHERE player_id = " . intval($player_id);
    if ($specific_item_id !== null) {
        $query .= " AND item_id = " . $specific_item_id;
    }
    $inventory = run_query($query);
    if (!$inventory) {
        return ["success" => false, "message" => "Item not found"];
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
    return $specific_item_id !== null ? ["success" => true, "item" => $inventory[0]] : ["success" => true, "items" => $inventory];
}

function create_inventory_lightbox($player_id, $item_id) {
    global $itemData;
    if (!isset($itemData[$item_id])) {
        return ["success" => false, "message" => "Invalid item ID"];
    }
    $result = get_inventory_by_player_id($player_id, $item_id);
    if (!$result["success"]) {
        return ["success" => false, "message" => "Item not found"];
    }
    $item = $result["item"];
    $tier = $itemData[$item_id]["tier"];
    $description = $itemData[$item_id]["description"];
    $stars = generate_stars($tier);
    $item_html = "<div class='item-displaybox'>";
    $item_html .= "<img src='{$item["icon"]}' alt='{$item["name"]}' class='inventory-lightbox-icon'>";
    $item_html .= "<div class='item-name highlight-text'>{$item["name"]}</div>";
    $item_html .= "<div class='style-line'></div>";
    $item_html .= "<div class='inventory-stars'>{$stars}</div>";
    $item_html .= "<div class='style-line'></div>";
    $item_html .= "<div class='inventory-description'>{$description}</div>";
    $item_html .= "<div class='style-line'></div>";
    $item_html .= "<div>Quantity: {$item["item_qty"]}</div>";
    $item_html .= "</div>";
    $menu_html = "<div class='lightbox-menu'>";
    $menu_html .= "<button class='lightbox-button-gray' onclick='closeLightbox()'><span class='symbol-height'>✖</span> Close</button>";
    $menu_html .= "</div>";
    return ["success" => true, "html" => $item_html, "menu" => $menu_html];
}


function handle_gear($player_profile) {
    $gearData = get_gear_by_player_id($player_profile->player_id);
    if (!$gearData["success"]) {
        return $gearData;
    }
    $response = [
        "success" => true,
        "items" => array_map(function($gear) use ($player_profile) {
            return format_gear_item($gear, $player_profile);
        }, $gearData["items"])
    ];
    return $response;
}

function format_gear_item($gear, $player_profile) {
    return [
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

function create_gear_lightbox($player_id, $item_id) {
    $player_profile = get_player_by_id($player_id);
    $item = read_custom_item($item_id);
    if (!$item) {
        return ["success" => false, "message" => "Item not found"];
    }
    return generate_gear_content($player_profile, $item);
}

function generate_gear_content($player_profile, $item, $include_menu = true) {
    global $slot_types;
    $is_gem = strpos($item->item_type, "D") !== false;
    $item_html = "<div class='item-slot'>";
    $item_html .= $item->display_item($is_gem, "basic");
    $item_html .= "</div>";
    $menu_html = "";
    if ($include_menu && !in_array($item->item_id, $player_profile->player_equipped, true)) {
        if (!$is_gem) {
            $menu_html .= "<button class='lightbox-button-green' onclick='EquipItem(\"{$item->item_id}\")'>Equip</button>";
        } else {
            $gear_options = "";
            $gearData = get_gear_by_player_id($player_profile->player_id);
            $equipped_gear = array_filter($gearData["items"], function ($gear) use ($player_profile) {
                return in_array((int) $gear->item_id, $player_profile->player_equipped, true);
            });
            foreach ($equipped_gear as $gear) {
                if ($gear->item_num_sockets > 0) {
                    $slot_type = $gear->item_type;
                    $gear_options .= "<button class='lightbox-button-red' value='{$slot_type}' onclick='InlayItem(\"{$item->item_id}\", \"{$slot_type}\")'>{$slot_types[$slot_type]}</button>";
                }
            }
            if ($gear_options) {
                $menu_html .= "<button class='lightbox-button-green' onclick='toggleInlayMenu()'>Inlay</button>";
                $menu_html .= "<div id='inlay-gear-select' class='hideItem'>{$gear_options}</div>";
            }
        }
        $menu_html .= "<button class='lightbox-button-blue' onclick='handleGearAction({$item->item_id}, \"Scrap\")'>Scrap</button>";
        $menu_html .= "<button class='lightbox-button-blue' onclick='handleGearAction({$item->item_id}, \"Sell\")'>Sell</button>";
    }
    $menu_html .= "<button class='lightbox-button-gray' onclick='closeLightbox()'><span class='symbol-height'>✖</span> Close</button>";
    return ["success" => true, "html" => $item_html, "menu" => $menu_html ];
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

function inlay_gem($player_id, $gem_id, $slot_type) {
    global $slot_types;
    $player = get_player_by_id($player_id);
    $gearData = get_gear_by_player_id($player_id);
    $slot_index = array_search($slot_type, array_keys($slot_types), true);
    if (!$gearData["success"] || !isset($player->player_equipped[$slot_index]) || $player->player_equipped[$slot_index] == 0) {
        return ["success" => false, "message" => "Failed to inlay gem"];
    }
    $gem = array_filter($gearData["items"], fn($gear) => $gear->item_id == $gem_id);
    if (empty($gem)) {
        return ["success" => false, "message" => "Gem not found in inventory"];
    }
    $item_id = (int) $player->player_equipped[$slot_index];
    $update_query = "UPDATE CustomInventory SET item_inlaid_gem_id = CASE ";
    $update_query .= "WHEN item_id = " . intval($item_id) . " AND item_num_sockets > 0 THEN " . intval($gem_id) . " ";
    $update_query .= "WHEN item_inlaid_gem_id = " . intval($gem_id) . " THEN 0 ";
    $update_query .= "ELSE item_inlaid_gem_id END ";
    $update_query .= "WHERE player_id = " . intval($player_id); 
    run_query($update_query, false);
    return ["success" => true, "message" => "Gem successfully inlaid"];
}

function equip_gear($player_id, $item_id) {
    global $item_loc_dict;
    $item = read_custom_item($item_id);
    $player = get_player_by_id($player_id);
    if (!$item || !$player || $item->player_id !== $player->player_id) {
        return ["success" => false, "message" => "Item or Player not found."];
    }
    $player->player_equipped[$item_loc_dict[$item->item_type]] = $item_id;
    $player->update_player_data();
    return ["success" => true, "message" => "Item equipped: " . $item_id];
}

function process_gear_removal($player_id, $item_id, $action) {
    $player = get_player_by_id($player_id);
    $item = read_custom_item($item_id);
    if (!$item || !$player || $item->player_id !== $player->player_id) {
        return ["success" => false, "reload" => true, "message" => "Item or Player error."];
    }
    $equip_index = array_search($item_id, $player->player_equipped, true);
    if ($equip_index !== false) {
        $player->player_equipped[$equip_index] = 0;
    }
    $query = "DELETE FROM CustomInventory WHERE player_id = " . intval($player_id) . " AND item_id = " . intval($item_id);
    run_query($query, false);
    if ($action === "Scrap") {
        $reward_message = assign_scrap_reward($player_id, $item);
    } else if ($action === "Sell"){
        $reward_message = assign_sell_reward($player, $item);
    }
    $player->update_player_data();
    return ["success" => true, "message" => "Item $action completed. " . $reward_message];
}

function assign_scrap_reward($player_id, $item) {
    $scrap_qty = $item->item_tier;
    $query = "INSERT INTO BasicInventory (player_id, item_id, item_qty)";
    $query .= " VALUES ($player_id, 'Scrap', $scrap_qty)";
    $query .= " ON DUPLICATE KEY UPDATE item_qty = item_qty + $scrap_qty";
    run_query($query, false);
    return "Received $scrap_qty Scrap.";
}

function assign_sell_reward($player, $item) {
    $sell_value_by_tier = [
        0 => 0, 1 => 500, 2 => 1000, 3 => 2500, 4 => 5000,
        5 => 10000, 6 => 25000, 7 => 50000, 8 => 100000, 9 => 500000
    ];
    $sell_value = $sell_value_by_tier[$item->item_tier] ?? 0;
    $player->player_coins += $sell_value;
    return "Sold for $sell_value coins.";
}

function checkUserStock($player_id, $item_ids) {
    if (empty($item_ids)) return [];
    $item_ids_string = implode("','", array_map('addslashes', $item_ids));
    $query = "SELECT item_id, item_qty FROM BasicInventory 
              WHERE player_id = " . intval($player_id) . " 
              AND item_id IN ('$item_ids_string')";
    $result = run_query($query);
    $stock = [];
    foreach ($result as $row) {
        $stock[$row['item_id']] = (int) $row['item_qty'];
    }
    foreach ($item_ids as $item_id) {
        if (!isset($stock[$item_id])) {
            $stock[$item_id] = 0;
        }
    }
    return $stock;
}

?>