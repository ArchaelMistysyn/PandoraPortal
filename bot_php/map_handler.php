<?php
class Expedition {
    public $tier, $room_num, $length, $player, $room_structure, $current_room, $player_cHP, $player_mHP, $luck;

    public function __construct($player_profile, $tier) {
        $this->tier = $tier;
        $this->player = $player_profile;
        $this->room_num = 0;
        $this->length = 9 + $tier;
        $this->room_structure = $this->generateRoomStructure();
        $this->current_room = null;
        $this->player_cHP = $player_profile->player_cHP;
        $this->player_mHP = $player_profile->player_mHP;
        $this->luck = $tier;
    }

    private function generateRoomStructure() {
        $structure = [];
        for ($i = 0; $i < $this->length; $i++) {
            if ($i === $this->length - 1) {
                $structure[] = "greater_treasure";
            } else {
                $structure[] = $this->randomRoom();
            }
        }
        return $structure;
    }

    private function randomRoom() {
        global $random_room_list;
        // filter valid rooms by tier
        $valid = array_filter($random_room_list, fn($data) => $this->tier >= $data[1]);
        $choices = array_column($valid, 0);
        // jackpot override
        if (mt_rand(1, 1000) <= 5 * ($this->player->luck_bonus ?? 1)) {
            return "jackpot_room";
        }
        return $choices[array_rand($choices)];
    }

    public function saveExpedition() {
        global $verified_player_id;
        $this->clearExpedition();
        $structure = implode(";", $this->room_structure);
        $insert_query = "INSERT INTO playerWebMap (player_id, expedition_tier, expedition_room_num, expedition_length, expedition_structure, player_cHP, player_mHP, luck) ";
        $insert_query .="VALUES (" . intval($verified_player_id) . ", " . intval($this->tier) . ", " . intval($this->room_num) . ", " . intval($this->length) . ", '";
        $insert_query .=  $structure . "', " . intval($this->player_cHP) . ", " . intval($this->player_mHP) . ", " . intval($this->luck) . ")";
        run_query($insert_query, false);
    }

    private function updateExpedition() {
        global $verified_player_id;
        if ($this->current_room == null) {
            $this->clearExpedition();
            return;
        }
        $room_info = $this->current_room->type . ";" . $this->current_room->tier;
        $room_details = $this->current_room->element . ';' . $this->current_room->variant . ';' . $this->current_room->deity;
        $actions = implode(";", array_map(fn($opt) => $opt['action'], $this->current_room->options));
        $update_query = "UPDATE playerWebMap SET ";
        $update_query .= "expedition_room_num = " . intval($this->room_num);
        $update_query .= ", current_room = '" . $room_info . "', room_details = '" . $room_details . "', current_actions = '" . $actions;
        $update_query .= "', player_cHP = " . intval($this->player_cHP) . ", player_mHP = " . intval($this->player_mHP) . ", luck = " . intval($this->luck);
        $update_query .= " WHERE player_id = " . intval($verified_player_id);
        run_query($update_query, false);
    }

    private function clearExpedition() {
        global $verified_player_id;
        $delete_query = "DELETE FROM playerWebMap WHERE player_id = " . intval($verified_player_id);
        run_query($delete_query, false);
    }

    public function nextRoom() {
        if ($this->room_num >= $this->length) {
            $this->current_room = null;
        } else {
            $this->current_room = new Room($this->tier, $this->room_structure[$this->room_num], $this->luck);
        }
        $this->room_num++;
        $this->updateExpedition();
        return $this->current_room;
    }
}

class Room {
    public $type, $tier, $options, $title, $description, $display, $image, $element, $variant, $deity, $luck;

    public function __construct($tier, $type, $luck) {
        global $room_button_dict;
        $this->tier = $tier;
        $this->type = $type;
        $this->luck = $luck;
        $this->element = mt_rand(0, 8);
        $this->variant = "W";
        $this->deity = getDeityByTier($this->tier);
        $this->setText();
        $this->options = $room_button_dict[$this->type] ?? [["label" => "Proceed", "style" => "green", "action" => "nextRoom"]];
        $variant = $this->applyRoomVariants();
        if (!empty($variant)) {
            $this->title = $variant["title"];
            $this->description = $variant["description"];
            $this->options = $variant["buttons"];
        }
        $this->display = $this->buildDisplay();
        $this->image = "./gallery/Displays/Locations/Map.webp";
    }

    private function setText(){
        $this->title = "Room Title Goes Here";
        $this->description = "Room Description Goes Here";
    }

    private function buildDisplay() {
        $html  = "<div class='room-box'>";
        $html .= "<div class='highlight-text'>{$this->title}</div>";
        $html .= "<div class='room-description'>{$this->description}</div>";
        $html .= "<div class='style-line'></div>";
        $html .= "<div class='room-options'>";
        foreach ($this->options as $button) {
            $html .= "<button class='lightbox-button-{$button['style']}' ";
            $html .= "onclick=\"handleRoomAction('{$button['action']}', '{$this->type}')\">";
            $html .= "{$button['label']}</button>";
        }
        $html .= "</div></div>";
        return $html;
    }

    private function applyRoomVariants() {
        global $safe_msg_list, $room_button_dict, $trial_variants, $tag_dict, $monster_dict, $element_descriptor_list, $boss_list, $trap_room_name_list, $tarot_data;
        global $selection_pools, $pact_data, $itemData, $shrine_dict;
        $random_check = mt_rand(1, 100);
        $button_options = $room_button_dict[$this->type];
        switch ($this->type) {
            case "trap_room":
                $title = "{$trap_room_name_list[$this->element]} Trap Room";
                $description = "The remains of other fallen adventurers are clearly visible here. Perhaps their equipment is salvageable, however you feel uneasy.";
                break;
            case "statue_room":
                $title = "Foreboding Statue";
                $tarot_info = getTarotByNumeral($this->deity);
                $deity_name = $tarot_info['Name'];
                $description = "A statue of {$deity_name} stands before you.";
                break;
            case "basic_monster":
                $title = "Basic Monster Encounter";
                $element_descriptor = $element_descriptor_list[$this->element];
                $monster = $monster_dict[$this->type][array_rand($monster_dict[$this->type])];
                $prefix = (in_array(strtolower($element_descriptor[0]), ['a', 'e', 'i', 'o', 'u'])) ? "An" : "A";
                $description = "{$prefix} {$element_descriptor} {$monster} blocks your path!";
                break;
            case "elite_monster":
                $title = "Elite Monster Encounter";
                $monster = $monster_dict[$this->type][$this->element];
                $description = "{$monster} spotted!! It won't be long before it notices you.";
                break;
            case "legend_monster":
                $title = "Legendary Titan Encounter";
                $monster = $monster_dict[$this->type][$this->element];
                $description = "{$monster} the legendary titan comes into view!!! DANGER!!!";
                break;
            case "healing_room":
                $title = "Safe Zone";
                $description = $safe_msg_list[array_rand($safe_msg_list)];
                break;
            case "basic_treasure":
                $description = "The unopened chest calls to you.";
                if ($random_check <= 50) { $this->variant = "A"; } elseif ($random_check <= 80) { $this->variant = "W"; } else { $this->variant = "Y"; }
                $title = "Lesser {$tag_dict[$this->variant]} Chamber";
                break;
            case "greater_treasure":
                $description = "The irresistible allure of treasure entices you.";
                if ($random_check <= 33) { $this->variant = "A"; } elseif ($random_check <= 67) { $this->variant = "W"; } else { $this->variant = "Y"; }
                $title = "Greater {$tag_dict[$this->variant]} Vault";
                break;
            case "penetralia_room":
                $title = "Secret Penetralia";
                $description = "This room is well hidden. Perhaps there are valuable items here.";
                break;
            case "jackpot_room":
                $title = "Golden Penetralia!";
                $description = "Riches spread all across the secret room. Ripe for the taking!";
                break;
            case "crystal_room":
                $title = "Crystal Cave";
                $description = "Crystals are overly abundant in this cave. It is said that the rarest items are drawn to each other. Those adorned in precious metals may fare better than those who search blindly.";
                break;
            case "sanctuary_room":
                $title = "Butterfae Sanctuary";
                $description = "A wondrous room illuminated by the sparkling lights of countless elemental butterfaes.";
                $random_set = range(0, 8);
                shuffle($random_set);
                $reward_elements = array_slice($random_set, 0, 3);
                $isGemstone = (mt_rand(1, 1000) <= $this->luck);
                $rewards = array_map(fn($id_num) => $isGemstone ? "Gemstone{$id_num}" : "Fae{$id_num}", $reward_elements);
                $this->variant = implode("/", $rewards);
                $button_options = [
                    ["label" => $itemData[$rewards[0]]['name'], "style" => "blue", "action" => "sanctuaryOption1"],
                    ["label" => $itemData[$rewards[1]]['name'], "style" => "blue", "action" => "sanctuaryOption2"],
                    ["label" => $itemData[$rewards[2]]['name'], "style" => "blue", "action" => "sanctuaryOption3"]
                ];
                break;
            case "epitaph_room":
                $title = "Lone Epitaph";
                $description = "You see a tablet inscribed with glowing letters. It will take some time to uncover the message.";
                break;
            case "selection_room":
                $title = "Selection Trap";
                $description = "Two items sit precariously atop podiums, but it's obviously a trap. Trying to take both seems extremely dangerous.";
                $reward_pool = min(5, intval($this->luck / 5));
                if (mt_rand(1, 10000) <= $this->luck) { $reward_pool = 6; }
                $selected_pool = $selection_pools[$reward_pool];
                $random_items = array_rand($selected_pool, 2);
                $item1 = $selected_pool[$random_items[0]];
                $item2 = $selected_pool[$random_items[1]];
                $item1 = checkEssence($item1, $reward_pool);
                $item2 = checkEssence($item2, $reward_pool);
                if (mt_rand(1, 20000) == 1) { $item1 = "Skull4"; }
                $this->variant = "{$item1};{$item2}";
                $button_options = [
                    ["label" => $itemData[$item1]['name'], "style" => "blue", "action" => "selectOption1"],
                    ["label" => $itemData[$item2]['name'], "style" => "blue", "action" => "selectOption2"],
                    ["label" => "Both", "style" => "red", "action" => "selectBoth"]
                ];
                break;
            case "pact_room":
                $description = "As you examine the altar, a demonic creature materializes. It requests you to prove yourself with blood and offers to forge a pact.";
                $demon_tier = generate_random_tier(1, 8, $this->luck);
                $pact_type = array_rand($pact_data['pact_variants']);
                $demon_type = $pact_data['demon_variants'][$demon_tier];
                $title = "{$demon_type} Altar [{$pact_type}]";
                $this->variant = "{$demon_tier}/{$pact_type}";
                $image = "./botimages/Gear_Icon/Pact/Frame_Pact_{$demon_tier}_{$pact_type}.png";
                break;
            case "trial_room":
                $this->variant = array_rand($trial_variants);
                $trial_data = $trial_variants[$this->variant];
                $title = "Trial of {$this->variant}";
                $description = $trial_data["description"];
                $button_options = $trial_data["options"];
                break;
            case "boss_shrine":
                $description = "The shrine awaits the ritual of the challenger. Those who can endure the raw elemental power and complete the ritual shall be granted rewards and passage.";
                $this->variant = ($random_check <= (5 * $this->tier)) ? "Greater " : "";
                $target_list = ($this->variant === "") ? array_slice($boss_list, 1, -3) : array_slice($boss_list, 1, -2);
                $this->deity = $target_list[array_rand($target_list)];
                $boss_num = array_search($this->deity, $boss_list);
                if ($boss_num === false) { $boss_num = 3; }
                $title = "{$this->variant}{$element_descriptor_list[$this->element]} {$this->deity} Shrine";
                $button_options = [
                    ["label"  => "Ritual of {$shrine_dict[$boss_num][0]}", "style"  => "blue", "action" => "shrineOption1"],
                    ["label"  => "Ritual of {$shrine_dict[$boss_num][2]}", "style"  => "blue", "action" => "shrineOption2"],
                    ["label"  => "Ritual of Chaos", "style"  => "red", "action" => "shrineOption3"]
                ];
                break;
            case "heart_room":
                $title = "Judgement Room";
                $description = "Its heart barely beating, the poor creature beneath you is beyond help. Living hearts are extremely valuable if preserved correctly with magic. Will you cleanse or corrupt it?";
                break;
            default:
                return [];
        }
        return ["title" => $title, "description" => $description, "buttons" => $button_options];
    }

}

function startExpedition($map_name, $player_profile) {
    global $map_tier_dict, $verified_player_id;
    $tier = $map_tier_dict[$map_name];
    // stamina check
    $stamina_cost = 200 + ($tier * 50);
    if ($player_profile->player_stamina < $stamina_cost) {
        return ["success" => false, "message" => "Not enough stamina."];
    }
    // deduct stamina + persist
    $player_profile->player_stamina -= $stamina_cost;
    $player_profile->update_player_data();
    $player_profile->get_player_multipliers();
    $expedition = new Expedition($player_profile, $tier);
    $expedition->saveExpedition();
    $room = $expedition->nextRoom();
    return ["success" => true, "tier" => $tier, "room_display" => $room->display, "room_image" => $room->image];
}

function loadExpedition($player_id) {
    $query = "SELECT * FROM playerWebMap WHERE player_id = " . intval($player_id) . " LIMIT 1";
    $result = run_query($query, true);
    if (!$result || count($result) === 0) { return null; }
    return $result[0];
}

function rebuildExpedition($player_profile, $expedition_data) {
    [$expedition_room_type, $expedition_room_tier] = explode(";", $expedition_data['current_room']);
    $expedition = new Expedition($player_profile, intval($expedition_room_tier));
    $expedition->room_num = intval($expedition_data['expedition_room_num']);
    $expedition->room_structure = explode(";", $expedition_data['expedition_structure']);
    $expedition->player_cHP = intval($expedition_data['player_cHP']);
    $expedition->player_mHP = intval($expedition_data['player_mHP']);
    $expedition->luck = intval($expedition_data['luck']);
    $expedition->current_room = new Room(intval($expedition_room_tier), $expedition_room_type, $expedition->luck);
    [$room_element, $expedition->current_room->variant, $expedition->current_room->deity] = explode(";", $expedition_data['room_details']);
    $expedition->current_room->element = intval($room_element);
    return $expedition;
}

function updateDetails($expedition, $player_id, $action_data) {
    $current_hp = $expedition->player_cHP;
    $max_hp = $expedition->player_mHP;
    $actions = implode(";", $action_data);
    $update_query = "UPDATE playerWebMap SET current_actions = '" . $actions . "', player_cHP = " . intval($current_hp) . ", player_mHP = " . intval($max_hp);
    $update_query .= " WHERE player_id = " . intval($player_id);
    run_query($update_query, false);
}

function handleRoomAction($player_profile, $room_type, $room_action) {
    global $verified_player_id, $death_lines, $itemData;
    // Validate expedition exists
    $expedition_data = loadExpedition($verified_player_id);
    if (!$expedition_data) { return ["success" => false, "message" => "No active expedition."]; }
    // Validate the current room
    [$expedition_room_type, $expedition_room_tier] = explode(";", $expedition_data['current_room']);
    if ($room_type !== $expedition_room_type) { return ["success" => false, "message" => "Room mismatch."]; }
    // Validate room action
    $valid_actions = explode(";", $expedition_data['current_actions']);
    if (!in_array($room_action, $valid_actions)) { return ["success" => false, "message" => "Invalid action {$room_type} for this room."]; }
    // Handle action
    $expedition = rebuildExpedition($player_profile, $expedition_data);
    $next_room = null;
    $title = "";
    $description = "";
    $display = "";
    $image = "";
    $options = [["label"  => "Proceed", "style"  => "green", "action" => "nextRoom"]];
    switch ($room_action) {
        case "skipTreasure":
        case "trapBypass":
        case "nextRoom":
            $next_room = $expedition->nextRoom();
            if ($next_room === null) {
                $display = create_end_menu();
            }
            break;
        case "shortRest":
        case "longRest":
            $title = ($room_action === "shortRest") ? "Short rest" : "Long rest";
            $description = handleRest($expedition, $room_action);
            break;
        case "basicFight":
        case "eliteFight":
        case "legendFight":
            $title = "Battle Result";
            $description = handleCombat($expedition, $room_action);
            break;
        case "basicStealth":
        case "eliteStealth":
        case "legendStealth":
            $title = "Stealth Result";
            $description = handleStealth($expedition, $room_action);
            break;
        case "basic_treasure":
        case "greater_treasure":
            $title = "Treasure Result";
            $description = handleTreasure($expedition, $room_action);
            break;
        case "prayStatue":
        case "destroyStatue":
            $title = "Statue Result";
            $description = handleStatue($expedition, $room_action);
            break;
        case "refusePact":
        case "forgePact":
            $title = "Pact Result";
            $description = handlePact($expedition, $room_action);
            break;
        case "purifyHeart":
        case "taintHeart":
            $title = ($room_action === "purifyHeart") ? "Purify" : "Corrupt";
            $description = handleHeart($expedition, $room_action);
            break;
        case "searchEpitaph":
        case "decipherEpitaph":
            $title = "Epitaph Result";
            $description = handleEpitaph($expedition, $room_action);
            break;
        case "searchPenetralia":
        case "collectPenetralia":
        case "searchJackpot":
        case "collectJackpot":
            $title = "Penetralia Result";
            $method = str_contains($room_action, "collect") ? "Collect" : "Search";
            $description = handlePenetralia($expedition, $method);
            break;
        case "crystalResonate":
        case "crystalSearch":
            $title = "Crystal Result";
            $description = handleCrystal($expedition, $room_action);
            break;
        case "trialOption1":
        case "trialOption2":
        case "trialOption3":
            $title = "Trial Result";
            $description = handleTrial($expedition, $room_action);
            break;
        case "shrineOption1":
        case "shrineOption2":
        case "shrineOption3":
            $title = "Ritual Result";
            $description = handleShrine($expedition, $room_action);
            break;
        case "sanctuaryOption1":
        case "sanctuaryOption2":
        case "sanctuaryOption3":
            $title = "Sanctuary Reward";
            $description = handleSanctuary($expedition, $room_action);
            break;
        case "selectOption1":
        case "selectOption2":
        case "selectBoth":
            $title = "Selection Result";
            $description = handleSelection($expedition, $room_action);
            break;
        case "trapSalvage":
            $title = "Trap Triggered!";
            $description = handleTrap($expedition);
            break;
        default:
            return ["success" => false, "message" => "Unknown room action."];
    }
    if (isset($itemData[$description])) {
        $image = get_frame_image($description);
        $item_name = $itemData[$description]['name'];
        $description = "Received item(s): {$item_name}";
    } else if (ctype_digit($description)) {
        $custom_item_id = intval($description);
        $item_obj = read_custom_item($custom_item_id);
        if ($item_obj) {
            $image = $item_obj->get_gear_thumbnail();
            $description = "Received item(s): {$item_obj->item_name} - ITEM ID: {$custom_item_id}";
        }
    }
    if ($expedition->player_cHP <= 0) {
        $death_msg = $death_lines[array_rand($death_lines)];
        $options = [["label"  => "Return", "style"  => "red", "action" => "return"]];
        $action_buttons = buildButtonHTML($options, "death");
        $display  = "<div class='room-box'>";
            $display .= "<div class='highlight-text'>Expedition Failed</div>";
            $display .= "<div class='room-description'>{$description} You hear the voice of Thana, the Death, \"{$death_msg}\" </div>";
            $display .= "<div class='style-line'></div>";
            $display .= "<div class='room-options'>" . $action_buttons . "</div>";
        $display .= "</div>"; 
        $expedition->clearExpedition();        
    } else if ($next_room !== null) {
        $display = $next_room->display;
        $image = $next_room->image;
    } else {
        updateDetails($expedition, $verified_player_id, array_column($options, 'action'));
        $action_buttons = buildButtonHTML($options, $room_type);
        $display  = "<div class='room-box'>";
            $display .= "<div class='highlight-text'>{$title}</div>";
            $display .= "<div class='room-description'>{$description}</div>";
            $display .= "<div class='style-line'></div>";
            $display .= "<div class='room-options'>" . $action_buttons . "</div>";
        $display .= "</div>";
    }
    $response = ["success" => true, "tier" => $expedition->tier, "room_display" => $display, "room_image" => $image];
    return $response;
}

function buildButtonHTML($options, $room_type) {
    $html = "";
    foreach ($options as $option) {
        $label  = $option['label'];
        $colour  = $option['style'];
        $action = $option['action'];
        $html  .= "<button class='lightbox-button-{$colour}' ";
        if ($room_type === "death"){
            $html  .= "onclick=\"deathReturn()\">";
        } else {
            $html  .= "onclick=\"handleRoomAction('{$action}', '{$room_type}')\">";
        }
        $html  .= "{$label}</button>";
    }
    return $html;
}

# Result Room Handling Functions
function handleRest($expedition, $action) {
    $heal_percent = mt_rand($expedition->tier, 10 + $expedition->luck);
    if ($action === "longRest") $heal_percent *= 2;
    $heal_amount = intval(($expedition->player_mHP * $heal_percent) / 100);
    if (checkTrap($expedition, "healing_room")) {
        $fail_msg = "Nothing good will come from staying any longer.";
        if ($action === "longRest") {
            $damage = handleDamage($expedition, $heal_amount, $heal_amount);
            $death_msg = (handleDeath($expedition)) ? " You have been slain." : "";
            $fail_msg = "Ambushed while resting! Took {$damage} damage.{$death_msg}";
        }
        return $fail_msg;
    }
    $expedition->player_cHP = min($expedition->player_mHP, $expedition->player_cHP + $heal_amount);
    return "You take a moment to recover. You heal {$heal_amount} HP (" . number_format($expedition->player_cHP) . " / " . number_format($expedition->player_mHP) . ").";
}

function handleCombat($expedition, $action) {
    # Determine variant and scaling
    switch ($action) {
        case "basicFight":  $adjuster = 1; break;
        case "eliteFight":  $adjuster = 2; break;
        case "legendFight": $adjuster = 3; break;
        default: $adjuster = 1; break;
    }
    # Unscathed chance
    $unscathed_chance = intval(($expedition->luck * 2) / $adjuster);
    if (mt_rand(1, 100) <= $unscathed_chance) {
        return "You emerge unscathed from combat.";
    }
    # Set the damge range
    $min_dmg = 100 * $adjuster;
    $max_dmg = 300 * $adjuster;
    if ($adjuster === 3) {
        $min_dmg *= 5;
        $max_dmg *= 5;
    }
    $damage = handleDamage($expedition, $min_dmg, $max_dmg, $expedition->current_room->element);
    $remaining = number_format($expedition->player_cHP);
    $maxhp = number_format($expedition->player_mHP);
    $death_msg = (handleDeath($expedition)) ? " You have been slain." : "";
    if ($death_msg === "") {
        $exp_msg = "";
        $pact = new Pact($expedition->player);
        $exp_gain = (500 + (100 * $expedition->tier) + (25 * $expedition->luck)) * $adjuster;
        $exp_gain = calculate_exp_gain($exp_gain, $pact->pact_variant, $exp_msg);
        $expedition->player->player_exp += $exp_gain;
        $lvl_data = apply_level_up($expedition->player);
        $level_increase = $lvl_data[0];
        $lvl_msg = $lvl_data[1];
        $expedition->player->update_player_data();
    }
    return "You took {$damage} damage ({$remaining} / {$maxhp} HP).{$death_msg}";
}

function handleDamage($expedition, $min_dmg, $max_dmg, $element = -1) {
    // Base damage scaling by map tier
    $damage = mt_rand($min_dmg, $max_dmg) * $expedition->tier;
    // Apply elemental resistance if available
    if (isset($expedition->player->elemental_res) && $element !== -1) {
        $resist = $expedition->player->elemental_res[$element] ?? 0;
        $damage -= $damage * $resist;
    }
    // Apply generic mitigation
    if (isset($expedition->player->damage_mitigation)) {
        $mit = $expedition->player->damage_mitigation;
        $damage -= $damage * ($mit / 100);
    }
    $expedition->player_cHP -= intval($damage);
    if ($expedition->player_cHP < 0) $expedition->player_cHP = 0;
    return $damage;
}

function handleDeath($expedition) {
    global $verified_player_id;
    if ($expedition->player_cHP > 0) return false;
    $death_counter_query = "UPDATE MiscPlayerData SET deaths = deaths + 1 WHERE player_id = " . intval($verified_player_id);
    run_query($death_counter_query, false);
    $expedition->player_cHP = 0;
    return true;
}

function checkTrap($expedition, $room_type) {
    $trap_rates = [
        "trap_room" => 100,
        "basic_treasure" => max(0, 25 - $expedition->luck),
        "healing_room" => max(0, 25 - $expedition->luck),
        "greater_treasure"=> max(0, 15 - $expedition->luck)
    ];
    return (mt_rand(1, 100) <= $trap_rates[$room_type]);
}

function handleStealth($expedition, $action) {
    switch ($action) {
        case "basicStealth":  $adjuster = 1; break;
        case "eliteStealth":  $adjuster = 2; break;
        case "legendStealth": $adjuster = 3; break;
        default: $adjuster = 1; break;
    }
    $success_chance = intval(65 - ($adjuster * 15) + ($expedition->luck / $adjuster));
    if (mt_rand(1, 100) <= $success_chance) { return "You have successfully avoided the encounter."; }
    # Set the damge range
    $min_dmg = 100 * $adjuster;
    $max_dmg = 300 * $adjuster;
    if ($adjuster === 3) {
        $min_dmg *= 5;
        $max_dmg *= 5;
    }
    $damage = handleDamage($expedition, $min_dmg, $max_dmg, $expedition->current_room->element);
    $death = handleDeath($expedition) ? " You have been slain." : "";
    return "Your attempt fails! You take {$damage} damage while escaping.{$death}";
}

function handleTreasure($expedition, $action) {
    switch ($action) {
        case "basic_treasure":  $adjuster = 1; break;
        case "greater_treasure":  $adjuster = 2; break;
        default: $adjuster = 1; break;
    }
    if (checkTrap($expedition, $action)) {
        if ($adjuster === 2) {
            $trap_message = "You have fallen for the ultimate elder mimic's clever ruse!";
            $expedition->player_cHP = 0;
        } else {
            $damage = handleDamage($expedition, 100, 300, $expedition->current_room->element);
            $trap_message = "The mimic bites you dealing {$damage} damage.";
        }
        $death = handleDeath($expedition) ? " You have been slain." : "";
        return $trap_message . $death;
    }
    return openChest($expedition, $adjuster);
}

function openChest($expedition, $adjuster) {
    $is_greater = $adjuster == 2;
    // Fragment reward
    if ($expedition->tier > 4) {
        $quantity = getRewardQuantity($expedition->luck, $is_greater);
        $frag_tier = min(($expedition->tier - 4), 4);
        $reward_item = new BasicItem("Fragment{$frag_tier}");
        update_stock($expedition->player->player_id, $reward_item->item_id, $quantity);
        return $reward_item->item_id;
    }
    $new_item = expeditionCustomItem($expedition, $expedition->current_room->variant);
    return $new_item->item_id;
}

function handleEpitaph($expedition, $method) {
    $searchRate   = min(90, 50 + $expedition->luck);
    $decipherRate = min(90, 75 + $expedition->luck);
    $rate_check = rand(1, 100);
    if ($method === "searchEpitaph") {
        if ($rate_check <= $searchRate) {
            $new_item = expeditionCustomItem($expedition, "W");
            return $new_item->item_id;
        }
        return "Search Failed! You leave empty handed.";
    } else {
        if ($rate_check <= $decipherRate) {
            $luckAdjust = rand(1, 3);
            $expedition->luck += $luckAdjust;
            return "Decryption successful. Gained +" . $luckAdjust . " Luck";
        }
        return "Decryption Failed! You leave empty handed.";
    }
}

function handlePenetralia($expedition, $method){
    $base = rand(1000, 2000);
    $jackpotChance = 5 * $expedition->luck;
    $troveRate = 1;
    $room_adjuster = 1;
    if ($expedition->current_room->type === "jackpot_room") {
        $base = 10000;
        $jackpotChance *= 10;
        $troveRate = 5;
        $room_adjuster = 2;
    }
    $searchRate = min(90, 5 + (5 * $room_adjuster) + (5 * $expedition->luck));
    $rate_check = rand(1, 100);
    if($method === "Search"){
        if($rate_check > $searchRate){
            return "Search Failed! No amulets found!";
        }
        $new_item = expeditionCustomItem($expedition, "Y");
        return $new_item->item_id;
    } else {
        if ($expedition->tier > 4) { $base *= 2; }
        $rewardCoins = $base * $expedition->luck;
        $bonusCoins  = 0;
        if (rand(1, 1000) <= $jackpotChance) { $bonusCoins = rand(1000, 5000); }
        // Award trove or coins
        if (rand(1, 100) <= $troveRate) {
            $troveTier = generate_random_tier(1, 8, $expedition->luck);
            $item = new BasicItem("Trove{$troveTier}");
            update_stock($expedition->player->player_id, $item->item_id, 1);
            return $item->item_id;
        }
        $total = $rewardCoins + $bonusCoins;
        $expedition->player->player_coins += $total;
        $expedition->player->update_player_data();
        return "Acquired {$total} lotus coins!";
    }
}

function handleCrystal($expedition, $method){
    $variant = ($method === "crystalResonate") ? 0 : 1;
    $rate_resonate = $expedition->player->player_echelon * 10;
    $rate_search   = min(100, 30 + ($expedition->luck * 2));
    $successRate = ($variant === 0) ? $rate_resonate : $rate_search;
    $rate_check = rand(1,100);
    $luck_adjust = intval((($expedition->tier / 4) + 1) * ($variant + 1));
    if($rate_check > $successRate){
        $expedition->luck = max(1, $expedition->luck - $luck_adjust);
        return "Nothing Found! You leave empty handed. Lost {$luck_adjust} luck";
    }
    if($expedition->tier <= 5){
        $reward_id = "Scrap";
        update_stock($expedition->player->player_id, $reward_id, $luck_adjust);
        $expedition->luck += $luck_adjust;
        return $reward_id;
    }
    $prefix = (rand(1,1000) > $expedition->luck) ? "Fragment" : "Crystal";
    $suffix = min(4, $expedition->tier - 4);
    $reward_id = $prefix . $suffix;
    $reward_qty = ($prefix === "Crystal") ? 1 : $luck_adjust;
    update_stock($expedition->player->player_id, $reward_id, $reward_qty);
    $expedition->luck += $luck_adjust;
    return $reward_id;
}

function handleHeart($expedition, $method){
    $variant = ($method === "purifyHeart") ? 0 : 1;
    if (rand(1,100000) <= $expedition->luck) {
        $expedition->luck += 10;
        $item_id = ($variant === 0) ? "Pandora" : "Nephilim";
        update_stock($expedition->player->player_id, $item_id, 1);
        return $item_id;
    }
    $successRate = 50;
    if (rand(1,100) <= $successRate) {
        $expedition->luck += 1;
        $item_id = "Heart" . ($variant + 1);
        update_stock($expedition->player->player_id, $item_id, 1);
        return $item_id;
    }
    $expedition->luck = max(1, $expedition->luck - 1);
    return "Magical Overload! The creature's heart was destroyed. Luck -1";
}

function handleSelection($expedition, $action){
    $reward_items = explode(";", $expedition->current_room->variant);
    if ($action === "selectOption1") {
        update_stock($expedition->player->player_id, $reward_items[0], 1);
        return $reward_items[0];
    }
    if ($action === "selectOption2") {
        update_stock($expedition->player->player_id, $reward_items[1], 1);
        return $reward_items[1];
    }
    if (rand(1, 100) > min(75, ($expedition->luck * 5))) {
        return "Trap Triggered! " . handleTrap($expedition);
    }
    update_stock($expedition->player->player_id, $reward_items[0], 1);
    update_stock($expedition->player->player_id, $reward_items[1], 1);
    return "Received Both Items!";
}

function handleTrap($expedition) {
    global $trap_trigger_list_default, $trap_trigger_list_death;
    $death = "";
    // Fatal trap:
    if (rand(1, 100) <= max(0, (15 - $expedition->luck))) {
        $expedition->player_cHP = 0;
        $expedition->player->update_player_data();
        return $trap_trigger_list_death[$expedition->current_room->element]; 
    }
    // Teleport (elements 6,7,8)
    if ($expedition->current_room->element >= 6) {
        $expedition->room_num = rand(0, $expedition->length - 2);
        return $trap_trigger_list_default[$expedition->current_room->element];
    }
    $damage = handleDamage($expedition, 100, 300, $expedition->current_room->element);
    $death = handleDeath($expedition) ? " You have perished." : "";
    return $trap_trigger_list_default[$expedition->current_room->element] . "Took {$damage} damage." . $death;
}

function handleSanctuary($expedition, $action){
    $items = explode("/", $expedition->current_room->variant);
    $index = intval(substr($action, -1)) - 1;
    $item_id = $items[$index];
    if (str_starts_with($item_id, "Gemstone")) {
        update_stock($expedition->player->player_id, $item_id, 1);
        return $item_id;
    }   
    $qty = rand(1 + $expedition->luck, 10 + $expedition->luck);
    update_stock($expedition->player->player_id, $item_id, $qty);
    return $item_id;
}

function handlePact($expedition, $action){
    $parts = explode("/", $expedition->current_room->variant);
    $demonTier = intval($parts[0]);
    $bloodCost = intval($expedition->player_mHP * $demonTier * 0.1);
    $death = "";
    # Refuse Pact
    if ($action === "refusePact") {
        $unscathed = 10 + $expedition->luck;
        if (rand(1,100) < $unscathed) { return "You emerge unscathed."; }
        $damage = handleDamage($expedition, 100 * $demonTier, 300 * $demonTier, $expedition->current_room->element);
        $death = handleDeath($expedition) ? " You have been slain." : "";
        $expValue = 500 + (100 * $expedition->tier) + (25 * $expedition->luck);
        if ($death_msg === "") {
            $exp_msg = "";
            $pact = new Pact($expedition->player);
            $exp_gain = (500 + (100 * $expedition->tier) + (25 * $expedition->luck));
            $exp_gain = calculate_exp_gain($exp_gain, $pact->pact_variant, $exp_msg);
            $expedition->player->player_exp += $exp_gain;
            $lvl_data = apply_level_up($expedition->player);
            $level_increase = $lvl_data[0];
            $lvl_msg = $lvl_data[1];
            $expedition->player->update_player_data();
        }
        return "You took {$damage} damage." . $death;
    }
    # Forge Pact
    if ($expedition->player_cHP <= $bloodCost) {
        $expedition->player_cHP = 0;
        $expedition->player->update_player_data();
        return "The demon consumes you.";
    }
    $expedition->player_cHP -= $bloodCost;
    $expedition->player->player_pact = $parts[0] . ";" . $parts[1];
    $expedition->player->update_player_data();
    return "The pact has been forged.";
}

function handleStatue($expedition, $method){
    global $wrath_msg_list, $blessing_rewards;
    $prayRate = min(90, 25 + $expedition->luck);
    $destroyRate = min(90, 50 + $expedition->luck);
    $rate_check = rand(1,100);
    $result = "";
    if ($method === "prayStatue") {
        if ($rate_check <= $prayRate) {
            $tarot_info = getTarotByNumeral($expedition->current_room->deity);
            $reward_info = $blessing_rewards[$tarot_info['Type']][$tarot_info['Tier']] ?? $blessing_rewards[$tarot_info['Type']][0];
            if (rand(1,100) <= 20) {
                $reward_info[1] = "Essence{$expedition->current_room->deity}";
            }
            update_stock($expedition->player->player_id, $reward_info[1], 1);
            $expedition->luck += $reward_info[2];
            return $reward_info[1];
        }
        # Heal user if no reward item.
        $heal_total = intval($expedition->player_mHP * (rand($expedition->tier, 10) / 100));
        $expedition->player_cHP = min($expedition->player_mHP, $expedition->player_cHP + $heal_total);
        return "Health Restored: {$heal_total} HP";
    }
    # Wrath Outcome
    if (rand(1,100) <= 1) {
        $death_msg = "";
        $wrath_data = $wrath_msg_list[$expedition->current_room->deity];
        $wrath_msg = $wrath_data[0];
        $outcome = $wrath_data[1];
        if ($outcome === 1) {
            // Death outcome
            $expedition->player_cHP = 0;
            $expedition->player->update_player_data();
        } else if ($outcome === 2) {
            // Teleport outcome
            $expedition->room_num = rand(0, $expedition->length - 2);
        } else if ($outcome !== 0) {
            // Damage Outcome
            $damage = handleDamage($expedition, $outcome, $outcome, -1);
            $death_msg = (handleDeath($expedition)) ? " You have perished." : "";
        }
        return $wrath_msg . $death_msg;
    }
    if ($rate_check > $destroyRate) {
        return "Nothing happens.";
    }
    # Reward Outcome
    $reward_data = generate_random_item();
    update_stock($expedition->player->player_id, $reward_data[0][0], $reward_data[0][1]);
    return $reward_data[0];
}

function handleTrial($expedition, $action) {
    global $greed_cost_list;
    $index   = intval(substr($action, -1)) - 1;
    $reward = 2 * ($index + 1) - 1;
    # Trial of Offering
    if ($expedition->current_room->variant === "Offering") {
        $cost = intval(0.1 * $reward * $expedition->player_mHP);
        if ($cost < $expedition->player_cHP) {
            $expedition->player_cHP -= $cost;
            $expedition->player->update_player_data();
            $expedition->luck += $reward;
            return "Sacrificed {$cost} HP. Gained +{$reward} Luck.";
        }
    }
    # Trial of Greed
    if ($expedition->current_room->variant === "Greed") {
        $cost = $greed_cost_list[$index];
        if ($expedition->player->player_coins >= $cost) {
            $expedition->player->player_coins -= $cost;
            $expedition->player->update_player_data();
            $expedition->luck += $reward;
            return "Sacrificed {$cost} coins. Gained +{$reward} Luck.";
        }
    } else if ($expedition->current_room->variant === "Soul") {
        $cost = (1 + $index) * 100;
        if ($expedition->player->player_stamina >= $cost) {
            $expedition->player->player_stamina -= $cost;
            $expedition->player->update_player_data();
            $expedition->luck += $reward;
            return "Sacrificed {$cost} stamina. Gained +{$reward} Luck.";
        }
    }
    return "Cost could not be paid. Nothing gained.";
}

function handleShrine($expedition, $action) {
    global $boss_list, $shrine_dict;
    $index = intval(substr($action, -1)) - 1;
    $adjuster = ($expedition->current_room->variant === "") ? 1 : 2;
    $boss_num = array_search($expedition->current_room->deity, $boss_list);
    if ($boss_num === false) { $boss_num = 3; }
    $res_list = [
        $expedition->player->elemental_res[$shrine_dict[$boss_num][1]] ?? 0,
        $expedition->player->elemental_res[$shrine_dict[$boss_num][3]] ?? 0,
        $expedition->player->elemental_res[$expedition->current_room->element] ?? 0
    ];
    $success_rates = [
        min(100, intval($res_list[0] * 100) + 5),
        min(100, intval($res_list[1] * 100) + 5),
        min(100, intval($res_list[2] * 100) + 5)
    ];
    $reward_items = ["Gem{$boss_num}", "Unrefined{$boss_num}", null];
    if ($boss_num == 4) {
        $reward_items = ["Jewel4", "Token" . generate_random_tier(1,7,$expedition->luck), null];
    }
    if (rand(1,100) <= $success_rates[$index]) {
        # Element Rituals
        if ($reward_items[$index] !== null) {
            $item_id = $reward_items[$index];
            $quantity = getRewardQuantity($expedition->luck, ($adjuster === 2));
            update_stock($expedition->player->player_id, $item_id, $quantity);
            return $item_id;
        }
        # Chaos Ritual
        $gain = rand(1,3) * $adjuster;
        $expedition->luck += $gain;
        return "Gained +{$gain} Luck";
    }
    # Failure
    $damage = handleDamage($expedition, (100 * $adjuster), (300 * $adjuster), $expedition->current_room->element);
    $death_msg = (handleDeath($expedition)) ? " You have perished." : "";
    $expedition->luck = max(1, $expedition->luck - $adjuster);
    return "Unable to hold out, you took {$damage} damage. Lost -{$adjuster} Luck." . $death_msg;
}

# Helper Functions
function expeditionCustomItem($expedition, $item_type){
    $reward_tier = generate_random_tier(1, 4, $expedition->luck);
    $new_item = new CustomItem($expedition->player->player_id, $item_type, $reward_tier, '', false, true);
    $new_item->set_item_name();
    $new_item->saveChanges('new');
    return $new_item;
}

function getRewardQuantity($luck = 1, $is_lucky = false) {
    $reward_probabilities = [50 => 1, 30 => 2, 15 => 3, 5 => 4];
    $num_attempts = 1 + ($is_lucky ? 1 : 0) + intval($luck / 10);
    $highest_quantity = 0;
    for ($i = 0; $i < $num_attempts; $i++) {
        $check = mt_rand(1, 100);
        $cumulative = 0;
        foreach ($reward_probabilities as $prob => $qty) {
            $cumulative += $prob;
            if ($check <= $cumulative) {
                if ($qty > $highest_quantity) {
                    $highest_quantity = $qty;
                }
                break;
            }
        }
    }
    return $highest_quantity;
}

function checkEssence($selected_item, $pool_tier) {
    global $tarot_data;
    if ($selected_item != "ESS") { return $selected_item; }
    $valid_tarots = [];
    foreach ($tarot_data as $card_number => $data) {
        if ($data['tier'] == $pool_tier) { $valid_tarots[] = $card_number; }
    } 
    if (empty($valid_tarots)) { return "Essence0"; }
    $essence_numeral = number_to_roman($valid_tarots[array_rand($valid_tarots)]);
    return "Essence{$essence_numeral}";
}

?>