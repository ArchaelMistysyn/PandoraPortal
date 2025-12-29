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
            "Salvation (Class Skill)" => [
                ["item_id" => "Salvation", "quantity" => 1]
            ],
            "Implant" =>  [["item_id" => "Gemstone" . $element, "quantity" => 1]],
            "Wish Purification" => [["item_id" => "Crystal2", "quantity" => 1]],
            "Abyss Purification" => [["item_id" => "Crystal3", "quantity" => 1]],
            "Divine Purification" => [["item_id" => "Crystal4", "quantity" => 1]],
            "Blood Purification" => [["item_id" => "Sacred", "quantity" => 1]]
        ];
        if (!array_key_exists($action, $action_costs)) {
            return [];
        }
        return array_values(array_filter($action_costs[$action] ?? []));
    }
    
    function forgeQualifyAndRate(&$response, $item, $action, $stock, $cost) {
        global $max_enhancement, $sovereign_item_list;
        $response['qualified'] = true;
        $response['success_rate'] = 100;
        if ($action !== "Blood Extraction" && $item->item_tier == 9 || in_array($item->item_base_type, $sovereign_item_list)) {
            $response['qualified'] = false;
            return;
        }
        $element = 0;
        if (!empty($cost)) {
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
                if ($item->item_tier < 5 || $item->item_type === "W") { 
                    $response['qualified'] = false; 
                }
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
            case "Salvation (Class Skill)":
                if ($item->item_type != 'Y') { $response['qualified'] = false; }
                break;
            case "Wish Purification":
            case "Abyss Purification":
            case "Divine Purification":
            case "Blood Purification":
                $ready = true;
                $ready = $ready && $item->item_tier >= 5;
                $ready = $ready && $item->item_enhancement >= $max_enhancement[$item->item_tier - 1];
                $ready = $ready && $item->item_num_sockets >= 1;
                $ready = $ready && $item->item_quality_tier >= 5;
                $ready = $ready && checkAugment($item) >= ($item->item_tier * 6);
                if (!$ready) { $response['qualified'] = false; }
                break;
            case "Blood Extraction":
                if ($item->item_tier != 9) { $response['qualified'] = false; }
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
        if (!empty($response['cost'])) {
            foreach ($response['cost'] as $requirement) {
                $item_id = $requirement['item_id'];
                $required_qty = (int) $requirement['quantity'];
                $response['stock'][$item_id] -= $required_qty;
                $query = "UPDATE BasicInventory SET item_qty = item_qty - $required_qty WHERE player_id = $player_profile->player_id AND item_id = '$item_id'";
                run_query($query);
            }
        }        
        if (rand(1, 100) > $response['success_rate']) {
            return $response;
        }
        $response['action_triggered'] = true;
        applyForgeAction($working_item, $action, $player_profile->player_class, $element);
        $working_item->set_item_name();
        $working_item->update_damage();
        $working_item->saveChanges();
        $response['item_data'] = format_gear_item($working_item, $player_profile);
        $item_content = generate_gear_content($player_profile, $working_item, false);
        $response['item_html'] = $item_content['html'];
        return $response;
    }

    function applyForgeAction(&$item, $action, $player_class, $element = null) {
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
            case "Mutate Reforge":
                reforgeClass($item);
                break;
            case "Hellfire Reforge":
                reforgeStats($item);
                break;
            case "Abyssfire Reforge":
                reforgeStats($item, true);
                break;
            case "Attune Rolls":
                addAugment($item);
                break;
            case "Star Fusion (Add/Reroll)":
            case "Radiant Fusion (Defensive)":
            case "Chaos Fusion (All)":
            case "Void Fusion (Damage)":
            case "Wish Fusion (Penetration)":
            case "Abyss Fusion (Curse)":
            case "Divine Fusion (Unique)":
            case "Salvation (Class Skill)":
                rerollItem($item, $action, $player_class);
                break;
            case "Implant":
                implantElement($item, $element);
                break;
            case "Wish Purification":
            case "Abyss Purification":
            case "Divine Purification":
            case "Blood Purification":
                purifyItem($item);
                break;
            case "Blood Extraction":
                extractBlood($item);
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
    
    function reforgeClass(&$item, $specific_class = "") {
        global $class_names;
        if ($specific_class !== "") {
            $item->item_damage_type = $specific_class;
        } else {
            $available_classes = array_filter($class_names, fn($class) => $class !== $item->item_damage_type);
            $item->item_damage_type = $available_classes[array_rand($available_classes)];
        }
    }    
    
    function reforgeStats(&$item, $unlock = false) {
        $item->get_tier_damage();
        if (strpos($item->item_type, "D") !== false) {
            return;
        }
        if ($item->item_type === "W") {
            $item->set_base_attack_speed();
            return;
        }
        if ($item->item_type === "A") {
            $item->set_base_damage_mitigation();
        }
        if ($item->item_tier >= 5 && $unlock) {
            global $rare_ability_dict;
            $available = array_filter(array_keys($rare_ability_dict), fn($key) => $key !== $item->item_bonus_stat);
            $item->item_bonus_stat = $available[array_rand($available)];
            return;
        }
        if ($item->item_tier < 5) {
            $current_bonus_stat = $item->item_bonus_stat;
            while ($current_bonus_stat === $item->item_bonus_stat && $current_bonus_stat !== "") {
                $item->assign_bonus_stat();
            }
        }
    }    
    
    function addAugment($selected_item, $mode = "") {    
        if ($mode === "All") {
            foreach ($selected_item->item_roll_values as $i => $roll_id) {
                $roll = new ItemRoll($roll_id);
                if ($roll->roll_tier < $selected_item->item_tier) {
                    $roll->roll_tier += 1;
                    $selected_item->item_roll_values[$i] = $roll->roll_tier . "-" . $roll->roll_code;
                }
            }
            return;
        }
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

    function reduceAugment(&$item) {
        foreach ($item->item_roll_values as $i => $roll_id) {
            $roll = new ItemRoll($roll_id);
            if ($roll->roll_tier > 1) {
                $roll->roll_tier -= 1;
                $item->item_roll_values[$i] = $roll->roll_tier . "-" . $roll->roll_code;
            }
        }
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
    
    function rerollItem(&$item, $fusion_type, $player_class = null) {
        global $roll_structure_dict, $item_roll_master_dict;
        $current_rolls = &$item->item_roll_values;
        $max_rolls = 6;
        if ($fusion_type === "Star Fusion (Add/Reroll)") {
            if (count($current_rolls) < $max_rolls) {
                add_roll($item, 1); 
            } else {
                reroll_roll($item, "any");
            }
            return;
        }
        if ($fusion_type === "Chaos Fusion (All)") {
            reroll_roll($item, "all", $player_class);
            return;
        }
        $fusion_target_map = [
            "Radiant Fusion (Defensive)" => "defensive",
            "Void Fusion (Damage)" => "damage",
            "Wish Fusion (Penetration)" => "penetration",
            "Abyss Fusion (Curse)" => "curse",
            "Divine Fusion (Unique)" => "unique",
            "Salvation (Class Skill)" => "Salvation"
        ];
        reroll_roll($item, $fusion_target_map[$fusion_type], $player_class);
    }
     
    function implantElement(&$item, $element) {
        $item->item_elements[$element] = 1;
    }
    
    function openSocket(&$item) {
        $item->item_num_sockets = 1;
    }

    function purifyItem(&$item) {
        $item->item_tier += 1;
        $item->item_quality_tier = 5;
        reforgeStats($item);
        if ($item->item_tier == 9) {
            addAugment($item, "All");
        }
    }

    function extractBlood(&$item) {
        global $verified_player_id;
        $item->item_tier -= 1;
        $item->item_quality_tier = 5;
        reforgeStats($item);
        reduceAugment($item);
        run_query("UPDATE BasicInventory SET item_qty = item_qty + 1 WHERE player_id = {$verified_player_id} AND item_id = 'Sacred'");
    }

    function refine_item($item_id) {
        global $verified_player_id, $refine_map;
        $update_query = "UPDATE BasicInventory SET item_qty = item_qty - 1 WHERE player_id = {$verified_player_id} AND item_id = '{$item_id}'";
        run_query($update_query);
        $new_item = try_refine($refine_map[$item_id][0], $refine_map[$item_id][1]);
        if (!isset($new_item)) { return ''; }
        $new_item->saveChanges("new");
        return $new_item->display_item(strpos($new_item->item_type, "D") !== false, "basic");
    }
    
    function try_refine($item_type, $target_tier) {
        global $verified_player_id;
        $new_tier = 0;
        $check = random_int(1, 100);
        if (strpos($item_type, "D") === false && $target_tier >= 5) {
            $rolled = generate_random_tier();
            $new_tier = ($rolled <= $target_tier) ? $target_tier : $rolled;
            if ($item_type !== "W" && $check > 80) { $new_tier = 0; }
        } else {
            if ((strpos($item_type, "D") !== false && $target_tier >= 5 && $check <= 50) ||($target_tier <= 4 && $check <= 75)) {
                $new_tier = ($target_tier == 4) ? generate_random_tier() : generate_random_tier($target_tier, 8);
                if ($new_tier == 1) $new_tier = 2;
            }
        }
        if ($new_tier == 0 ) { return null; }
        return new CustomItem($verified_player_id, $item_type, $new_tier, "", false, true);
    }

?>