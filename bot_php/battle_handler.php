<?php
include_once('./bot_php/shared_methods.php');

$skill_name_list = [
    "Knight" => ["Destructive Cleave", "Merciless Blade", "Ruinous Slash", "Destiny Divider"],
    "Ranger" => ["Viper Shot", "Comet Arrow", "Meteor Volley", "Blitz Barrage"],
    "Assassin" => ["Wound Carver", "Exploit Injury", "Eternal Laceration", "Blood Recursion"],
    "Mage" => ["Magical Bolt", "Aether Blast", "Mystic Maelstrom", "Astral Convergence"],
    "Weaver" => ["Power Stitch", "Infused Slice", "Multithreading", "Reality Fabricator"],
    "Rider" => ["Valiant Charge", "Surge Dash", "Mounted Onslaught", "Chaos Rampage"],
    "Summoner" => ["Savage Blows", "Moonlit Hunt", "Berserk Frenzy", "Synchronized Wrath"]
];
$shared_ruler_skills = [["King's Strike", "basic"], ["Sealing Chains", "basic"], ["Forbidden Command", "basic"], ["Heaven's Rule", "basic"]];
$boss_attack_dict = [
    "Dragon" => [["[ELEMENT] Claw Slash", "basic"], ["[ELEMENT] Wing Blast", "basic"], ["Amplified [ELEMENT] Breath", "basic"]],
    "Demon" => [["Dark Flame", "basic"], ["Abyss Bolt", "basic"], ["Blood Deluge", "basic"]],
    "Paragon" => [["Essence Strike", "basic"], ["Essence Shock", "basic"], ["Essence Destruction", "basic"]],
    "Arbiter" => [["Decree Of Pain", "basic"], ["Revoke Control", "basic"], ["Invoke Authority", "basic"]],
    "Pandora, The Celestial" => [["Cosmic Pulse","basic"], ["Constellation Blast","basic"], ["Starfall","ultimate"], ["Collapsing Star Hammer","signature"]],
    "Oblivia, The Void" => [["Gravity Crush","basic"], ["Void Grasp","basic"], ["Disintegrate","basic"], ["Void Impaler","ultimate"], ["Black Hole","signature"]],
    "Akasha, The Infinite" => [["Overwhelm","basic"], ["Subjugation","basic"], ["Manifest Armaments","basic"], 
    ["Blade Recursion","ultimate"], ["Exceed Infinity","signature"]],
    "Astratha, The Dimensional" => [["Rift Claws","basic"], ["Quantum Wing Blast","basic"], ["Amplified Dimension Breath","basic"], ["Starlight Beam","signature"]],
    "Tyra, The Behemoth" => [["Power Slam","basic"], ["Horn Thrust","basic"], ["Tectonic Rumble","basic"], ["Tyrant's Thunderbolt","signature"]],
    "Diabla, The Primordial" => [["Elemental Divergence","basic"], ["Magma Blizzard","basic"], ["Pyroclasmic Rain","basic"], ["Glacial Meltdown","signature"]],
    "Aurora, The Fortress" => [["Living Fortress","basic"], ["Brilliant Bastion","basic"], ["Radiant Aura","basic"], ["Luminous Cascade","signature"]],
    "Eleuia, The Wish" => [["Twisted Dream","basic"], ["Desire's Echo","basic"], ["Fervent Wish","basic"], 
    ["Heartfelt Tears","ultimate"], ["Shape Miracle","ultimate"], ["Shatter All Hope","signature"]],
    "Kazyth, The Lifeblood" => [["Natural Order","basic"], ["Life Absorption","basic"], ["Spirit Reclamation","basic"], ["Form Distortion","signature"]],
    "Vexia, The Scribe" => [["Vengeful Inscription","basic"], ["Create Rule","basic"], ["Words To Life","basic"], 
    ["Alter Chronicle","ultimate"], ["Record Erasure","signature"]],
    "Fleur, The Oracle" => [["Vine Lash","basic"], ["Falling Petals","basic"], ["Thorn's Embrace","basic"], 
    ["Severed Fate","ultimate"], ["Withering Future","ultimate"], ["Blossoming Death","signature"]],
    "Yubelle, The Adjudicator" => [["Tainted Smite","basic"], ["Distorted Gavel","basic"], ["Alter Verdict","basic"], 
    ["Counterfeit Retribution","ultimate"], ["Weight Of Sin","ultimate"], ["Scales Of Judgement","ultimate"], ["White Abyss","signature"]],
    "Nephilim, Incarnate of the Divine Lotus" => [["Lotus Slash","basic"], ["Propagate Ruin","basic"], ["Divine Purgation","basic"], 
    ["Chaos Bloom","ultimate"], ["Sacred Revelation","ultimate"], ["Eye Of The Annihilator","ultimate"], ["Nightmare Saber","ultimate"], 
    ["Fabricate Apotheosis","signature"]],
    "Geb, Sacred Ruler of Sin" => array_merge($shared_ruler_skills, [
        ["Guilt Gorger", "ultimate"], ["Coil Crush", "ultimate"], ["Sin Swallower", "ultimate"], ["Final Confession", "signature"]]),
    "Tiamat, Sacred Ruler of Fury" => array_merge($shared_ruler_skills, [
        ["Discord Howl", "ultimate"], ["Chaos Ravager", "ultimate"], ["Pandemonium Fury", "ultimate"], ["True Nemesis", "signature"]]),
    "Veritas, Sacred Ruler of Prophecy" => array_merge($shared_ruler_skills, [
        ["Reverse Reclamation", "ultimate"], ["Destiny's Dream", "ultimate"], ["Eye of Revelation", "ultimate"], ["Beyond Fate", "signature"]]),
    "Alaric, Sacred Ruler of Totality" => array_merge($shared_ruler_skills, [
        ["Unmaking", "ultimate"], ["Transcendence", "ultimate"], ["Total Collapse", "ultimate"], ["All To Nothing", "signature"]])
];

$boss_attack_exceptions = array_keys($boss_attack_dict);
$skill_multiplier_list = [1, 2, 3, 5, 7, 10, 15, 20, 50];
$skill_multiplier_list_high = [4, 6, 8, 10, 15, 25, 50, 99, 999];
$element_status_list = [null, null, "paralyzed", "petrified", null, "frozen", null, "blinded", "disoriented"];

class CombatTracker {
    public int $player_cHP = 0;
    public int $current_mana = 0;
    public int $mana_limit = 0;
    public int $charges = 0;
    public int $remaining_hits = 0;
    public string $total_dps = '0';
    public string $highest_damage = '0.0';
    public int $recovery = 0;
    public int $hp_regen = 0;
    public int $total_cycles = 0;
    public int $stun_cycles = 0;
    public string $stun_status = '';
    public string $boss_stun_status = '';
    public int $solar_stacks = 0;
    public int $time_lock = 0;
    public string $time_damage = '0.0';
    public float $bleed_tracker = 0.0;
}

function run_boss($verified_player_id, $boss_calltype, $magnitude) {
    global $battleItemCost, $spawn_dict, $boss_list, $boss_tier_dict;
    $player_profile = get_player_by_id($verified_player_id);
    $player_profile->get_player_multipliers();
    // Handle Costs
    $requires_stamina = in_array($boss_calltype, ["Any", "Fortress", "Dragon", "Demon", "Paragon", "Arbiter"]);
    $has_stamina = !$requires_stamina || $player_profile->player_stamina >= 200;
    $required_item = $battleItemCost[$boss_calltype] ?? null;
    $has_item = true;
    if ($required_item) {
        $item_result = get_inventory_by_player_id($verified_player_id, $required_item);
        $has_item = $item_result["success"] && $item_result["item"]["item_qty"] >= 1;
    }
    if (!$has_stamina || !$has_item) {
        $response = ["success" => false, "message" => "Insufficient stamina/materials."];
        return $response;
    }
    if ($requires_stamina) $player_profile->player_stamina -= 200;
    $player_profile->update_player_data();
    if ($required_item) {
        $query = "UPDATE BasicInventory SET item_qty = item_qty - 1 WHERE player_id = $verified_player_id AND item_id = '$required_item' AND item_qty >= 1";
        run_query($query, false);
    }
    // Handle Boss
    $boss_type = $boss_calltype;
    if ($boss_type === "Any") {
        $max_spawn_index = 0;
        foreach ($spawn_dict as $key => $value) {
            if ($key <= $player_profile->player_echelon && $value > $max_spawn_index) { $max_spawn_index = $value; }
        }
        $boss_type = $boss_list[rand(0, $max_spawn_index)];
    }
    $boss_level = $player_profile->player_level;
    if (strpos($boss_type, "Palace")) {
        $boss_level = ($boss_type == "Palace1") ? 300 : (($boss_type == "Palace2") ? 600 : 999);
    }
    $boss_tier = $boss_tier_dict[$boss_type];
    if ($boss_tier == 0) {
        $boss_tier = getBossTier($boss_type);
    }
    $boss_profile = makeBoss($verified_player_id, $boss_type, $boss_tier, $boss_level, $magnitude);
    $boss_row = $boss_profile->setBoss($verified_player_id);
    $response = $player_profile
        ? ["success" => true, "player" => $player_profile, "boss" => $boss_profile, "boss_image" => $boss_profile->boss_image, "encounter_id" => $boss_row['encounter_id']]
        : ["success" => false, "message" => "Player not found"];
    return $response;
}

function run_cycle($verified_player_id, $encounter_id) {
    $query = "SELECT * FROM OnlineBosses WHERE player_id = $verified_player_id LIMIT 1";
    $rows = run_query($query);
    if (!$rows) {
        $response = ["success" => false, "message" => "No active boss."];
        return $response;
    }
    $boss_row = $rows[0];
    $last_time = strtotime($boss_row["time_stamp"]);
    $current_time = time();
    $elapsed = $current_time - $last_time;
    if ($boss_row['encounter_id'] !== $encounter_id) {
        return ["success" => false, "message" => "Encounter mismatch. Voided encounter."];
    }
    if ($elapsed < 60) {
        return ["success" => false, "message" => "Cycle time error intercept."];
    }
    $raw_cycle_data = process_cycle($verified_player_id, $boss_row, $encounter_id);
    if ($raw_cycle_data[2] != "continue") {
        clear_boss($verified_player_id);
    }
    return ["success" => true, "cycle_data" => $raw_cycle_data[0], "combat_tracker" => $raw_cycle_data[1], 
        "battle_status" => $raw_cycle_data[2], "player" => $raw_cycle_data[3], "boss" => $raw_cycle_data[4]];
}

function process_cycle($verified_player_id, $boss_row, $encounter_id) {
    $battle_status = "continue";
    $boss_profile = build_boss_from_row($boss_row);
    $player_profile = get_player_by_id($verified_player_id);
    $player_profile->get_player_multipliers();
    $combat_tracker = get_combat_tracker($player_profile, $boss_row);
    $combat_tracker->total_cycles++;
    $action_rows = [];
    $action_rows = handle_boss_actions($player_profile, $boss_profile, $combat_tracker, $action_rows);
    if ($combat_tracker->player_cHP <= 0 && $combat_tracker->stun_status !== "stunned") {
        return [$action_rows, $combat_tracker, "player_dead"];
    }
    $action_rows = handle_player_actions($player_profile, $boss_profile, $combat_tracker, $action_rows);
    if ($boss_profile->boss_cHP <= 0) { 
        $boss_profile ->boss_cHP = 0;
        $battle_status = "boss_dead"; 
    }
    // Handle Tracker
    $total_damage = 0;
    foreach ($action_rows as &$row) {
        if (!str_contains($row['action_type'], 'boss') && !str_contains($row['action_type'], 'stun') && !str_contains($row['action_type'], 'regen')) {
            $dmg = (string) $row['damage_value'];
            $total_damage = big_add($total_damage, $dmg);
            if (big_cmp($dmg, $combat_tracker->highest_damage) > 0) {
                $combat_tracker->highest_damage = $dmg;
            }
        }
        $row['damage_value'] = big_round($row['damage_value']);
    }
    unset($row);
    $combat_tracker->total_dps = big_add($combat_tracker->total_dps, $total_damage);
    update_boss_details($boss_profile, $combat_tracker, $encounter_id);
    return [$action_rows, $combat_tracker, $battle_status, $player_profile, $boss_profile];
}

function get_combat_tracker($player, $boss_row) {
    $tracker = new CombatTracker();
    if ($boss_row['combat_tracker'] === '') {
        $tracker->player_cHP = $player->player_mHP;
        $tracker->current_mana = $player->start_mana;
        $tracker->mana_limit = $player->mana_limit;
        $tracker->recovery = $player->recovery;
        $tracker->hp_regen = (int) ($player->hp_regen * $player->player_mHP);
    } else {
        $values = explode(';', $boss_row['combat_tracker']);
        $tracker->player_cHP = (int) $values[0];
        $tracker->current_mana = (int) $values[1];
        $tracker->mana_limit = (int) $values[2];
        $tracker->charges = (int) $values[3];
        $tracker->remaining_hits = (float) $values[4];
        $tracker->total_dps = (string) $values[5];
        $tracker->highest_damage = (string) $values[6];
        $tracker->recovery = (int) $values[7];
        $tracker->hp_regen = (int) $values[8];
        $tracker->total_cycles = (int) $values[9];
        $tracker->stun_cycles = (int) $values[10];
        $tracker->stun_status = $values[11];
        $tracker->boss_stun_status = $values[12];
        $tracker->solar_stacks = (int) $values[13];
        $tracker->time_lock = (int) $values[14];
        $tracker->time_damage = (string) $values[15];
        $tracker->bleed_tracker = (float) $values[16];    
    }
    return $tracker;
}

function update_boss_details($boss, $tracker, $encounter_id) {
    $tracker_data = implode(';', [
        $tracker->player_cHP,
        $tracker->current_mana,
        $tracker->mana_limit,
        $tracker->charges,
        $tracker->remaining_hits,
        $tracker->total_dps,
        $tracker->highest_damage,
        $tracker->recovery,
        $tracker->hp_regen,
        $tracker->total_cycles,
        $tracker->stun_cycles,
        $tracker->stun_status,
        $tracker->boss_stun_status,
        $tracker->solar_stacks,
        $tracker->time_lock,
        $tracker->time_damage,
        $tracker->bleed_tracker
    ]);
    $updated_boss_data = "{$boss->boss_level};{$boss->boss_tier};{$boss->boss_cHP};{$boss->boss_mHP};{$boss->boss_element}";
    $now = date('Y-m-d H:i:s');
    $query = "UPDATE OnlineBosses 
              SET boss_data = '$updated_boss_data', combat_tracker = '$tracker_data', time_stamp = '$now' 
              WHERE encounter_id = $encounter_id";
    run_query($query, false);
}

function handle_boss_actions($player, &$boss, &$tracker, $rows) {
    global $boss_attack_dict, $skill_multiplier_list, $skill_multiplier_list_high, $element_names;
    if ($tracker->boss_stun_status != '') {
        $tracker->boss_stun_status = '';
        return $rows; 
    }
    $boss_element = ($boss->boss_element !== 9) ? $boss->boss_element : rand(0, 8);
    // Handle Boss Regen
    $boss_regen = 0;
    if ($boss->boss_type_num >= 3) {
        $regen_percent = 0.001 * $boss->boss_tier;
        $boss_regen = big_mul($boss->boss_mHP, $regen_percent);
        $boss->boss_cHP = big_cmp(big_add($boss->boss_cHP, $boss_regen), $boss->boss_mHP) > 0
            ? $boss->boss_mHP : big_add($boss->boss_cHP, $boss_regen);
        $rows[] = ["action_type" => "boss_regen", "action_name" => "Boss: Regenerate", "damage_value" => $boss_regen, "new_hp" => $boss->boss_cHP];
    }
    // Handle Boss Attack Skill
    if ($boss->boss_type_num == 0) { return $rows; }
    $attack_list = isset($boss_attack_dict[$boss->boss_type]) ? $boss_attack_dict[$boss->boss_type] : ["boss_skill_undefined"];
    foreach ($boss_attack_dict as $key => $list) {
        if (str_contains($boss->boss_name, $key)) {
            $attack_list = $list;
            break;
        }
    }
    $skill_index = rand(0, count($attack_list) - 1);
    $skill_name_raw = $attack_list[$skill_index][0];
    $skill_class = $attack_list[$skill_index][1];
    $skill_name = str_replace("[ELEMENT]", $element_names[$boss_element], $skill_name_raw);
    $bonus = ($boss->boss_level < 500) ? $skill_multiplier_list[$skill_index] : $skill_multiplier_list_high[$skill_index];
    $bypass1 = ($skill_class === "signature");
    $bypass2 = ($skill_class === "ultimate");
    $base = match($skill_class) {
        "ultimate" => [100, 100],
        default => [25, 50]
    };
    // Handle Enrage
    if ($boss->boss_type_num >= 2 && $boss->boss_cHP <= ($boss->boss_mHP / 2)) {
        $base[0] *= 2;
        $base[1] *= 2;
    }
    $damage_set = [$base[0] * $boss->boss_level * $bonus, $base[1] * $boss->boss_level * $bonus];
    $damage_set = handle_evasions($player->block, $player->dodge, $damage_set, $bypass1, $bypass2);
    $damage = take_combat_damage($player, $tracker, $damage_set, $boss_element);
    $rows[] = ["action_type" => "boss_skill_" . $skill_class . " element_" . $element_names[$boss_element], "action_name" => $skill_name, "damage_value" => $damage, "new_hp" => $tracker->player_cHP];
    return $rows;
}

function handle_evasions($block_rate, $dodge_rate, $damage_set, $bypass1 = false, $bypass2 = false) {
    if (!$bypass1 && !$bypass2 && rand(1, 100) <= $dodge_rate * 100) {
        return [0, 0];
    } elseif (!$bypass2 && rand(1, 100) <= $block_rate * 100) {
        return [(int) ($damage_set[0] / 2), (int) ($damage_set[1] / 2)];
    }
    return $damage_set;
}

function take_combat_damage($player, &$tracker, $damage_set, $element, $bypass_immortal = false, $no_trigger = false) {
    global $element_status_list;
    $damage = rand($damage_set[0], $damage_set[1]);
    // Elemental resistance
    $resist = 0;
    if ($element !== -1) {
        $resist = $player->elemental_res[$element];
        if (!$no_trigger && $tracker->stun_status !== "stunned" && rand(1, 100) <= 1) {
            $status = $element_status_list[$element];
            if ($status !== null) {
                $tracker->stun_status = $status;
                $tracker->stun_cycles += 1;
            }
        }
    }
    $damage -= $damage * $resist;
    $damage = (int) ($damage - ($damage * $player->damage_mitigation * 0.01));
    $tracker->player_cHP -= $damage;
    if ($tracker->player_cHP > 0) {
        return $damage;
    }
    if ($player->immortal && !$bypass_immortal) {
        $tracker->player_cHP = 1;
        return $damage;
    }
    if ($tracker->recovery > 0) {
        $tracker->player_cHP = 0;
        $tracker->recovery -= 1;
        $tracker->stun_status = "stunned";
        $tracker->stun_cycles = max(1, 10 - $player->recovery);
        return $damage;
    }
    return $damage;
}


function handle_player_actions($player, &$boss, &$tracker, $rows) {
    $weapon = read_custom_item($player->player_equipped[0]);
    // Player Regen
    $regen = ($tracker->player_cHP > 0) ? $tracker->hp_regen : 0;
    if ($regen > 0) {
        $tracker->player_cHP = min($player->player_mHP, $tracker->player_cHP + $regen);
        $rows[] = ["action_type" => "player_regen", "action_name" => "Player: Regenerate", "damage_value" => $regen, "new_hp" => $tracker->player_cHP];
    }
    // Player Actions
    $combo = 1;
    $hits = $player->attack_speed;
    $tracker->remaining_hits += $player->attack_speed - $hits;
    while ($tracker->remaining_hits >= 1) {
        $hits += 1;
        $tracker->remaining_hits -= 1;
    }
    // Player Basic Hits
    for ($i = 0; $i < $hits; $i++) {
        $rows = array_merge($rows, handle_regular_skill($player, $boss, $tracker, $combo, $weapon));
        $combo += 1;
        if ($tracker->solar_stacks >= 35) {
            $rows = array_merge($rows, trigger_flare($player, $boss, $tracker));
        }        
        // Player Ultimate
        $tracker->charges += 1;
        $rows = array_merge($rows, handle_ultimate($player, $boss, $tracker, $combo, $weapon));
        if ($tracker->solar_stacks >= 35) {
            $rows = array_merge($rows, trigger_flare($player, $boss, $tracker));
        }        
    }
    // Player Basic Bleeds
    $rows = array_merge($rows, handle_bleed($player, $boss, $tracker, $weapon, false));
    return $rows;
}

function handle_regular_skill($player, &$boss, &$tracker, $combo_count, $weapon) {
    global $skill_name_list;
    $rows = [];
    $combo_mult = (1 + ($player->combo_mult * $combo_count)) * (1 + $player->combo_pen);
    $ult_mult = (1 + $player->ultimate_mult) * (1 + $player->ultimate_pen);
    $tier_index = ($combo_count < 3) ? 0 : (($combo_count < 5) ? 1 : 2);
    $skill_bonus = $player->skill_damage_bonus[$tier_index];
    $skill_name = ($player->aqua_points >= 100)
        ? ["Sea of Subjugation", "Ocean of Oppression", "Deluge of Domination"][$tier_index]
        : $skill_name_list[$player->player_class][$tier_index];
    get_player_initial_damage($player, $boss, $weapon, $tracker);
    $damage = (int) ($player->total_damage * $combo_mult * (0.5 + $skill_bonus));
    if ($player->unique_glyph_ability[3]) {
        $damage = (int) ($player->total_damage * $ult_mult);
    }
    $tracker->charges += 1 + $player->charge_generation;
    $tracker->solar_stacks += ($player->flare_type !== "") ? 1 : 0;
    $triggers = apply_triggers($player, $tracker, $damage);
    limit_and_calc($boss, $damage);
    $rows[] = ["action_type" => "skill_" . $tier_index . ($triggers ? " " . implode(" ", $triggers) : ""), 
        "action_name" => $skill_name, "damage_value" => $damage, "new_hp" => $boss->boss_cHP];
    return $rows;
}

function handle_bleed($player, &$boss, &$tracker, $weapon, $is_ultimate = false) {
    if ($player->appli["Bleed"] <= 0 || $boss->boss_cHP <= 0) return [];
    $bleed_dict = [1 => "Single", 2 => "Double", 3 => "Triple", 4 => "Quadra", 5 => "Penta"];
    $rows = [];
    $count = $player->appli["Bleed"];
    $count_label = ($count > 5) ? "Zenith" : ($bleed_dict[$count] ?? $count);
    [$type, $label_prefix, $base] = $is_ultimate ? ["ultimate ", "Sanguine", 1.5] : ["cyclic ", "Blood", 0.75];
    get_player_initial_damage($player, $boss, $weapon, $tracker);
    $tracker->bleed_tracker += 0.05 * $count;
    $tracker->bleed_tracker = min(1, $tracker->bleed_tracker);
    $damage = $player->total_damage * $tracker->bleed_tracker * (1 + $player->bleed_mult) * (1 + $player->bleed_pen) * (1 + $count);
    // Hyperbleed check
    $triggers = ["bleed"];
    if (rand(1, 100) <= $player->trigger_rate["Hyperbleed"]) {
        $damage *= (1 + $player->bleed_mult);
        $triggers = ["hyperbleed"];
    }
    $triggers = array_merge($triggers, apply_triggers($player, $tracker, $damage, true));
    limit_and_calc($boss, $damage);
    $rows[] = ["action_type" => $type . ($triggers ? " " . implode(" ", $triggers) : ""), "action_name" => "$label_prefix Rupture [$count_label]", "damage_value" => $damage, "new_hp" => $boss->boss_cHP];
    return $rows;
}

function handle_ultimate($player, &$boss, &$tracker, $combo_count, $weapon) {
    global $skill_name_list;
    $rows = [];
    if ($tracker->charges < 20 || $boss->boss_cHP <= 0) return [];
    $tracker->charges -= 20;
    $combo_mult = (1 + ($player->combo_mult * $combo_count)) * (1 + $player->combo_pen);
    $ult_mult = (1 + $player->ultimate_mult) * (1 + $player->ultimate_pen);
    $skill_bonus = $player->skill_damage_bonus[3];
    $skill_name = $player->aqua_points >= 100 ? "Tides of Annihilation" : $skill_name_list[$player->player_class][3];
    get_player_initial_damage($player, $boss, $weapon, $tracker);
    $damage = (int) ($player->total_damage * $combo_mult * $ult_mult * (2 + $skill_bonus));
    $triggers = apply_triggers($player, $tracker, $damage);
    limit_and_calc($boss, $damage);
    $rows[] = ["action_type" => "ultimate" . ($triggers ? " " . implode(" ", $triggers) : ""), "action_name" => $skill_name, 
        "damage_value" => $damage, "new_hp" => $boss->boss_cHP];
    // Ultimate Bleed Proc
    $rows = array_merge($rows, handle_bleed($player, $boss, $tracker, $weapon, true));
    return $rows;
}

function limit_and_calc($boss, &$damage) {
    $damage = (string)$damage;
    if ($boss->damage_cap !== '-1' && big_cmp($damage, $boss->damage_cap) > 0) {
        $damage = (string)$boss->damage_cap;
    }
    $boss->boss_cHP = big_sub($boss->boss_cHP, $damage);
    if (big_cmp($boss->boss_cHP, '0') < 0) {
        $boss->boss_cHP = '0';
    }
}


function get_player_initial_damage($player, $boss, $weapon, &$tracker) {
    global $sovereign_item_list;
    if (!$weapon || in_array($weapon->item_base_type, $sovereign_item_list)) {
        $player->player_damage_min = $player->player_damage_max;
    }
    $base_damage = rand($player->player_damage_min, $player->player_damage_max);
    $adjusted = big_mul($base_damage, big_mul(big_add(1, $player->total_class_mult), big_add(1, $player->final_damage)));
    $player->total_damage = boss_adjustments($player, $boss, $weapon, $adjusted, $tracker);
}

function boss_adjustments($player, $boss, $weapon, $player_damage, &$tracker) {
    global $element_status_list;
    $damage = $player_damage;
    // Bane multiplier (skip if boss_type is "Ruler")
    if ($boss->boss_type !== "Ruler") {
        $bane_mult = 1 + ($player->banes[$boss->boss_type_num - 1] ?? 0);
        $damage = big_mul($damage, $bane_mult);
    }
    // Type defence
    $def_mult = boss_defences("", $player, $boss, -1, $weapon) + $player->defence_pen;
    $damage = big_mul($damage, $def_mult);
    // Elemental calculation
    $highest = 0;
    $player->elemental_damage = array_fill(0, 9, 0);
    $elements = limit_elements($player, $weapon);
    foreach ($elements as $idx => $active) {
        if ($active === 1) {
            $resist_multi = boss_defences("Element", $player, $boss, $idx, $weapon) + $player->resist_pen;
            $ele_mult = 1 + $player->elemental_mult[$idx];
            $pen_mult = 1 + $player->elemental_pen[$idx];
            $curse_mult = 1 + $boss->curse_debuffs[$idx];
            $conv_mult = $player->elemental_conversion[$idx];
            $value = $damage;
            $value = big_mul($value, $ele_mult);
            $value = big_mul($value, $resist_multi);
            $value = big_mul($value, $pen_mult);
            $value = big_mul($value, $curse_mult);
            $value = big_mul($value, $conv_mult);
            $player->elemental_damage[$idx] = $value;
            if (big_cmp($value, $player->elemental_damage[$highest]) > 0) {
                $highest = $idx;
            }            
        }
    }
    $stun_status = $element_status_list[$highest] ?? null;
    if ($stun_status !== null && rand(1, 100) <= $player->trigger_rate["Status"]) {
        $tracker->stun_status = $stun_status;
        $tracker->stun_cycles += 1;
    }
    $total_elemental = '0';
    foreach ($player->elemental_damage as $val) {
        $total_elemental = big_add($total_elemental, $val);
    }
    $mitigation = boss_true_mitigation($boss->boss_level);
    return big_mul($total_elemental, $mitigation);
}

function boss_defences($method, $player, $boss, $location, $weapon) {
    global $class_names;
    $mult = 1 - (0.05 * $boss->boss_tier - 1);
    if ($method === "Element" && $boss->boss_eleweak[$location] !== 1) {
        return $mult;
    }
    $c_idx = array_search($player->player_class, $class_names);
    $w_idx = array_search($weapon->item_damage_type, $class_names);
    if ($method !== "Element" && $boss->boss_typeweak[$c_idx] !== 1 && $boss->boss_typeweak[$w_idx] !== 1) {
        return $mult;
    }
    return 1.1;
}

function boss_true_mitigation($level) {
    $a = 1; $b = 0.5; $c = 0.1;
    if ($level <= 99) return $a - ($a - $b) * $level / 99;
    return $b - ($b - $c) * ($level - 100) / 899;
}

function apply_triggers($player, &$tracker, &$damage, $is_bleed = false) {
    $triggers = [];
    $damage = (string)$damage;
    // Bloom Triggers
    if (rand(1, 100) <= round($player->trigger_rate["Bloom"] * 100)) {
        $damage = big_mul($damage, $player->bloom_mult);
        $triggers[] = "bloom";
        if (rand(1, 100) <= round($player->spec_conv["Heavenly"] * 10)) {
            $damage = big_mul($damage, '10');
            $triggers[] = "heavenly";
        }
    } elseif (rand(1, 100) <= round($player->spec_conv["Stygian"] * 100)) {
        $bloom_bonus = $player->bloom_mult * 3; // seperation mandatory for bcmath
        $damage = big_mul($damage, $bloom_bonus);
        $triggers[] = "stygian";
    } elseif (rand(1, 100) <= round($player->spec_conv["Calamity"] * 100)) {
        $damage = big_mul($damage, '9.99');
        $triggers[] = "calamity";
    }
    if ($is_bleed) {
        return $triggers;
    }
    // Critical Triggers
    $crit_roll = rand(1, 100);
    $crit_bonus = 1 + $player->critical_mult; // seperation mandatory for bcmath
    $crit_pen_bonus = 1 + $player->critical_pen; // seperation mandatory for bcmath
    if ($crit_roll <= $player->trigger_rate["Fractal"]) {
        $ele_count = array_sum(limit_elements($player, read_custom_item($player->player_equipped[0])));
        $damage = big_mul($damage, $ele_count);
        $triggers[] = "fractal";
    } elseif ($crit_roll <= $player->trigger_rate["Critical"]) {
        $damage = big_mul($damage, $crit_bonus);
        $triggers[] = "critical";
        if (rand(1, 100) <= $player->trigger_rate["Omega"]) {
            $damage = big_mul($damage, $crit_bonus);
            $triggers[] = "omega";
        }
        $damage = big_mul($damage, $crit_pen_bonus);
    }
    // Mana Triggers
    if ($tracker->current_mana > 0) {
        $tracker->current_mana -= 1;
    } elseif (!$player->mana_shatter) {
        $tracker->current_mana = $tracker->mana_limit;
        $damage = big_mul($damage, 1 + $player->mana_mult);
        $triggers[] = "mana_burst";
    } else {
        $tracker->current_mana = max($tracker->mana_limit * -1, $tracker->current_mana);
        $shatter_mult = 1 + $player->mana_mult + ($tracker->current_mana * -1);
        $damage = big_mul($damage, $shatter_mult);
        $triggers[] = "mana_shatter";
    }
    // Time Triggers
    if ($tracker->time_lock === 0) {
        if (rand(1, 100) <= $player->trigger_rate["Temporal"]) {
            $tracker->time_lock = $player->appli["Temporal"];
            $triggers[] = "time_lock";
        }
    } elseif ($tracker->time_lock > 0) {
        $tracker->time_lock -= 1;
        $tracker->time_damage = big_add($tracker->time_damage, $damage);
        $damage = '0';
        $triggers[] = "locked";
        if ($tracker->time_lock === 0 || $player->unique_glyph_ability[7]) {
            $damage = big_mul($tracker->time_damage, $player->temporal_mult);
            $tracker->time_damage = '0.0';
            $triggers[] = "time_shatter";
        }
    }
    return $triggers;
}

function trigger_flare($player, &$boss, &$tracker) {
    if ($tracker->solar_stacks < 35 || $boss->boss_cHP <= 0) return [];
    $tracker->solar_stacks = 0;
    $flare_rate = ($player->flare_type === "Solar") ? 0.10 : 0.25;
    $damage = big_mul($boss->boss_cHP, $flare_rate);
    limit_and_calc($boss, $damage);
    return [["action_type" => "flare", "action_name" => $player->flare_type . " Flare", 
        "damage_value" => $damage, "new_hp" => $boss->boss_cHP]];
}


?>