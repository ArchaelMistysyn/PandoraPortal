<?php
$fortress_elements = ["Pyre", "Rain", "Clouds", "Sands", "Plague", "Blizzard", "Oblivion", "Nirvana", "Dreams"];
$demon_colours = ["Crimson", "Azure", "Violet", "Bronze", "Jade", "Ivory", "Stygian", "Gold", "Rose"];

$raid_bosses = [
    0 => "Geb, Sacred Ruler of Sin",
    1 => "Tiamat, Sacred Ruler of Fury",
    2 => "Veritas, Sacred Ruler of Prophecy",
    3 => "Geb, Sacred Ruler of Sin",
    4 => "Tiamat, Sacred Ruler of Fury",
    5 => "Veritas, Sacred Ruler of Prophecy",
    6 => "Alaric, Sacred Ruler of Totality"
];
$raid_element_dict = [
    "Geb, Sacred Ruler of Sin" => [3, 6, 7],
    "Tiamat, Sacred Ruler of Fury" => [0, 2, 5],
    "Veritas, Sacred Ruler of Prophecy" => [1, 4, 8],
    "Alaric, Sacred Ruler of Totality" => range(0, 8)
];

$all_names_dict = [
    "Fortress" => [["Keep"], ["Stronghold"], ["Castle"], ["XVI - Aurora, The Fortress"]],
    "Dragon" => [
        ["Zelphyros, Wind", "Sahjvadiir, Earth", "Cyries'vael, Ice"],
        ["Arkadrya, Lightning", "Phyyratha, Fire", "Elyssrya, Water"],
        ["Y'thana, Light", "Rahk'vath, Shadow"],
        ["VII - Astratha, The Dimensional"]
    ],
    "Demon" => [
        ["Beelzebub", "Azazel", "Astaroth", "Belial"],
        ["Abbadon", "Asura", "Baphomet", "Charybdis"],
        ["Iblis", "Lilith", "Ifrit", "Scylla"],
        ["VIII - Tyra, The Behemoth"]
    ],
    "Paragon" => array_fill(0, 6, []),
    "Arbiter" => array_fill(0, 7, []),
    "Incarnate" => array_fill(0, 8, [])
];

foreach ($tarot_data as $entry) {
    $boss_type = $entry["type"];
    $boss_tier = $entry["tier"];
    $boss_name = $entry["Numeral"] . " - " . $entry["Name"];
    if (isset($all_names_dict[$boss_type][$boss_tier - 1])) {
        $all_names_dict[$boss_type][$boss_tier - 1][] = $boss_name;
    }
}

class CurrentBoss {
    public int $player_id = 0;
    public string $boss_type = "";
    public int $boss_type_num = 0;
    public int $boss_tier = 0;
    public int $boss_level = 0;
    public string $boss_name = "";
    public string $boss_image = "";
    public int $boss_cHP = 0;
    public int $boss_mHP = 0;
    public array $boss_typeweak = [];
    public array $boss_eleweak = [];
    public array $curse_debuffs = [];
    public int $boss_element = 0;
    public int $damage_cap = -1;
    public int $stun_cycles = 0;
    public string $stun_status = "";
    public string $boss_thumbnail = "";

    public function __construct() {
        $this->boss_typeweak = array_fill(0, 7, 0);
        $this->boss_eleweak = array_fill(0, 9, 0);
        $this->curse_debuffs = array_fill(0, 9, 0.0);
    }

    public function generateBossNameImage() {
        global $all_names_dict, $raid_bosses, $raid_element_dict, $fortress_elements, $demon_colours, $element_names, $web_url_base;
        $web_url = $web_url_base . 'botimages/';
    
        if ($this->boss_type === "Ruler") {
            $this->boss_name = $raid_bosses[date('w')];
            $this->boss_element = $raid_element_dict[$this->boss_name][array_rand($raid_element_dict[$this->boss_name])];
            return;
        }
        $target_list = $all_names_dict[$this->boss_type][$this->boss_tier - 1];
        $this->boss_name = $target_list[array_rand($target_list)];
        $this->boss_element = 9;
    
        switch ($this->boss_type) {
            case "Fortress":
                $this->boss_image = "{$web_url}Tarot/Arbiter/XVI - Aurora, The Fortress.webp";
                if ($this->boss_tier !== 4) {
                    $this->boss_element = rand(0, 8);
                    $extension = ($this->boss_element >= 6) ? "" : "the ";
                    $suffix = $fortress_elements[$this->boss_element];
                    $this->boss_name = "{$suffix} {$this->boss_name} of {$extension}{$fortress_elements[$this->boss_element]}";
                    $this->boss_image = "{$web_url}bosses/Fortress/{$element_names[$this->boss_element]}_Fortress.png";
                }
                break;
            case "Paragon":
            case "Arbiter":
            case "Incarnate":
                $boss_numeral = explode(" ", $this->boss_name)[0];
                $this->boss_image = "{$web_url}tarot/{$boss_numeral}/{$boss_numeral}_8.png";
                break;
            case "Demon":
                if ($this->boss_tier !== 4) {
                    $this->boss_element = rand(0, 8);
                    $boss_colour = $demon_colours[$this->boss_element];
                    $this->boss_image = "{$web_url}bosses/Demon/{$boss_colour}/{$this->boss_name}_{$boss_colour}.png";
                    $this->boss_name = "{$boss_colour} {$this->boss_name}";
                }
                break;
            case "Dragon":
                $temp_name_split = explode(" ", $this->boss_name);
                $boss_element = $temp_name_split[1];
                $this->boss_element = 8;
                if ($this->boss_tier !== 4) {
                    $this->boss_element = array_search($boss_element, $element_names);
                    $this->boss_name .= " Dragon";
                }
                $this->boss_image = "{$web_url}bosses/Dragon/{$element_names[$this->boss_element]}_Dragon.png";
                break;
            default:
                break;
        }
    }

    public function setBoss($player_id) {
        $clear_query = "DELETE FROM OnlineBosses WHERE player_id = " . intval($player_id);
        run_query($clear_query, false);
        $boss_info = $this->boss_name . ";" . $this->boss_image . ";" . $this->boss_type_num;
        $boss_data = $this->boss_level . ";" . $this->boss_tier . ";" . $this->boss_cHP . ";" . $this->boss_mHP;
        $boss_weakness = implode(";", $this->boss_typeweak) . "/" . implode(";", $this->boss_eleweak);
        $insert_query = "INSERT INTO OnlineBosses (time_stamp, player_id, encounter, boss_info, boss_data, boss_weakness) ";
        $insert_query .= "VALUES (CURRENT_TIMESTAMP, $player_id, 'solo', '$boss_info', '$boss_data', '$boss_weakness')";
        run_query($insert_query, false);
    }
    
    
}

function makeBoss($player_id, $boss_type, $boss_tier, $boss_level, $magnitude = 0) {
    global $boss_list;
    if ($boss_type === "Ruler") {
    } elseif ($boss_tier == 7) {
        $boss_level += 150;
    } elseif ($boss_tier == 6) {
        $boss_level += 10;
    } elseif ($boss_type === "Arbiter") {
        $boss_level += 10;
    }
    $boss = new CurrentBoss();
    $boss->player_id = $player_id;
    $boss->boss_type = $boss_type;
    $boss->boss_type_num = array_search($boss_type, $boss_list);
    $boss->boss_tier = $boss_tier;
    $boss->boss_level = $boss_level;
    $boss->generateBossNameImage();
    $boss->boss_eleweak = assignRandomWeaknesses(9, 3);
    $boss->boss_typeweak = assignRandomWeaknesses(7, 2);
    $boss->boss_mHP = calculateBossHP($boss_level, $boss_tier, $magnitude);
    $boss->boss_cHP = $boss->boss_mHP;
    if ($boss_tier <= 4) {
        $boss->damage_cap = (int) ($boss->boss_mHP / 10) - 1;
    } elseif ($boss_type === "Ruler") {
        $boss->damage_cap = (int) ($boss->boss_mHP / 1000) - 1;
    } elseif ($boss_type === "Incarnate") {
        $boss->damage_cap = -1;
    }
    return $boss;
}

function assignRandomWeaknesses($total, $count) {
    $weaknesses = array_fill(0, $total, 0);
    foreach (array_rand($weaknesses, min($count, $total)) as $idx) {
        $weaknesses[$idx] = 1;
    }
    return $weaknesses;
}

function calculateBossHP($level, $tier, $magnitude) {
    $base_hp = pow(10, min(100, $level) / 10 + 5) * pow(10, $magnitude);
    if ($level >= 100) {
        $multiplier = floor(($level - 100) / 100) + 1;
        $base_hp *= pow(10, $multiplier);
    }
    return (int)$base_hp;
}

function getBossTier($b_type) {
    $tiers = ($b_type === "Arbiter") ? [1, 2, 3, 4, 5, 6] : [1, 2, 3, 4];
    $weights = ($b_type === "Arbiter") ? [35, 25, 20, 10, 7, 3] : [35, 30, 25, 10];
    return weightedRandomChoice($tiers, $weights);
}

function weightedRandomChoice($values, $weights) {
    $total_weight = array_sum($weights);
    $rand = mt_rand(1, $total_weight);
    $cumulative_weight = 0;
    foreach ($values as $index => $value) {
        $cumulative_weight += $weights[$index];
        if ($rand <= $cumulative_weight) {
            return $value;
        }
    }
}

?>