<?php
session_start();
include_once('./bot_php/db_queries.php');
include_once('./bot_php/globals.php');
include_once('./bot_php/player.php');
include_once('./bot_php/tarot.php');
include_once('./bot_php/insignia.php');
include_once('./bot_php/pact.php');
include_once('./bot_php/path.php');
include_once('./bot_php/inventory.php');
include_once('./bot_php/itemrolls.php');
include_once('./bot_php/boss.php');
include_once('./bot_php/forge.php');
include_once('./bot_php/shared_methods.php');
include_once('./bot_php/battle_handler.php');
include_once('./bot_php/infuse.php');
include_once('./bot_php/map_handler.php');
include_once('./bot_php/mapdata.php');


// Data Verification
if (!isset($_SESSION['player_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}
$verified_player_id = $_SESSION['player_id'];
$input = json_decode(file_get_contents("php://input"), true);
$action = isset($input['action']) && $input['action'] !== 'null' ? $input['action'] : null;
$slot_type = isset($input['slot_type']) && $input['slot_type'] !== 'null' ? $input['slot_type'] : null;
$boss_calltype = isset($input['boss_calltype']) && $input['boss_calltype'] !== 'null' ? $input['boss_calltype'] : null;
$element = isset($input['element']) && $input['element'] !== 'null' ? $input['element'] : null;
$quest_choice = isset($input['quest_choice']) && $input['quest_choice'] !== 'null' ? $input['quest_choice'] : null;
$magnitude = isset($input['magnitude']) && $input['magnitude'] !== 'null' ? $input['magnitude'] : null;
$numeric_id = isset($input['numeric_id']) && $input['numeric_id'] !== 'null' ? $input['numeric_id'] : null;
$item_id = isset($input['item_id']) && $input['item_id'] !== 'null' ? $input['item_id'] : null;
$recipe_name = isset($input['recipe']) && $input['recipe'] !== 'null' ? $input['recipe'] : null;
$map_name = isset($input['map_name']) && $input['map_name'] !== 'null' ? $input['map_name'] : null;
$room_action = isset($input['room_action']) && $input['room_action'] !== 'null' ? $input['room_action'] : null;
$room_type = isset($input['room_type']) && $input['room_type'] !== 'null' ? $input['room_type'] : null;

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
if ($quest_choice !== null && $quest_choice !== '' && (!ctype_digit((string) $quest_choice) || $quest_choice < 0 || $quest_choice > 3)) {
    echo json_encode(["success" => false, "message" => "Invalid quest choice provided: " . $quest_choice]);
    exit();
}
if ($magnitude !== null && $magnitude !== '' && (!ctype_digit((string) $magnitude) || $magnitude < 0 || $magnitude > 10)) {
    echo json_encode(["success" => false, "message" => "Invalid magnitude provided: " . $magnitude]);
    exit();
}
if ($numeric_id !== null && $numeric_id !== '' && (!ctype_digit((string) $numeric_id) || $numeric_id < 0)) {
    echo json_encode(["success" => false, "message" => "Invalid numeric_id provided: " . $numeric_id]);
    exit();
}
if ($recipe_name !== null && !isset($all_recipe_names[$recipe_name])) {
    echo json_encode(["success" => false, "message" => "Invalid recipe name"]);
    exit();
}
if ($map_name !== null && $map_name !== '' && !in_array($map_name, $valid_maps, true)) {
    echo json_encode(["success" => false, "message" => "Invalid map provided: " . $map_name]);
    exit();
}


$refine_map = [
    "Unrefined1" => ["G", 4], "Void5" => ["G", 5],
    "Unrefined2" => ["V", 4], "Void3" => ["V", 5],
    "Unrefined3" => ["C", 4], "Void6" => ["C", 5],
    "Void1" => ["W", 5], "Void2" => ["A", 5], "Void4" => ["Y", 5],
    "Gem1" => ["D1", 4], "Gem2" => ["D2", 4], "Gem3" => ["D3", 4],
    "Jewel1" => ["D1", 5], "Jewel2" => ["D2", 5], "Jewel3" => ["D3", 5],
    "Jewel4" => ["D4", 6], "Jewel5" => ["D5", 7]
];

$refine_actions = [
    "Unrefined1", "Unrefined2", "Unrefined3", "Void1",  "Void2", "Void3", "Void4", "Void5", "Void6", 
    "Gem1", "Gem2", "Gem3", "Jewel1", "Jewel2", "Jewel3", "Jewel4", "Jewel5"
];
$forge_actions = [
    "Fae Enchant", "Gemstone Enchant", "Reinforce Quality", "Create Socket", "Hellfire Reforge",
    "Abyssfire Reforge", "Mutate Reforge", "Attune Rolls", "Star Fusion (Add/Reroll)",
    "Radiant Fusion (Defensive)", "Chaos Fusion (All)", "Void Fusion (Damage)",
    "Wish Fusion (Penetration)", "Abyss Fusion (Curse)", "Divine Fusion (Unique)", "Salvation (Class Skill)", "Implant",
    "Wish Purification", "Abyss Purification", "Divine Purification", "Blood Purification", "Blood Extraction"
];

$boss_calltypes = ["Any", "Fortress", "Dragon", "Demon", "Paragon", "Arbiter", 
    "Summon1", "Summon2", "Summon3", "Gauntlet", "Palace1", "Palace2", "Palace3", "Ruler"];

$boss_tier_dict = ["Any" => 0, "Fortress" => 0, "Dragon" => 0, "Demon" => 0, "Paragon" => 0, "Arbiter" => 0, "Summon1" => 5, "Summon2" => 6,
    "Summon3" => 7, "Gauntlet" => 1, "Palace1" => 8, "Palace2" => 8, "Palace3" => 8, "Ruler" => 9];    
$spawn_dict = [0 => 0, 1 => 1, 3 => 2, 5 => 3, 9 => 4];
$battleItemCost = [
    "Fortress" => "Stone1", "Dragon" => "Stone2", "Demon" => "Stone3", "Paragon"  => "Stone4", "Arbiter"  => "Stone6", "Gauntlet" => "Compass",
    "Summon1" => "Summon1", "Summon2" => "Summon2", "Summon3" => "Summon3", "Palace1" => "Lotus10", "Palace2" => "Lotus10", "Palace3" => "Lotus10"    
];
  

if ($boss_calltype !== null && $boss_calltype !== '' && !in_array($boss_calltype, $boss_calltypes)) {
    echo json_encode(["success" => false, "message" => "Invalid calltype provided: " . $boss_calltype]);
    exit();
}

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
} else if (in_array($action, $refine_actions)) {
    $player_profile = get_player_by_id($verified_player_id);
    $stock = checkUserStock($verified_player_id, [$action]);
    if (check_custom_inventory_capacity($verified_player_id, $refine_map[$action][0]) > 40) {
        $response = ["success" => true, "stock_check" => true, "inventory_check" => false];
    } else if ($stock[$action] < 1) {
        $response = ["success" => true, "stock_check" => false, "inventory_check" => true];
    } else {
        $item_html = refine_item($action);
        $response = ["success" => true, "stock_check" => true, "inventory_check" => true, "item_html" => $item_html];
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
} else {
    switch ($action) {
        case "inventory":
            $inventory_result = get_inventory_by_player_id($verified_player_id);
            $player_profile = get_player_by_id($verified_player_id);
            if ($inventory_result["success"] && $player_profile) {
                $response = ["success" => true, "items" => $inventory_result["items"], "player" => $player_profile];
            } else {
                $response = ["success" => false, "message" => "Inventory or player not found"];
            }
            break;        
        case "showInventoryItem":
            if (!$item_id) {
                $response = ["success" => false, "message" => "Missing item ID"];
            } else {
                $response = create_inventory_lightbox($verified_player_id, $item_id);
            }
            break;
        case "showItemPurchase":
            if (!$item_id) {
                $response = ["success" => false, "message" => "Missing item ID"];
            } else {
                $response = create_inventory_lightbox($verified_player_id, $item_id, true);
            }
            break;
        case "showTarotPurchase":
            if ($numeric_id === null || $numeric_id < 0 || $numeric_id > 30) {
                $response = ["success" => false, "message" => "Invalid tarot ID"];
            } else {
                $player_profile = get_player_by_id($verified_player_id);
                if ($player_profile) {
                    $tarot_card = get_tarot($verified_player_id, $numeric_id);
                    $response = create_tarot_lightbox($player_profile, $tarot_card, $numeric_id);
                } else {
                    $response = ["success" => false, "message" => "Player not found"];
                }
            }
            break;
        case "synthesizeTarot":
            if ($numeric_id === null || $numeric_id < 0 || $numeric_id > 30) {
                $response = ["success" => false, "message" => "Invalid tarot ID"];
            } else {
                $player_profile = get_player_by_id($verified_player_id);
                if ($player_profile) {
                    $tarot_card = get_tarot($verified_player_id, $numeric_id);
                    $response = synthesize_tarot($player_profile, $tarot_card, $numeric_id);
                } else {
                    $response = ["success" => false, "message" => "Player not found"];
                }
            }
            break;
        case "equipTarot":
            if ($numeric_id === null || $numeric_id < 0 || $numeric_id > 30) {
                $response = ["success" => false, "message" => "Invalid tarot ID"];
            } else {
                $player_profile = get_player_by_id($verified_player_id);
                if ($player_profile) {
                    $tarot_card = get_tarot($verified_player_id, $numeric_id);
                    $response = equip_tarot($player_profile, $tarot_card, $numeric_id);
                } else {
                    $response = ["success" => false, "message" => "Player not found"];
                }
            }
            break;
        case "bindTarot":
            if ($numeric_id === null || $numeric_id < 0 || $numeric_id > 30) {
                $response = ["success" => false, "message" => "Invalid tarot ID"];
            } else {
                $player_profile = get_player_by_id($verified_player_id);
                if ($player_profile) {
                    $tarot_card = get_tarot($verified_player_id, $numeric_id);
                    $response = bind_tarot($player_profile, $tarot_card, $numeric_id);
                } else {
                    $response = ["success" => false, "message" => "Player not found"];
                }
            }
            break;
        case "exchangeEssence":
            if (!$item_id) {
                $response = ["success" => false, "message" => "Missing item ID"];
            } else {
                $response = exchangeEssence($item_id);
            }
            break;
        case "showInfusion":
            if (!$recipe_name) {
                $response = ["success" => false, "message" => "Missing recipe name"];
                break;
            }
            $player_profile = get_player_by_id($verified_player_id);
            $recipe_obj = RecipeObject::from_name($recipe_name);
            if (!$recipe_obj) {
                $response = ["success" => false, "message" => "Recipe not found"];
                break;
            }
            [$embed_html, $can_craft, $is_gear, $has_sacred] = $recipe_obj->create_cost_html($player_profile);
            $menu_button = $recipe_obj->build_infusion_menu($can_craft, $has_sacred);
            $response = ["success" => true, "html" => "<div class='item-displaybox'>$embed_html</div>", "menu" => $menu_button];
            break;
        case "executeInfusion":
        case "executeSacredInfusion":
            if (!$recipe_name) {
                $response = ["success" => false, "message" => "Missing recipe name"];
                break;
            }
            $player_profile = get_player_by_id($verified_player_id);
            $recipe_obj = RecipeObject::from_name($recipe_name);
            if (!$recipe_obj) {
                $response = ["success" => false, "message" => "Recipe not found"];
                break;
            }
            $result = $recipe_obj->execute_infusion($player_profile, $action === "executeSacredInfusion");
            $response = ["success" => true, "html" => $result["html"], "menu" => $result["menu"]];
            break;
        case "purchaseShopItem":
            if (!$item_id) {
                $response = ["success" => false, "message" => "Missing item ID"];
            } else {
                $response = purchaseShopItem($item_id, $numeric_id);
            }
            break;
        case "playerExtra":
            $player_profile = get_player_by_id($verified_player_id);
            $gear_score = calculate_gear_score($player_profile);
            $response = $player_profile ? ["success" => true, "player" => $player_profile, "player_gear_score" => $gear_score] : ["success" => false, "message" => "Player not found"];
            break;
        case "player":
            $player_profile = get_player_by_id($verified_player_id);
            $response = $player_profile ? ["success" => true, "player" => $player_profile] : ["success" => false, "message" => "Player not found"];
            break;
        case "runBoss":
            $response = run_boss($boss_calltype, $magnitude);
            break;
        case "runCycle":
            $response = run_cycle($numeric_id);
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
        case "getQuest":
            $player_profile = get_player_by_id($verified_player_id);
            if (!$player_profile) { $response = ["success" => false, "message" => "Player not found"]; }
            $quest_obj = get_current_quest($player_profile);
            $response = handle_get_quest($player_profile, $quest_obj);
            break;
        case "completeQuest":
            $player_profile = get_player_by_id($verified_player_id);
            if (!$player_profile) { $response = ["success" => false, "message" => "Player not found"]; }
            $quest_obj = get_current_quest($player_profile);
            $response = handle_complete_quest($player_profile, $quest_obj, $quest_choice);
            break;
        case "checkMonument":
            $player_profile = get_player_by_id($verified_player_id);
            if (!$player_profile) { $response = ["success" => false, "message" => "Player not found"]; }
            if ($numeric_id > 5) { $response = ["success" => false, "message" => "Invalid id"]; }
            $monument_data = explode(';', $player_profile->misc_data['monument_data']);
            $is_claimable = $monument_data[$numeric_id - 1] !== "1";
            if (!$is_claimable) { $response = [ "success" => false, "message" => "Invalid claim attempt"]; }
            $response = handle_monument($player_profile, $numeric_id, $monument_data, $is_claimable);
            break;
        case "startExpedition":
            $player_profile = get_player_by_id($verified_player_id);
            if (!$player_profile) { $response = ["success" => false, "message" => "Player not found"]; }
            $response = startExpedition($map_name, $player_profile);
            break;
        case "roomAction":
            $player_profile = get_player_by_id($verified_player_id);
            if (!$player_profile) { $response = ["success" => false, "message" => "Player not found"]; }
            $response = handleRoomAction($player_profile, $room_type, $room_action);
            break;
        default:
            $response["message"] = "Invalid action";
    }
}
if (!$response || !is_array($response)) {
    $response = ["success" => false, "message" => "Empty or invalid response format"];
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
        $item["name"] = $itemData[$item["item_id"]]["name"];
        $filepath = get_frame_image($item["item_id"]);
        $item["icon"] = $filepath;
    }
    return $specific_item_id !== null ? ["success" => true, "item" => $inventory[0]] : ["success" => true, "items" => $inventory];
}

function create_inventory_lightbox($player_id, $item_id, $buy_view=false) {
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
    $menu_html = "<div id='lightbox-menu'>";
    if ($buy_view) {
        $purchase_button = getShopCostHTML($item_html, $item_id);
        $menu_html .= $purchase_button;
        if ($purchase_button === '') {
            $menu_html .= "<button class='lightbox-button-red'><s>Purchase</s></button>";
        }
    }
    $item_html .= "</div>";
    $menu_html .= "<button class='lightbox-button-gray' onclick='closeLightbox()'><span class='symbol-height'>✖</span> Close</button>";
    $menu_html .= "</div>";
    return ["success" => true, "html" => $item_html, "menu" => $menu_html];
}

function create_tarot_lightbox($player_profile, $tarot_card, $numeric_id) {
    $html = "<div id='tarot-lightbox'>";
        $html .= display_tarot($tarot_card, 'Synthesize');
    $html .= "</div>";
    $menu = "<div id='lightbox-menu'>";
    $essence_id = "Essence" . $tarot_card->card_numeral;
    $stock_map = checkUserStock($player_profile->player_id, [$essence_id, "Lotus9", "Lotus11"]);
    $essence_stock = $stock_map[$essence_id];
    $lotus_needed = 0;
    if ($tarot_card->num_stars === 7) {
        $lotus_needed = 1;
    } elseif ($tarot_card->num_stars === 8) {
        $lotus_needed = $tarot_card->card_tier;
    }
    $lotus_id = $tarot_card->num_stars === 7 ? "Lotus9" : ($tarot_card->num_stars === 8 ? "Lotus11" : null);
    $lotus_stock = $lotus_id !== null ? ($stock_map[$lotus_id] ?? 0) : 0;
    $can_equip = $player_profile->equipped_tarot !== $tarot_card->card_numeral && $tarot_card->num_stars >= 1;
    $can_synthesize = ($tarot_card->card_qty >= 2 && $tarot_card->num_stars < 9 && $tarot_card->num_stars >= 1 && !($lotus_id !== null && $lotus_stock < $lotus_needed));
    $can_bind = ($essence_stock > 0 && $tarot_card->num_stars < 9);
    $equip_class = $can_equip ? "green" : "gray";
    $synth_class = $can_synthesize ? "blue" : "gray";
    $bind_class = $can_bind ? "blue" : "gray";
    $equip_onclick = $can_equip ? "onclick=\"equipTarot('{$numeric_id}')\"" : "";
    $button_onclick = $can_synthesize ? "onclick=\"synthesizeTarot('{$numeric_id}')\"" : "";
    $bind_onclick = $can_bind ? "onclick=\"bindTarot('{$numeric_id}')\"" : "";
    $menu .= "<button class='lightbox-button-$equip_class' $equip_onclick>Equip</button>";
    $menu .= "<button class='lightbox-button-$synth_class' $button_onclick>Synthesis</button>";
    $menu .= "<button class='lightbox-button-$bind_class' $bind_onclick>Bind</button>";
    $menu .= "<button class='lightbox-button-gray' onclick='closeLightbox()'><span class='symbol-height'>✖</span> Close</button>";
    $menu .= "</div>";
    return ["success" => true, "html" => $html, "menu" => $menu];
}

function synthesize_tarot($player_profile, $tarot_card, $numeric_id) {
    global $tarot_rate_map;
    $attempt_success = false;
    if ($tarot_card->card_qty < 2) {
        return ["success" => false, "message" => "Not enough cards to synthesize."];
    }
    if ($tarot_card->num_stars >= 9) {
        return ["success" => false, "message" => "Card is already maxed."];
    }
    if ($tarot_card->num_stars === 7 || $tarot_card->num_stars === 8) {
        $lotus_id = $tarot_card->num_stars === 7 ? "Lotus9" : "Lotus11";
        $lotus_qty = $tarot_card->num_stars === 7 ? 1 : $tarot_card->card_tier;
        $stock = checkUserStock($player_profile->player_id, [$lotus_id])[$lotus_id] ?? 0;
        if ($stock < $lotus_qty) {
            return ["success" => false, "message" => "Cannot pay cost."];
        }
        update_stock($player_profile, $lotus_id, -$lotus_qty);
    }
    $tarot_card->card_qty -= 1;
    $success = rand(1, 100) <= ($tarot_rate_map[$tarot_card->num_stars] ?? 0);
    if ($success) {
        $update_query = "UPDATE TarotInventory SET num_stars = num_stars + 1, card_qty = card_qty - 1 ";
        $update_query .= "WHERE player_id = " . $player_profile->player_id . " AND card_numeral = '" . $tarot_card->card_numeral . "'";
        $tarot_card->num_stars += 1;
        $attempt_success = true;
    } else {
        $update_query = "UPDATE TarotInventory SET card_qty = card_qty - 1 ";
        $update_query .= "WHERE player_id = " . $player_profile->player_id . " AND card_numeral = '" . $tarot_card->card_numeral . "'";
    }
    run_query($update_query, false);
    $new_tarot = new TarotItem($player_profile->player_id, null, $tarot_card->card_numeral, $tarot_card->card_qty, $tarot_card->num_stars, $tarot_card->card_enhancement);
    $new_tarot->resonance = $tarot_card->resonance;
    $response = create_tarot_lightbox($player_profile, $new_tarot, $numeric_id);
    $response['attempt_success'] = $attempt_success;
    return $response;
}

function bind_tarot($player_profile, $tarot_card, $numeric_id) {
    $essence_id = "Essence" . $tarot_card->card_numeral;
    $stock = checkUserStock($player_profile->player_id, [$essence_id])[$essence_id];
    if ($stock < 1) {
        return ["success" => false, "message" => "Insufficient essence."];
    }
    $rate = max(0, 90 - ($tarot_card->card_tier * 5));
    $success = rand(1, 100) <= $rate;
    if ($success) {
        if ($tarot_card->num_stars == 0) {
            $tarot_query = "INSERT INTO TarotInventory (player_id, card_numeral, card_name, card_qty, num_stars, card_enhancement)";
            $tarot_query .= " VALUES (" . $player_profile->player_id . ", '" . $tarot_card->card_numeral . "','" . $tarot_card->card_name . "', 1, 1, 0)";
            $resonance = $tarot_card->resonance;
            $tarot_card = new TarotItem($player_profile->player_id, null, $tarot_card->card_numeral, 1, 1, $tarot_card->card_enhancement);
            $tarot_card->resonance = $resonance;
        } else {
            $tarot_query = "UPDATE TarotInventory SET card_qty = card_qty + 1";
            $tarot_query .= " WHERE player_id = " . $player_profile->player_id . " AND card_numeral = '" . $tarot_card->card_numeral . "'";
            
        }
        run_query($tarot_query, false);
    }
    update_stock($player_profile->player_id, $essence_id, -1);
    $response = create_tarot_lightbox($player_profile, $tarot_card, $numeric_id);
    $response['attempt_success'] = $success;
    return $response;
}

function equip_tarot($player_profile, $tarot_card, $numeric_id) {
    $player_profile->equipped_tarot = $tarot_card->card_numeral;
    $player_profile->update_player_data();
    $response = create_tarot_lightbox($player_profile, $tarot_card, $numeric_id);
    $response['attempt_success'] = true;
    return $response;
}


function getShopCostHTML(&$item_html, $item_id) {
    global $itemData, $verified_player_id, $web_url_base;
    $player_profile = get_player_by_id($verified_player_id);
    $cost = $itemData[$item_id]["cost"] ?? 0;
    $purchase_button = '';
    $item_html .= '<div class="style-line"></div>';
    $item_html .= '<div class="cost-row">';
        if (strpos($item_id, "Essence") === 0) {
            $cost = $itemData[$item_id]['tier'];
            $token_qty = checkUserStock($verified_player_id, ['Token7'])['Token7'];
            $item = new BasicItem("Token7");
            $item_html .= '<img src="' . $item->image_link . '" alt="' . $item->item_name . '" class="cost-icon">';
            $item_html .= '<span class="cost-name"> ' . $item->item_name . ' </span>';
            $item_html .= '<span class="cost-quantity">' .  number_format((int)$token_qty) . ' / ' . $cost . '</span>';
            if($token_qty >= $cost) {
                $purchase_button = "<button class='lightbox-button-green' onclick=\"exchangeEssence('{$item_id}')\">Exchange</button>";
            }
            
        } else if ($cost === 0) {
            // Fish swap
            $purchase_button = "<button class='lightbox-button-green' onclick=\"exchangeItem('{$item_id}')\">Exchange</button>";
        } else {
            $item_html .= '<img src="' . $web_url_base . 'gallery/Icons/Misc/Lotus Coin.webp" alt="Lotus Coins" class="cost-icon">';
            $item_html .= '<span class="cost-name"> Lotus Coins </span>';
            $item_html .= '<span class="cost-quantity">' .  number_format((int)$player_profile->player_coins) . ' / ' . number_format((int)$cost) . '</span>';
            if($player_profile->player_coins >= $cost) {
                $purchase_button = "<button class='lightbox-button-green' onclick=\"purchaseShopItem('{$item_id}')\">Buy 1</button>";
            }
            if($player_profile->player_coins >= $cost * 10) {
                $purchase_button .= "<button class='lightbox-button-green' onclick=\"purchaseShopItem('{$item_id}', 10)\">Buy 10</button>";
            }
            if($player_profile->player_coins >= $cost * 100) {
                $purchase_button .= "<button class='lightbox-button-green' onclick=\"purchaseShopItem('{$item_id}', 100)\">Buy 100</button>";
            }
        }
    $item_html .= '</div>';
    return $purchase_button;
}

function exchangeEssence($item_id) {
    global $verified_player_id, $itemData;
    if (strpos($item_id, "Essence") !== 0) {
        return ["success" => false, "message" => "Invalid essence ID"];
    }
    $cost = $itemData[$item_id]['tier'] ?? 0;
    $stock = checkUserStock($verified_player_id, ['Token7'])['Token7'];
    if ($stock < $cost) {
        return ["success" => false, "message" => "Not enough tokens"];
    }
    $update_query = "UPDATE BasicInventory SET item_qty = item_qty - " . $cost . " WHERE player_id = " . $verified_player_id . " AND item_id = 'Token7'";
    run_query($update_query, false);
    update_stock($verified_player_id, $item_id, 1);
    return create_inventory_lightbox($verified_player_id, $item_id, true);
}

function purchaseShopItem($item_id, $quantity = 1) {
    global $verified_player_id, $itemData;
    $cost = $itemData[$item_id]['cost'] ?? 0;
    $player = get_player_by_id($verified_player_id);
    if ($player->player_coins < $cost * $quantity) { return ["success" => false, "message" => "Not enough coins"]; }
    $player->player_coins -= $cost * $quantity;
    $player->update_player_data();
    update_stock($verified_player_id, $item_id, $quantity);
    return create_inventory_lightbox($verified_player_id, $item_id, true);
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

class Quest {
    public int $quest_num;
    public int $quest_type;
    public string $quest_title;
    public string $quest_giver;
    public string $story_message;
    public string $html;
    public $cost;
    public $token_num;
    public $item_handin;
    public string $quest_message;
    public array $award_items;
    public $award_role;
    public string $image;
    public int $progress;
    public $quest_options;

    public function __construct(array $data, $oath_index) {
        global $web_url_base, $tarot_map, $quest_exceptions;
        $this->quest_num = $data['quest_num'];
        $this->quest_type = $data['quest_type'];
        $this->quest_title = $data['quest_title'];
        $temp_giver = $data['quest_giver'];
        $this->story_message = $data['story_message'];
        $this->html = "";
        $this->cost = $data['cost'];
        $this->token_num = $data['token_num'];
        $this->item_handin = $data['item_handin'];
        $this->quest_message = $data['quest_message'];
        $this->award_items = $data['award_items'] ?? [];
        $this->award_role = $data['award_role'];
        $this->quest_giver = $temp_giver;
        $temp_giver = str_replace("Echo of ", "", $temp_giver);
        if ($temp_giver === '[OATH]') {
            $oath_names = ["Pandora, The Celestial", "Thana, the Death", "Eleuia, the Wish"];
            $this->quest_giver = $temp_giver = $oath_names[$oath_index];
        }
        $tarot_type = $tarot_map[$temp_giver]['type'];
        $numeral = $tarot_map[$temp_giver]['numeral'];
        $this->image = $web_url_base . "gallery/Tarot/" . $tarot_type . "/" . $numeral . " - " . $this->quest_giver . ".webp";
        $this->progress = 0;
        $this->quest_options = [];
        // Set Exception Options
        if (isset($quest_exceptions['quest_options'][$this->quest_num])) {
            $this->quest_options = $quest_exceptions['quest_options'][$this->quest_num];
        }
    }

    public function generate_html() {
        $this->html  = '<div class="quest-title highlight-text">' . $this->quest_title . '</div>';
        $this->html .= '<div class="quest-giver">' . $this->quest_giver . '</div>';
        $this->html .= '<div class="style-line"></div>';
        $this->html .= '<div class="quest-story">' . nl2br($this->story_message) . '</div>';
        if ($this->quest_num >= 55) {
            return;
        }
        $this->html .= '<div class="style-line"></div>';
        $this->html .= '<div class="quest-message">' . $this->quest_message . ": ";
        if ($this->quest_type === 2 && $this->item_handin) {
            $item = new BasicItem($this->item_handin);
            $this->html .= '<img src="' . $item->image_link . '" alt="' . $item->item_name . '" class="quest-icon"> <span class="quest-span">x' . $this->cost . "</span>";
        } else {
            $this->html .= $this->progress . " / " . $this->cost;
        }
        $this->html .= '</div>';
    }
    
    public function check_conditions($player) {
        global $verified_player_id, $ring_check;
        // Special Quest Cases
        if ($this->quest_num === 20) return ($this->progress = ($player->insignia !== ""));
        if ($this->quest_num === 21) return ($this->progress = ($player->pact !== ""));
        if ($this->quest_num === 22) return ($this->progress = ($player->player_equipped[2] != 0));
        if ($this->quest_num === 28) return ($this->progress = ($player->player_equipped[5] != 0));
        if ($this->quest_num === 31) return ($this->progress = ($player->equipped_tarot !== ""));
        if ($this->quest_num === 51) {
            $this->progress = get_tarot_collection_count($verified_player_id);
            return $this->progress === 31;
        }
        if ($this->quest_num === 55) return false;
        // Token/Boss Token Quests
        if ($this->quest_type === 0 || $this->quest_type === 3) {
            $this->progress = $player->quest_tokens[$this->token_num] ?? 0;
            return $this->progress >= $this->cost;
        }
        // Level Quests
        if ($this->quest_type === 1) {
            $this->progress = $player->player_level;
            return $this->progress >= $this->cost;
        }
        // Hand-In Quests
        if ($this->quest_type === 2) {
            $item_result = get_inventory_by_player_id($verified_player_id, $this->item_handin);
            $this->progress = isset($item_result["item"]["item_qty"]) ? (int)$item_result["item"]["item_qty"] : 0;
            return $this->progress >= $this->cost;
        }
        return false;
    }
    
}

function get_current_quest($player_profile) {
    global $quest_data;
    $quest_num = $player_profile->player_quest;
    if ($quest_num > 55) $quest_num = 55;
    $data = $quest_data[$quest_num - 1];
    return new Quest($data, $player_profile->player_oath_num);
}

function handle_get_quest($player_profile, $quest) {
    $gain = 0;
    $ready_status = verify_quest_option_unlocks($quest, $player_profile, 0, $gain);
    return ["success" => true, "quest" => $quest, "ready_status" => $ready_status];
}

function handle_complete_quest($player_profile, $quest, $selected_option) {
    $index = $selected_option - 1;
    $choice_reward = null;
    $gain = 0;
    $ready_status = verify_quest_option_unlocks($quest, $player_profile, $index, $gain);
    if (!$ready_status) { return ["success" => false, "message" => "Invalid hand in request"];}
    if ($index >= 0) {
        if (empty($quest->quest_options[$index][3])) {
            return ["success" => false, "message" => "Selected quest option is not available"];
        }
        $choice_reward = $quest->quest_options[$index][1];
    }
    $reward_data = process_quest_completion($player_profile, $quest, $choice_reward, $index, $gain);
    return ["success" => true, "reward_html" => $reward_data[0], "achievement_data" => $reward_data[1]];
}

function verify_quest_option_unlocks($quest, $player_profile, $selected_option, &$gain) {
    global $quest_exceptions;
    $ready_status = $quest->check_conditions($player_profile);
    // Handle Regular Quest
    if (empty($quest->quest_options)) {
        $quest->generate_html();
        return $ready_status;
    }
    // Handle Exception Quest
    $oath_data = array_map('intval', explode(';', $player_profile->misc_data['oath_data']));
    if (isset($quest_exceptions['eligibility_dict'][$quest->quest_num])) {
        [$oath_slot, $adjustment_map] = $quest_exceptions['eligibility_dict'][$quest->quest_num];
        foreach ($quest->quest_options as $i => &$opt) {
            if (($selected_option - 1) == $i) {
                $gain = $adjustment_map[(string)$i] ?? 0;
            }
            $opt[3] = $ready_status && ((int)$oath_data[$oath_slot] >= ($adjustment_map[(string)$i] ?? 0));
        }
    } elseif ($quest->quest_num == 54) {
        $gain = 1;
        $ring_check = ["Twin Rings of Divergent Stars", "Crown of Skulls", "Chromatic Tears"];
        $equipped_id = $player_profile->player_equipped[4] ?? 0;
        if ($equipped_id) {
            $equipped_ring = read_custom_item($equipped_id);
            $matched_index = array_search($equipped_ring->item_base_type, $ring_check, true);
            if ($matched_index !== false && $oath_data[$matched_index] === 2) {
                $quest->progress = 1;
                $ready_status = true;
                foreach ($quest->quest_options as $i => &$opt) {
                    $opt[3] = ($i === $matched_index);
                }
            }
        }
    }
    $quest->generate_html();
    return $ready_status;
}

function process_quest_completion($player_profile, $quest, $choice_reward = null, $choice_num = -1, $gain = 0) {
    global $web_url_base, $quest_exceptions;
    $achievement_data = [];
    // EXP & Coins
    $exp_msg = "";
    $coin_msg = "";
    $exp_gain = (int)(floor(($quest->quest_num + 4) / 5) * 1000);
    $coin_gain = (int)(floor(($quest->quest_num + 4) / 5) * 5000);
    if (!empty($player_profile->player_pact)) {
        $pact = new Pact($player_profile);
        $exp_gain = calculate_exp_gain($exp_gain, $pact->pact_variant, $exp_msg);
        $coin_gain = calculate_coin_gain($coin_gain, $pact->pact_variant, $coin_msg);
    }
    $player_profile->player_exp += $exp_gain;
    $player_profile->player_coins += $coin_gain;
    // Update Level
    $lvl_data = apply_level_up($player_profile);
    $level_increase = $lvl_data[0];
    $lvl_msg = $lvl_data[1];
    if ($level_increase > 0) {
        handle_achievement($player_profile, "Level", $achievement_data);
    }   
    $reward_html = "<div class='quest-title highlight-text'>" . $quest->quest_title . "</div>";
    $reward_html .= "<div class='quest-giver'>Quest Completed</div>";
    $reward_html .= '<div class="style-line"></div>';
    // Update Exceptions
    $oath_data = array_map('intval', explode(';', $player_profile->misc_data['oath_data']));
    if ($choice_num !== -1) {
        if (isset($quest_exceptions['eligibility_dict'][$quest->quest_num])){
            $slot = $quest_exceptions['eligibility_dict'][$quest->quest_num][0];
        } else {
            $slot = $choice_num;
        }
        $oath_data[$slot] += $gain;
        $player_profile->misc_data['oath_data'] = implode(';', $oath_data);
        $player_profile->update_misc_data();
        foreach ($choice_reward as [$item_id, $qty]) {
            if (isset($quest->award_items[$item_id])) {
                $quest->award_items[$item_id] += $qty;
            } else {
                $quest->award_items[$item_id] = $qty;
            }
        }
    }    
    if ($choice_num !== -1) {
        $exception_lines = $quest_exceptions['quest_options'][$quest->quest_num][$choice_num][2];
        $reward_html .= "<div class='quest-completion-message'>";
        foreach ($exception_lines as $line) {
            $reward_html .= $line;
        }
        $reward_html .= '</div><div class="style-line"></div>';
    }    
    // Build Reward HTML display
    $reward_html  .= '<div class="reward-row">';
        $reward_html .= '<img src="' . $web_url_base . 'gallery/Icons/Misc/Exp.webp" alt="Experience" class="reward-icon">';
        $reward_html .= '<span class="reward-name"> EXP </span>';
        $reward_html .= '<span class="reward-quantity">' . number_format((int)$exp_gain) . 'x' . $exp_msg . $lvl_msg . '</span>';
    $reward_html .= '</div>';
    $reward_html .= '<div class="reward-row">';
        $reward_html .= '<img src="' . $web_url_base . 'gallery/Icons/Misc/Lotus Coin.webp" alt="Lotus Coins" class="reward-icon">';
        $reward_html .= '<span class="reward-name"> Lotus Coins </span>';
        $reward_html .= '<span class="reward-quantity">' . number_format((int)$coin_gain) . 'x' . $coin_msg . '</span>';
    $reward_html .= '</div>';
    // Item Rewards
    foreach ($quest->award_items as $item_id => $qty) {
        $item = new BasicItem($item_id);
        handle_achievement($player_profile, "Item", $achievement_data, $item_id);
        $reward_html .= '<div class="reward-row">';
            $reward_html .= '<img src="' . $item->image_link . '" alt="' . $item->item_name . '" class="reward-icon">';
            $reward_html .= '<span class="reward-name"> ' . $item->item_name . ' </span>';
            $reward_html .= '<span class="reward-quantity">' . $qty . 'x</span>';
        $reward_html .= '</div>';
    }
    if ($quest->award_role) {
        $player_profile->player_echelon += 1;
        handle_achievement($player_profile, "Achievement", $achievement_data, "Echelon " . $player_profile->player_echelon);
        // discord will not update currently - adjust on bot end
    }
    $player_profile->player_quest += 1;
    $player_profile->update_player_data();
    return ["<div class='quest-reward-box'>{$reward_html}</div>", $achievement_data];
}

function handle_monument($player_profile, $monument_id, &$monument_data, $is_claimable) {
    $titles = [
        1 => "Monument of Beginnings",
        2 => "Monument of Journeys",
        3 => "Monument of Providence",
        4 => "Monument of Endings",
        5 => "Monument of Apotheosis"
    ];
    $rewards = [
        1 => ["item" => "Hammer",     "qty" => 10,  "exp" => 25000],
        2 => ["item" => "Fragment1",  "qty" => 20,  "exp" => 50000],
        3 => ["item" => "Stone5",     "qty" => 10,  "exp" => 75000],
        4 => ["item" => "Skull2",     "qty" => 5,   "exp" => 100000],
        5 => ["item" => "Sacred",     "qty" => 1,   "exp" => 1000000]
    ];
    $title = $titles[$monument_id];
    $reward = $rewards[$monument_id];
    // Update Data
    $monument_data[$monument_id - 1] = "1";
    $player_profile->misc_data['monument_data'] = implode(';', $monument_data);
    $player_profile->update_misc_data();
    // Grant EXP
    $achievement_data = [];
    $exp_msg = "";
    $exp_gain = 0;
    if (!empty($player_profile->player_pact)) {
        $pact = new Pact($player_profile);
        $exp_gain = calculate_exp_gain($reward['exp'], $pact->pact_variant, $exp_msg);
    }
    $player_profile->player_exp += $exp_gain;
    // Update Level
    $lvl_data = apply_level_up($player_profile);
    $level_increase = $lvl_data[0];
    $lvl_msg = $lvl_data[1];
    if ($level_increase > 0) {
        handle_achievement($player_profile, "Level", $achievement_data);
    }   
    // Grant Item
    $item_html = '<div class="highlight-text lightbox-header">' . $title . '</div>';
    $item_html .= '<div class="style-line"></div>';
    $item = new BasicItem($reward['item']);
    handle_achievement($player_profile, "Item", $achievement_data, $reward['item']);
    $item_html .= '<div class="reward-row">';
    $item_html .= '<img src="' . $item->image_link . '" alt="' . $item->item_name . '" class="reward-icon">';
    $item_html .= '<span class="reward-name">' . $item->item_name . '</span>';
    $item_html .= '<span class="reward-quantity">' . $reward['qty'] . 'x</span>';
    $item_html .= '</div>';
    return ["success" => true, "reward_html" => $item_html, "title" => $title, "achievement_data" => $achievement_data];
}

?>