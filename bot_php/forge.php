<?php
    function getForgeItemDetails($player_id, $slot_type, &$working_item = null) {
        global $slot_types;
        $player_profile = get_player_by_id($player_id);
        $slot_index = array_search($slot_type, array_keys($slot_types), true);
        if (!isset($player_profile->player_equipped[$slot_index]) || $player_profile->player_equipped[$slot_index] == 0) {
            return ["success" => false, "message" => "No item equipped in this slot"];
        }
        $item_id = $player_profile->player_equipped[$slot_index];
        $working_item = read_custom_item($item_id);
        if (!$working_item) {
            return ["success" => false, "message" => "Item not found"];
        }
        $formatted_item = format_gear_item($working_item, $player_profile);
        $item_content = generate_gear_content($player_profile, $working_item, false);
        return ["success" => true, "item_html" => $item_content['html'], "item_data" => $formatted_item];
    }
    
    function getForgeActionCost($action, $element, $item) {
        $action_costs = [
            "Fae Enchant" => [
                ["item_id" => "Fae" . $element, "quantity" => 10],
                ($item->item_tier >= 5) ? ["item_id" => "Fragment" . ($item->item_tier - 4), "quantity" => 1] : null
            ],
            "Gemstone Enchant" => [["item_id" => "Gemstone" . $element, "quantity" => 1]],
            "Reinforce Quality" => [["item_id" => "Ore5", "quantity" => 1]],
            "Create Socket" => [["item_id" => "Matrix", "quantity" => 1]],
            "Hellfire Reforge" => [["item_id" => "Flame1", "quantity" => 1]],
            "Abyssfire Reforge" => [["item_id" => "Flame2", "quantity" => 1]],
            "Mutate Reforge" => [["item_id" => "Metamorphite", "quantity" => 1]],
            "Attune Rolls" => [["item_id" => "Pearl", "quantity" => 1]],
            "Star Fusion (Add/Reroll)" => [["item_id" => "Hammer", "quantity" => 1]],
            "Radiant Fusion (Defensive)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Heart1", "quantity" => 1]
            ],
            "Chaos Fusion (All)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Heart2", "quantity" => 1]
            ],
            "Void Fusion (Damage)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Fragment1", "quantity" => 1]
            ],
            "Wish Fusion (Penetration)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Fragment2", "quantity" => 1]
            ],
            "Abyss Fusion (Curse)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Fragment3", "quantity" => 1]
            ],
            "Divine Fusion (Unique)" => [
                ["item_id" => "Hammer", "quantity" => 1],
                ["item_id" => "Fragment4", "quantity" => 1]
            ],
            "Implant" =>  [["item_id" => "Gemstone" . $element, "quantity" => 1]]
        ];
        return array_values(array_filter($action_costs[$action] ?? []));
    }
    
    function forgeQualifyAndRate(&$response, $item, $action, $stock, $cost) {
        global $max_enhancement, $sovereign_item_list;
        $response['qualified'] = true;
        $response['success_rate'] = 100;
        if ($item->item_tier == 9 || in_array($item->item_base_type, $sovereign_item_list)) {
            $response['qualified'] = false;
            return;
        }
        foreach ($cost as $requirement) {
            $item_id = $requirement['item_id'];
            $required_qty = $requirement['quantity'];
            $available_qty = $stock[$item_id] ?? 0;
            if ($available_qty < $required_qty) {
                $response['qualified'] = false;
            }
        }
        $element_str = substr($cost[0]['item_id'], -1);
        if (ctype_digit($element_str)) {
            $element = intval($element_str);
        }
        switch ($action) {
            case "Fae Enchant":
                $response['success_rate'] = max(5, 100 - floor($item->item_enhancement / 10) * 5);
                if (!isset($item->item_elements[$element]) || $item->item_elements[$element] == 0) { $response['qualified'] = false; }
                break;
            case "Gemstone Enchant":
                if (!isset($item->item_elements[$element]) || $item->item_elements[$element] == 0) { $response['qualified'] = false; }
                break;
            case "Reinforce Quality":
                $response['success_rate'] = max(10, 100 - ($item->item_quality_tier * 10));
                if ($item->item_quality_tier >= 5) { $response['qualified'] = false; }
                break;
            case "Create Socket":
                $response['success_rate'] = 5;
                if ($item->item_num_sockets == 1) { $response['qualified'] = false; }
                break;
            case "Attune Rolls":
                $response['success_rate'] = 80;
                if (checkAugment($item) >= ($item->item_tier * 6)) { $response['qualified'] = false; }
                break;
            case "Hellfire Reforge":
                $response['success_rate'] = 50;
                break;
            case "Abyssfire Reforge":
                $response['success_rate'] = 75;
                if ($item->item_tier < 5) { $response['qualified'] = false; }
                break;
            case "Mutate Reforge":
                $response['success_rate'] = 75;
                break;
            case "Implant":
                $response['success_rate'] = 90;
                if ($item->item_elements[$element]) { $response['qualified'] = false; }
                break;
            case "Star Fusion (Add/Reroll)":
                $response['success_rate'] = 80;
                break;
            case "Radiant Fusion (Defensive)":
            case "Chaos Fusion (All)":
            case "Void Fusion (Damage)":
            case "Wish Fusion (Penetration)":
            case "Abyss Fusion (Curse)":
            case "Divine Fusion (Unique)":
                $response['success_rate'] = 80;
                if ($item->item_num_rolls != 6) { $response['qualified'] = false; }
                break;
            default:
                $response['qualified'] = true;
        }
    }    

    function processForgeExecution($player_profile, $working_item, $action, $element, $response) {
        if (!$working_item || $working_item->player_id !== $player_profile->player_id) {
            return ["success" => false, "message" => "Item or Player not found."];
        }
        $response['action_triggered'] = false;
        if (!$response['qualified']) return $response;
        foreach ($response['cost'] as $requirement) {
            $item_id = $requirement['item_id'];
            $required_qty = (int) $requirement['quantity'];
            $response['stock'][$item_id] -= $required_qty;
            $query = "UPDATE BasicInventory SET item_qty = item_qty - $required_qty WHERE player_id = $player_profile->player_id AND item_id = '$item_id'";
            run_query($query);
        }
        if (rand(1, 100) > $response['success_rate']) {
            return $response;
        }
        $response['action_triggered'] = true;
        applyForgeAction($working_item, $action, $element);
        $working_item->set_item_name();
        $working_item->saveChanges();
        $response['item_data'] = format_gear_item($working_item, $player_profile);
        $item_content = generate_gear_content($player_profile, $working_item, false);
        $response['item_html'] = $item_content['html'];
        return $response;
    }

    function applyForgeAction(&$item, $action, $element = null) {
        switch ($action) {
            case "Fae Enchant":
            case "Gemstone Enchant":
                enhanceItem($item);
                break;
            case "Reinforce Quality":
                reinforceItemQuality($item);
                break;
            case "Create Socket":
                openSocket($item);
                break;
            case "Hellfire Reforge":
            case "Abyssfire Reforge":
            case "Mutate Reforge":
                reforgeItem($item);
                break;
            case "Attune Rolls":
                attuneRolls($item);
                break;
            case "Star Fusion (Add/Reroll)":
            case "Radiant Fusion (Defensive)":
            case "Chaos Fusion (All)":
            case "Void Fusion (Damage)":
            case "Wish Fusion (Penetration)":
            case "Abyss Fusion (Curse)":
            case "Divine Fusion (Unique)":
                fusionProcess($item, $action);
                break;
            case "Implant":
                implantElement($item, $element);
                break;
            default:
                return;
        }
    }    

    function enhanceItem(&$item) {
        $item->item_enhancement += 1;
    }
    
    function upgradeItem(&$item) {
        $item->item_quality_tier += 1;
    }
    
    function reforgeItem(&$item) {
        resetItemStats($item);
    }
    
    function addAugment($selected_item) {    
        $eligible_rolls = [];
        foreach ($selected_item->item_roll_values as $index => $roll_id) {
            $current_roll = new ItemRoll($roll_id);
            if ($current_roll->roll_tier < $selected_item->item_tier) {
                $eligible_rolls[$index] = $current_roll;
            }
        }
        $random_index = array_rand($eligible_rolls);
        $roll_to_upgrade = $eligible_rolls[$random_index];
        $new_roll_tier = $roll_to_upgrade->roll_tier + 1;
        $roll_details = explode("-", $roll_to_upgrade->roll_id);
        $roll_details[0] = $new_roll_tier;
        $selected_item->item_roll_values[$random_index] = implode("-", $roll_details);
    }

    function checkAugment($selected_item) {
        $aug_total = 0;
        if ($selected_item->item_num_rolls == 0) {
            return $aug_total;
        }
        foreach ($selected_item->item_roll_values as $roll_id) {
            $current_roll = new ItemRoll($roll_id);
            $aug_total += $current_roll->roll_tier;
        }
        return $aug_total;
    }
    
    function augmentItem(&$item) {
        // $item->rolls[] = generateNewAugment();
        return;
    }
    
    function implantElement(&$item, $element) {
        $item->item_elements[$element] = 1;
    }
    
    function openSocket(&$item) {
        $item->item_num_sockets = 1;
    }   
    
?>