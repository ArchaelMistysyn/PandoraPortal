<?php
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

$boss_loot_dict = [
    "All" => [
        [0, "Chest", 25], [0, "Matrix", 15], [0, "Crystal1", 1], [0, "Catalyst", 1],
        [0, "Skull4", 0.005], [0, "Skull3", 0.05], [0, "Skull2", 0.5], [0, "Skull1", 5],
        [0, "Hammer", 25], [0, "Pearl", 15], [0, "Heart1", 2], [0, "Heart2", 1],
        [1, "Ore1", 25], [2, "Ore2", 25], [3, "Ore3", 25], [4, "Ore4", 25],
        [1, "Potion1", 5], [2, "Potion2", 5], [3, "Potion3", 5], [4, "Potion4", 5],
        [5, "Potion4", 10], [6, "Potion4", 15], [7, "Potion4", 25], [8, "Potion4", 25],
        [6, "Crystal2", 1], [7, "Crystal3", 1], [8, "Crystal4", 1],
        [5, "Fragment1", 75], [6, "Fragment2", 75], [7, "Fragment3", 99], [8, "Fragment4", 99]
    ],
    "Fortress" => [
        [0, "Scrap", 100], [0, "Stone1", 60],
        [1, "Trove1", 5], [2, "Trove2", 5], [3, "Trove3", 5], [4, "Trove4", 5], [0, "Ore5", 5]
    ],
    "Dragon" => [
        [0, "Unrefined1", 25], [0, "Stone2", 55],
        [1, "Gem1", 10], [2, "Gem1", 20], [3, "Gem1", 30], [4, "Jewel1", 40]
    ],
    "Demon" => [
        [0, "Flame1", 10], [0, "Flame2", 1], [0, "Stone3", 50], [0, "Unrefined2", 25],
        [1, "Gem2", 10], [2, "Gem2", 20], [3, "Gem2", 30], [4, "Jewel2", 40]
    ],
    "Paragon" => [
        [0, "Summon1", 5], [0, "Summon2", 1], [0, "Unrefined3", 25], [0, "Stone4", 45],
        [1, "Gem3", 10], [2, "Gem3", 20], [3, "Gem3", 30],
        [4, "Jewel3", 40], [5, "Jewel3", 50], [6, "Jewel3", 60], [6, "Gemstone10", 5]
    ],
    "Arbiter" => [
        [0, "Summon3", 5], [0, "Stone5", 40], [7, "Lotus5", 5],
        [1, "Token1", 5], [2, "Token2", 5], [3, "Token3", 5], [4, "Token4", 5],
        [5, "Token5", 5], [6, "Token6", 5], [7, "Token7", 5],
        [1, "Jewel4", 10], [2, "Jewel4", 20], [3, "Jewel4", 30], [4, "Jewel4", 40],
        [5, "Jewel4", 50], [6, "Jewel4", 60], [7, "Jewel4", 70]
    ],
    "Incarnate" => [
        [8, "Crystal3", 10], [8, "Crystal4", 5], [8, "Jewel5", 80], [8, "Trove8", 99],
        [8, "Lotus2", 5], [8, "Lotus3", 5], [8, "Lotus4", 5], [8, "Lotus5", 5],
        [8, "Lotus6", 5], [8, "Lotus7", 5], [8, "Lotus8", 5], [8, "Lotus9", 5],
        [8, "Lotus1", 5], [8, "Lotus10", 1], [8, "DarkStar", 2], [8, "Nephilim", 1], [8, "EssenceXXX", 99]
    ],
    "Ruler" => [
        [9, "Stone6", 33], [9, "Crystal4", 1], [9, "Lotus4", 0.1],
        [9, "Salvation", 0.2], [9, "Ruler", 0.01], [9, "Sacred", 0.05]
    ]
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
    public string $mode = '';
}

function run_boss($boss_calltype, $magnitude) {
    global $battleItemCost, $spawn_dict, $boss_list, $boss_tier_dict, $verified_player_id;
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
    // Normalize boss type
    $mode = '';
    $boss_level = $player_profile->player_level;
    switch ($boss_calltype) {
        case "Any":
            $max_spawn_index = 0;
            foreach ($spawn_dict as $key => $value) {
                if ($key <= $player_profile->player_echelon && $value > $max_spawn_index) {
                    $max_spawn_index = $value;
                }
            }
            $boss_type = $boss_list[rand(0, $max_spawn_index)];
            break;
        case "Gauntlet":
            $boss_type = getGauntletType(1);
            $mode = $boss_calltype;
            break;
        case "Palace1":
        case "Palace2":
        case "Palace3":
            $boss_type = "Incarnate";
            $boss_level = ($boss_calltype == "Palace1") ? 300 : (($boss_calltype == "Palace2") ? 600 : 999);
            break;
        case "Summon1":
        case "Summon2":
            $boss_type = "Paragon";
            break;
        case "Summon3":
            $boss_type = "Arbiter";
            break;
        default:
            $boss_type = $boss_calltype;
            break;
    }
    $boss_tier = $boss_tier_dict[$boss_calltype];
    if ($boss_tier == 0) {
        $boss_tier = getBossTier($boss_type);
    }
    $boss_profile = makeBoss($verified_player_id, $boss_type, $boss_tier, $boss_level, $magnitude, $mode);
    $boss_profile->curse_debuffs = $player_profile->elemental_curse;
    $boss_row = $boss_profile->setBoss($verified_player_id);
    $response = $player_profile
        ? ["success" => true, "player" => $player_profile, "boss" => $boss_profile, "encounter_id" => $boss_row['encounter_id']]
        : ["success" => false, "message" => "Player not found"];
    return $response;
}

function getGauntletType($tier) {
    $pool = ($tier < 4) ? ["Fortress", "Dragon", "Demon", "Paragon"] : ["Paragon", "Arbiter"];
    return $pool[array_rand($pool)];
}

function run_cycle($encounter_id) {
    global $verified_player_id;
    $query = "SELECT * FROM OnlineBosses WHERE player_id = $verified_player_id LIMIT 1";
    $rows = run_query($query);
    if (!$rows) {
        return ["success" => false, "message" => "No active boss."];
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
    $raw_cycle_data = process_cycle($boss_row, $encounter_id);
    if ($raw_cycle_data[2] != "continue" && !($raw_cycle_data[4]->mode === "Gauntlet" && $raw_cycle_data[4]->boss_tier < 6)) {
        clear_boss($verified_player_id);
    }
    return ["success" => true, "cycle_data" => $raw_cycle_data[0], "combat_tracker" => $raw_cycle_data[1], "battle_status" => $raw_cycle_data[2], 
    "player" => $raw_cycle_data[3], "boss" => $raw_cycle_data[4], "reward_data" => $raw_cycle_data[5], "achievement_data" => $raw_cycle_data[6]];
}

function process_cycle($boss_row, $encounter_id) {
    global $verified_player_id;
    $battle_status = "continue";
    $player_profile = get_player_by_id($verified_player_id);
    $player_profile->get_player_multipliers();
    $load_boss = build_boss_from_row($boss_row);
    if (big_cmp($load_boss->boss_cHP, '0') <= 0) {
        $new_tier = $load_boss->boss_tier + 1;
        $boss_profile = makeBoss($verified_player_id, getGauntletType($new_tier), $new_tier, $player_profile->player_level, $load_boss->magnitude, "Gauntlet");
        $boss_profile->curse_debuffs = $player_profile->elemental_curse;
        update_boss_details($boss_profile, null, $encounter_id, true);
        $combat_tracker = get_combat_tracker($player_profile, $boss_row, true);
    } else {
        $boss_profile = $load_boss;
        $combat_tracker = get_combat_tracker($player_profile, $boss_row, false);
    }
    $combat_tracker->total_cycles++;
    $action_rows = handle_boss_actions($player_profile, $boss_profile, $combat_tracker);
    if ($combat_tracker->player_cHP <= 0 && $combat_tracker->stun_status !== "stunned") {
        return [$action_rows, $combat_tracker, "player_dead"];
    }
    if ($combat_tracker->stun_cycles > 0) {
        $combat_tracker->stun_cycles -= 1;
        if ($combat_tracker->stun_cycles == 0) {
            $combat_tracker->stun_status = '';
        }
    } else {
        $action_rows = handle_player_actions($player_profile, $boss_profile, $combat_tracker, $action_rows);
    }
    if (big_cmp($boss_profile->boss_cHP, '0') <= 0) {
        $boss_profile->boss_cHP = '0';
        $battle_status = "boss_dead";
    }
    // Handle Tracker
    $total_damage = 0;
    for ($i = 0; $i < count($action_rows); $i++) {
        $type = $action_rows[$i]['action_type'] ?? '';
        if (strpos($type, 'boss') === false && strpos($type, 'stun') === false && strpos($type, 'regen') === false) {
            $dmg = (string) $action_rows[$i]['damage_value'];
            $total_damage = big_add($total_damage, $dmg);
            if (big_cmp($dmg, $combat_tracker->highest_damage) > 0) {
                $combat_tracker->highest_damage = $dmg;
            }
        }
        $action_rows[$i]['damage_value'] = str_strip_decimal($action_rows[$i]['damage_value']);
    }    
    $combat_tracker->total_dps = big_add($combat_tracker->total_dps, $total_damage);
    $reward_data = ['', []];
    $is_gauntlet = $boss_profile->mode === "Gauntlet";
    if ($battle_status == "boss_dead" && (!$is_gauntlet || $boss_profile->boss_tier >= 6)) {
        $reward_data = handle_rewards($player_profile, $boss_profile, $combat_tracker, $is_gauntlet);
    }
    update_boss_details($boss_profile, $combat_tracker, $encounter_id);
    return [$action_rows, $combat_tracker, $battle_status, $player_profile, $boss_profile, $reward_data[0], $reward_data[1]];
}

function get_combat_tracker($player, $boss_row, $reset_tracker = false) {
    $tracker = new CombatTracker();
    if ($boss_row['combat_tracker'] === '' || $reset_tracker) {
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

function update_boss_details($boss, $tracker, $encounter_id, $full_update = false) {
    $tracker_data = '';
    if ($tracker){
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
    }
    $updated_boss_data = "{$boss->boss_level};{$boss->boss_tier};{$boss->boss_cHP};{$boss->boss_mHP};{$boss->boss_element};{$boss->damage_cap}";
    $now = date('Y-m-d H:i:s');
    $query = "UPDATE OnlineBosses 
              SET boss_data = '$updated_boss_data', combat_tracker = '$tracker_data', time_stamp = '$now' 
              WHERE encounter_id = $encounter_id";
    if ($full_update) {
        $updated_boss_info = $boss->boss_name . ";" . $boss->boss_image . ";" . $boss->boss_type_num . ";" . $boss->magnitude . ";" . $boss->mode;
        $updated_boss_weakness = implode(";", $boss->boss_typeweak) . "/" . implode(";", $boss->boss_eleweak) . "/" . implode(";", $boss->curse_debuffs);
        $query = "UPDATE OnlineBosses 
                SET boss_info = '$updated_boss_info', boss_data = '$updated_boss_data', combat_tracker = '$tracker_data', time_stamp = '$now', boss_weakness = '$updated_boss_weakness'
                WHERE encounter_id = $encounter_id";
        }
    run_query($query, false);
}

function handle_boss_actions($player, &$boss, &$tracker) {
    global $boss_attack_dict, $skill_multiplier_list, $skill_multiplier_list_high, $element_names;
    $rows = [];
    if ($tracker->boss_stun_status != '') {
        $tracker->boss_stun_status = '';
        return $rows; 
    }
    $boss_element = ($boss->boss_element !== 9) ? $boss->boss_element : rand(0, 8);
    // Handle Boss Regen
    if ($boss->boss_type_num >= 3) {
        $regen_percent = 0.001 * $boss->boss_tier;
        $boss_regen = big_mul($boss->boss_mHP, $regen_percent);
        $boss->boss_cHP = big_cmp(big_add($boss->boss_cHP, $boss_regen), $boss->boss_mHP) > 0
            ? $boss->boss_mHP : big_add($boss->boss_cHP, $boss_regen);
        $rows[] = ["action_type" => "boss_regen", "action_name" => "Boss: Regenerate", "damage_value" => $boss_regen, 
        "triggers"=> '', "new_hp" => $boss->boss_cHP];
    }
    // Handle Boss Attack Skill
    if ($boss->boss_type_num == 0) { return $rows; }
    $attack_list = isset($boss_attack_dict[$boss->boss_type]) ? $boss_attack_dict[$boss->boss_type] : ["boss_skill_undefined"];
    foreach ($boss_attack_dict as $key => $list) {
        if (strpos($boss->boss_name, $key)) {
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
    if ($skill_class === "ultimate") {
        $base = [100, 100];
    } else {
        $base = [25, 50];
    }    
    // Handle Enrage
    if ($boss->boss_type_num >= 2 && big_cmp($boss->boss_cHP, big_div($boss->boss_mHP, '2')) < 0) {
        $base[0] *= 2;
        $base[1] *= 2;
    }
    $damage_set = [$base[0] * $boss->boss_level * $bonus, $base[1] * $boss->boss_level * $bonus];
    $damage_set = handle_evasions($player->block, $player->dodge, $damage_set, $bypass1, $bypass2);
    $damage = take_combat_damage($player, $tracker, $damage_set, $boss_element, $boss->magnitude);
    $trigger_data = ($skill_class === "signature") ? "SIGNATURE" : (($skill_class === "ultimate") ? "ULTIMATE" : "");
    $rows[] = ["action_type" => "boss_skill_" . $skill_class . " element_" . $element_names[$boss_element],  "triggers" => $trigger_data,
        "action_name" => $skill_name, "damage_value" => $damage, "new_hp" => $tracker->player_cHP];
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

function take_combat_damage($player, &$tracker, $damage_set, $element, $magnitude, $bypass_immortal = false, $no_trigger = false) {
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
    if ($magnitude > 0) {
        $damage *= ((1 + $magnitude) ^ 2);
    } 
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
        $rows[] = ["action_type" => "player_regen", "action_name" => "Player: Regenerate", "damage_value" => $regen, 
        "triggers"=> '', "new_hp" => $tracker->player_cHP];
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
        $rows = array_merge($rows, handle_skill($player, $boss, $tracker, $combo, $weapon, false));
        $combo += 1;
        if ($tracker->solar_stacks >= 35) {
            $rows = array_merge($rows, trigger_flare($player, $boss, $tracker));
        }        
        // Player Ultimate
        $tracker->charges += 1;
        $rows = array_merge($rows, handle_skill($player, $boss, $tracker, $combo, $weapon, true));
        if ($tracker->solar_stacks >= 35) {
            $rows = array_merge($rows, trigger_flare($player, $boss, $tracker));
        }        
    }
    // Player Basic Bleeds
    $rows = array_merge($rows, handle_bleed($player, $boss, $tracker, $weapon, false));
    return $rows;
}

function handle_skill($player, &$boss, &$tracker, $combo_count, $weapon, $is_ultimate) {
    global $skill_name_list;
    $rows = [];
    if ($is_ultimate) {
        if ($tracker->charges < 20 || $boss->boss_cHP <= 0) return [];
        $tracker->charges -= 20;
    }
    $combo_mult = (1 + $player->combo_mult * $combo_count) * (1 + $player->combo_pen);
    $ult_mult = (1 + $player->ultimate_mult) * (1 + $player->ultimate_pen);
    $tier_index = $is_ultimate ? 3 : (($combo_count < 3) ? 0 : (($combo_count < 5) ? 1 : 2));
    $skill_bonus = $player->skill_damage_bonus[$tier_index];
    # Skill name and multipliers
    $skill_damage_chart = [0 => 0.5, 1 => 0.75, 2 => 1, 3 => 2];
    $skill_list = $skill_name_list[$player->player_class];
    if ($player->ruler_mult > $player->player_level * 0.06) {
        $skill_list = ["Stasis Eater", "Stasis Eater", "Stasis Eater", "Finale"];
        $ruler_damage  = min(4, max(2, intdiv($player->player_level, 250)));
        $ruler_finale  = max(10, intdiv($player->player_level, 100));
        if ($player->aqua_points >= 100) {
            $ruler_finale = max(50, intdiv($player->player_level, 50));
            $skill_list[3] = "Splash";
        }
        $skill_damage_chart = [0 => $ruler_damage, 1 => $ruler_damage, 2 => $ruler_damage, 3 => $ruler_finale];
    }
    elseif ($player->ruler_mult > 0) {
        $skill_list = ["Stasis Breaker", "Stasis Breaker", "Stasis Breaker", "Stasis Breaker"];
        $skill_damage_chart = [0 => 2, 1 => 2, 2 => 2, 3 => 2];
    }
    elseif ($player->aqua_points >= 100) {
        $skill_list = ["Sea of Subjugation", "Ocean of Oppression", "Deluge of Domination", "Tides of Annihilation"];
        $skill_damage_chart = [0 => 1, 1 => 1.5, 2 => 2, 3 => 5];
    }
    $skill_name = $skill_list[$tier_index];
    # Damage Calc
    get_player_initial_damage($player, $boss, $weapon, $tracker);
    $damage = big_mul($player->total_damage, $combo_mult * ($skill_damage_chart[$tier_index] + $skill_bonus));
    if ($is_ultimate){
        if ($player->unique_glyph_ability[3]) {
            $damage = big_mul($damage, $ult_mult);
        }
    } else {
        $tracker->charges += 1 + $player->charge_generation;
        $tracker->solar_stacks += ($player->flare_type !== "") ? 1 : 0;
    }
    $triggers = apply_triggers($player, $tracker, $damage);
    $str_dmg = limit_and_calc($boss, $damage);
    $trigger_data = implode(" ", $triggers);
    if ($is_ultimate){ 
        $rows[] = ["action_type" => "ultimate" . ($triggers ? " " . $trigger_data : ""), "triggers" => strtoupper($trigger_data), 
            "action_name" => "Ultimate: " . $skill_name, "damage_value" => $str_dmg, "new_hp" => $boss->boss_cHP];
        // Ultimate Bleed Proc
        $rows = array_merge($rows, handle_bleed($player, $boss, $tracker, $weapon, true));
    } else {
        $rows[] = ["action_type" => "skill_" . $tier_index . ($triggers ? " " . $trigger_data : ""), "triggers" => strtoupper($trigger_data),
            "action_name" => "Combo (" . $combo_count . "x): " . $skill_name, "damage_value" => $str_dmg, "new_hp" => $boss->boss_cHP];
    }
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
    $damage = big_mul($player->total_damage, $tracker->bleed_tracker * (1 + $player->bleed_mult) * (1 + $player->bleed_pen) * (1 + $count) * $base);
    // Hyperbleed check
    $triggers = ["bleed"];
    if (rand(1, 100) <= $player->trigger_rate["Hyperbleed"]) {
        $damage = big_mul($damage, 1 + $player->bleed_mult);
        $triggers = ["hyperbleed"];
    }
    $str_dmg = limit_and_calc($boss, $damage);
    $trigger_data = implode(" ", $triggers);
    $rows[] = ["action_type" => $type . ($triggers ? " " . $trigger_data : ""), "triggers" => strtoupper($trigger_data), 
        "action_name" => "$label_prefix Rupture [$count_label]", "damage_value" => $str_dmg, "new_hp" => $boss->boss_cHP];
    return $rows;
}

function limit_and_calc($boss, $damage) {
    if ($boss->damage_cap !== '-1' && big_cmp($damage, $boss->damage_cap) > 0) {
        $str_dmg = $boss->damage_cap;
    } else {
        $str_dmg = strval($damage);
    }
    $boss->boss_cHP = big_sub($boss->boss_cHP, $str_dmg);
    if (big_cmp($boss->boss_cHP, '0') < 0) {
        $boss->boss_cHP = '0';
    }
    return $str_dmg;
}


function get_player_initial_damage($player, $boss, $weapon, &$tracker) {
    $base_damage = big_rand($player->player_damage_min, $player->player_damage_max);
    $adjusted = big_mul($base_damage, ((1 + $player->total_class_mult) * (1 + $player->final_damage) * (1 + $player->ruler_mult)));
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
        $tracker->boss_stun_status = $stun_status;
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
    $mult = 1 - (0.05 * ($boss->boss_tier - 1));
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

function apply_triggers($player, &$tracker, &$damage) {
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
        $bloom_bonus = $player->bloom_mult * 3;
        $damage = big_mul($damage, $bloom_bonus);
        $triggers[] = "stygian";
    } elseif (rand(1, 100) <= round($player->spec_conv["Calamity"] * 100)) {
        $damage = big_mul($damage, '9.99');
        $triggers[] = "calamity";
    }
    if($player->unique_glyph_ability[2]) {
        $damage = big_mul($damage, ((1 + $player->bleed_mult) * (1 + $player->bleed_pen)));
    }
    // Critical Triggers
    $crit_roll = rand(1, 100);
    $crit_bonus = 1 + $player->critical_mult;
    $crit_pen_bonus = 1 + $player->critical_pen;
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
    $str_dmg = limit_and_calc($boss, $damage);
    return [["action_type" => "flare", "action_name" => $player->flare_type . " Flare", 
        "damage_value" => $str_dmg, "new_hp" => $boss->boss_cHP]];
}

function handle_rewards($player_profile, $boss_profile, $combat_tracker, $gauntlet = false){
    global $web_url_base, $boss_loot_dict, $verified_player_id;
    $achievement_list = [];
    // Base Coin & Exp Calcs
    $pact = new Pact($player_profile);
    $multiplier_bonus = 2; // default multiplier for solo bosses 
    $type_bonus = ($boss_profile->boss_type_num + 1) * 100;
    $level_bonus = rand($boss_profile->boss_level, $boss_profile->boss_level * 10);
    $base_total = (1000 + $type_bonus + $level_bonus) * $multiplier_bonus;
    $exp_amount = $base_total * (1 + 2 * $boss_profile->magnitude);
    $coin_amount = $base_total * $boss_profile->boss_tier * (1 + $boss_profile->magnitude);
    if (strpos($boss_profile->boss_name, "XXX")) {
        $exp_amount = 200000;
    }
    // Update Coins
    $coin_msg = "";
    $coin_gain = calculate_coin_gain($coin_amount, $pact->pact_variant, $coin_msg);
    $player_profile->player_coins += (int) $coin_gain;
    // Update Exp
    $exp_msg = "";
    $exp_amount = calculate_exp_gain($exp_amount, $pact->pact_variant, $exp_msg);
    $player_profile->player_exp += (int) $exp_amount;
    // Update Level
    $lvl_data = apply_level_up($player_profile);
    $level_increase = $lvl_data[0];
    $lvl_msg = $lvl_data[1];
    if ($level_increase > 0) {
        handle_achievement($player_profile, "Level", $achievement_list);
    }   
    // Update Data
    $player_profile->update_player_data();
    // Calculate Table Reward Items
    $boss_loot_all = $boss_loot_dict['All'];
    $boss_loot_specific = $boss_loot_dict[$boss_profile->boss_type];
    $combined_loot = array_merge($boss_loot_specific, $boss_loot_all);
    $valid_loot = array_filter($combined_loot, fn($item) => $item[0] === $boss_profile->boss_tier || $item[0] === 0);
    $reward_items = [];
    foreach ($valid_loot as [$tier, $item_id, $drop_rate]) {
        $drop_chance = $drop_rate * 1000;
        $qty = 0;
        for ($i = 0; $i < $multiplier_bonus; $i++) {
            if (random_int(1, 100000) <= $drop_chance) {
                $qty++;
            }
        }
        if ($qty > 0) {
            $reward_items[$item_id] = ($reward_items[$item_id] ?? 0) + $qty;
        }
    }
    // Fae Core Drops
    $fae_id = "Fae" . (($boss_profile->boss_element != 9) ? $boss_profile->boss_element : rand(0, 8));
    $reward_items[$fae_id] = ($reward_items[$fae_id] ?? 0) + rand(5, max(5, min(100, $boss_profile->boss_level))) * $multiplier_bonus;
    // Tarot Essence Drops
    if (strpos($boss_profile->boss_name, ' - ') && !strpos($boss_profile->boss_name, "XXX")) {
        $essence_id = "Essence" . explode(" ", $boss_profile->boss_name, 2)[0];
        $essence_qty = 0;
        for ($i = 0; $i < $multiplier_bonus; $i++) {
            if (rand(1, 100) <= 25) $essence_qty++;
        }
        if ($essence_qty > 0) {
            $reward_items[$essence_id] = ($reward_items[$essence_id] ?? 0) + $essence_qty;
        }
    }    
    // Shard Drops
    $min_shards = ($boss_profile->magnitude > 0) ? 1 : 0;
    $max_shards = $boss_profile->magnitude;
    if (strpos($boss_profile->boss_name, "XXX")) {
        $min_shards += 1 * $multiplier_bonus;
        $max_shards += 5 * $multiplier_bonus;
    }
    if ($gauntlet) {
        $min_shards += 1;
        $max_shards += 5;
    }
    if ($min_shards > 0) {
        $shard_qty = rand($min_shards, $max_shards);
        $reward_items["Shard"] = ($reward_items["Shard"] ?? 0) + $shard_qty;
    }
    // Gauntlet Lotus Rewards
    if ($gauntlet && rand(1, 100) <= 5) {
        if (strpos($boss_profile->boss_name, "XXVIII")) {
            $reward_items["Lotus1"] = ($reward_items["Lotus1"] ?? 0) + 1;
        } elseif (strpos($boss_profile->boss_name, "XXV")) {
            $reward_items["Lotus9"] = ($reward_items["Lotus9"] ?? 0) + 1;
        }
    }
    // Hammer Fragment 
    if (strpos($boss_profile->boss_name, "Pandora") && random_int(1, 100) <= 1) {
        $reward_items["Pandora"] = ($reward_items["Pandora"] ?? 0) + 1;
    }    
    // Update Souls
    if ($player_profile->player_equipped[4] != 0) {
        $ring = read_custom_item($player_profile->player_equipped[4]);
        if ($ring->item_base_type === "Crown of Skulls") {
            $ring->roll_values[1] = strval(intval($ring->roll_values[1]) + 1);
            $ring->update_stored_item();
        }
    }
    // Update Stock
    update_reward_stock($verified_player_id, $reward_items);
    // Build Reward HTML display
    $reward_html  = '<div class="reward-row">';
        $reward_html .= '<img src="' . $web_url_base . 'gallery/Icons/Misc/Exp.webp" alt="Experience" class="reward-icon">';
        $reward_html .= '<span class="reward-name"> EXP </span>';
        $reward_html .= '<span class="reward-quantity">' . number_format((int)$exp_amount) . 'x' . $exp_msg . $lvl_msg . '</span>';
    $reward_html .= '</div>';
    $reward_html .= '<div class="reward-row">';
        $reward_html .= '<img src="' . $web_url_base . 'gallery/Icons/Misc/Lotus Coin.webp" alt="Lotus Coins" class="reward-icon">';
        $reward_html .= '<span class="reward-name"> Lotus Coins </span>';
        $reward_html .= '<span class="reward-quantity">' . number_format((int)$coin_gain) . 'x' . $coin_msg . '</span>';
    $reward_html .= '</div>';
    foreach ($reward_items as $item_id => $qty) {
        $item = new BasicItem($item_id);
        handle_achievement($player_profile, "Item", $achievement_list, $item_id);
        $reward_html .= '<div class="reward-row">';
            $reward_html .= '<img src="' . $item->image_link . '" alt="' . $item->item_name . '" class="reward-icon">';
            $reward_html .= '<span class="reward-name"> ' . $item->item_name . ' </span>';
            $reward_html .= '<span class="reward-quantity">' . $qty . 'x</span>';
        $reward_html .= '</div>';
    }
    return [$reward_html, $achievement_list];
}
?>