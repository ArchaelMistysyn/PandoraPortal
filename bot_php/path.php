<?php

function assign_path_multipliers($player_obj) {
    global $glyph_data, $path_perks, $element_dict;
    $total_points = array_map(function($stat, $gear) {
        return $stat + $gear;
    }, $player_obj->player_stats, $player_obj->gear_points);

    // Aqua Path exception
    if ($player_obj->aqua_mode != 0) {
        $player_obj->aqua_points = array_sum($total_points);
        $total_points = array_fill(0, count($total_points), 0);
        $player_obj->appli["Aqua"] += intdiv($player_obj->aqua_mode, 20);
        $player_obj->appli["Aqua"] = array_sum($player_obj->appli);
        $player_obj->appli = array_map(function($value) use ($player_obj) {
            return $value == "Aqua" ? $player_obj->appli["Aqua"] : 0;
        }, array_keys($player_obj->appli));
        $player_obj->elemental_res[1] += 0.002 * $player_obj->aqua_points;
        $player_obj->elemental_mult[1] += (0.25 * $player_obj->aqua_points) + (100 * $player_obj->appli["Aqua"]);
        return $total_points;
    }
	
	// Storm Path (0)
    $storm_bonus = $total_points[0];
    foreach ($element_dict['Storms'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.05 * $storm_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $storm_bonus;
    }
    $player_obj->critical_mult += 0.03 * $storm_bonus;
    $player_obj->appli["Critical"] += intdiv($storm_bonus, 20);
    if ($storm_bonus >= 100) {
        $player_obj->appli["Critical"] += 10;
    }

    // Horizon Path (2)
    $horizon_bonus = $total_points[2];
    foreach ($element_dict['Horizon'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.05 * $horizon_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $horizon_bonus;
    }
    $player_obj->bleed_mult += 0.1 * $horizon_bonus;
    $player_obj->appli["Bleed"] += intdiv($horizon_bonus, 20);
    if ($horizon_bonus >= 80) {
        $player_obj->bleed_pen *= 2;
    }

    // Eclipse Path (3)
    $eclipse_bonus = $total_points[3];
    foreach ($element_dict['Eclipse'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.05 * $eclipse_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $eclipse_bonus;
    }
    $player_obj->ultimate_mult += 0.1 * $eclipse_bonus;
    $player_obj->appli["Ultimate"] += intdiv($eclipse_bonus, 20);

    // Star Path (4)
    $star_bonus = $total_points[4];
    $player_obj->elemental_mult[8] += 0.07 * $star_bonus;
    $player_obj->elemental_res[8] += 0.001 * $star_bonus;
    $player_obj->combo_mult += 0.03 * $star_bonus;
    $star_skill_bonus = 0.25 * intdiv($star_bonus, 20);
    $player_obj->skill_damage_bonus[0] += $star_skill_bonus;
    if ($star_bonus >= 100) {
        $player_obj->skill_damage_bonus[1] += $player_obj->skill_damage_bonus[0];
        $player_obj->skill_damage_bonus[2] += $player_obj->skill_damage_bonus[0];
    }

    // Solar Flux Path (5)
    $solar_bonus = $total_points[5];
    foreach ($element_dict['Solar'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.03 * $solar_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $solar_bonus;
    }
    $player_obj->appli["Life"] += intdiv($solar_bonus, 20);
    if ($solar_bonus >= 100) {
        $player_obj->hp_bonus *= 2;
    }

    // Lunar Tides Path (6)
    $lunar_bonus = $total_points[6];
    foreach ($element_dict['Lunar'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.03 * $lunar_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $lunar_bonus;
    }
    $player_obj->appli["Mana"] += intdiv($lunar_bonus, 20);
    if ($lunar_bonus >= 100) {
        $player_obj->mana_mult *= 2;
    }

    // Terrestria Path (7)
    $terrestria_bonus = $total_points[7];
    foreach ($element_dict['Terrestria'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.03 * $terrestria_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $terrestria_bonus;
    }
    $player_obj->appli["Temporal"] += intdiv($terrestria_bonus, 20);

    // Confluence Path (8)
    $confluence_bonus = $total_points[8];
    $player_obj->all_elemental_mult += 0.02 * $confluence_bonus;
    $player_obj->all_elemental_curse += 0.01 * $confluence_bonus;
    $player_obj->appli["Elemental"] += 2 * intdiv($confluence_bonus, 20);
    if ($confluence_bonus >= 100) {
        $player_obj->all_elemental_pen *= 2;
        $player_obj->all_elemental_curse *= 2;
    }

    // Frostfire Path (1)
    $frostfire_bonus = $total_points[1];
    foreach ($element_dict['Frostfire'] as $ele_idx) {
        $player_obj->elemental_mult[$ele_idx] += 0.05 * $frostfire_bonus;
        $player_obj->elemental_res[$ele_idx] += 0.001 * $frostfire_bonus;
        $player_obj->elemental_pen[$ele_idx] += intdiv($frostfire_bonus, 20);
    }
    $player_obj->class_multiplier += 0.01 * $frostfire_bonus;

    if ($frostfire_bonus >= 80) {
        apply_cascade($player_obj->elemental_mult, $frostfire_bonus);
        apply_cascade($player_obj->elemental_pen, $frostfire_bonus);
        apply_cascade($player_obj->elemental_curse, $frostfire_bonus);
    }
    if ($frostfire_bonus >= 100) {
        foreach ($element_dict['Frostfire'] as $ele_idx) {
            $player_obj->elemental_mult[$ele_idx] *= 3;
            $player_obj->elemental_pen[$ele_idx] *= 3;
            $player_obj->elemental_curse[$ele_idx] *= 3;
        }
    }
    $appli_dict = [
        0 => "Critical", 
        3 => "Ultimate", 
        4 => "Combo",
        5 => "Life", 
        6 => "Mana", 
        7 => "Temporal", 
        8 => "Elemental"
    ];
    
    foreach ($total_points as $idx => $points) {
        if ($points >= 80 && array_key_exists($idx, $appli_dict)) {
            $player_obj->appli[$appli_dict[$idx]] *= 2;
        }
        if ($points >= 100) {
            $player_obj->unique_glyph_ability[$idx] = true;
        }
    }
    return $total_points;
}

function apply_cascade($data_list, $bonus) {
    $temp_list = $data_list;
    $temp_list[0] = 0;
    $temp_list[5] = 0;
    $highest_index = array_search(max($temp_list), $temp_list);
    $data_list[$highest_index] += $data_list[0] + $data_list[5];
}

function display_glyph($path_type, $total_points, $skill_color, $tier) {
    global $glyph_data, $path_perks;
    if ($total_points == 0) {
        return "";
    }
    $current_data = $glyph_data[$path_type];
    $path_bonuses = $path_perks[$path_type];
    $bonus = $current_data[0][1] * $tier;
    $glyph_name = "Glyph of " . $path_type;
    $description = "<div class='style-line'></div>";
    $description .= "<div>" . generate_stars(min(9, $tier)) . "</div>";
    $description .= "<div class='style-line'></div>";
    foreach ($path_bonuses as $modifier) {
        if (stripos($modifier[0], 'Resistance') !== false) {
            $total_value = number_format($modifier[1] * $total_points, 1);
        } else {
            $total_value = number_format($modifier[1] * $total_points);
        }
        $modified_string = str_replace("X", $total_value, $modifier[0]);
        $description .= "<div>" . $modified_string . "</div>";
    }
	$formatted_bonus = number_format($bonus);
    $description .= "<div>" . str_replace("X", $formatted_bonus, $current_data[0][0]) . "</div>";
    foreach ($current_data as $index => $breakpoint_data) {
        if ($index == 0) continue;
        if ($total_points >= $breakpoint_data[2]) {
            $glyph_name = "Glyph of " . $breakpoint_data[0];
            if ($breakpoint_data[1] !== null) {
                $description .= "<div>" . $breakpoint_data[1] . "</div>";
            }
        }
    }
    $glyph_display = "<div class='glyph-tooltip-lower" . $skill_color . "-border'><div class='" . $skill_color . "'>";
    $glyph_display .= "<div class='glyph-name' data-text='" . $glyph_name . "'><h3>" . $glyph_name . "</h3></div>";
    $glyph_display .= "<div class='glyph-description'>" . $description . "</div>";
    $glyph_display .= "</div></div>";

    return $glyph_display;

}
?>