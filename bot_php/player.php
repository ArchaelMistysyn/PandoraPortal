<?php

class PlayerProfile {
    // Player base info
    public $player_id, $discord_id, $player_username;
    public $player_exp, $player_level, $player_echelon;
    public $player_class;
    public $player_quest, $quest_tokens;
    public $player_coins, $player_stamina, $luck_bonus;
	public $player_oath_num, $misc_data;

    // Player gear/stats info
    public $player_stats, $gear_points, $player_equipped;
    public $player_pact, $player_insignia, $equipped_tarot;

    // Player health stats
    public $player_mHP, $player_cHP;
    public $immortal;

    // Player damage values
    public $player_damage_min, $player_damage_max, $total_damage;

    // Elemental stats
    public $elemental_damage, $elemental_mult, $elemental_pen, $elemental_curse, $elemental_conversion;
    public $all_elemental_mult, $all_elemental_pen, $all_elemental_curse;
    public $special_res, $special_mult, $special_pen, $special_curse;
    public $singularity_mult, $singularity_pen, $singularity_curse;

    // Specialization stats
	public $limit_shift;
    public $unique_glyph_ability, $elemental_capacity;
    public $mana_mult, $start_mana, $mana_limit, $mana_shatter;
    public $bleed_mult, $bleed_pen;
    public $combo_mult, $combo_pen;
    public $ultimate_mult, $ultimate_pen;
    public $critical_mult, $critical_pen;
    public $bloom_mult;
    public $temporal_mult;
    public $trigger_rate, $perfect_rate, $appli;

    // Misc Datasets
    public $banes, $skill_damage_bonus, $unique_conversion, $spec_conv;

    // Misc stats
    public $charge_generation, $attack_speed;
    public $class_multiplier, $total_class_mult, $final_damage, $rng_bonus;
    public $defence_pen, $resist_pen;
    public $aqua_mode, $aqua_points;
    public $flare_type;
	public $ruler_mult;

    // Defensive stats
    public $hp_bonus, $hp_regen, $hp_multiplier, $recovery;
    public $block, $dodge;
    public $damage_mitigation, $mitigation_bonus;
    public $elemental_res, $all_elemental_res;

    public function __construct() {
        // Initialize all fields (as done previously)
        $this->player_id = 0;
        $this->discord_id = 0;
        $this->player_username = "";
        $this->player_exp = 0;
        $this->player_level = 0;
        $this->player_echelon = 0;
        $this->player_class = "";
        $this->player_quest = 0;
        $this->quest_tokens = array_fill(0, 30, 0);
        $this->quest_tokens[1] = 1;
        $this->player_coins = 0;
        $this->player_stamina = 0;
        $this->luck_bonus = 0;
		$this->player_oath_num = -1;
		$this->misc_data = [];

        $this->player_stats = array_fill(0, 9, 0);
        $this->gear_points = array_fill(0, 9, 0);
        $this->player_equipped = array_fill(0, 7, 0);
        $this->player_pact = "";
        $this->player_insignia = "";
        $this->equipped_tarot = "";

        $this->player_mHP = 1000;
        $this->player_cHP = 1000;
        $this->immortal = false;

        $this->player_damage_min = 0;
        $this->player_damage_max = 0;
        $this->total_damage = '0.0';

        $this->elemental_damage = array_fill(0, 9, 0);
        $this->elemental_mult = array_fill(0, 9, 0.0);
        $this->elemental_pen = array_fill(0, 9, 0.0);
        $this->elemental_curse = array_fill(0, 9, 0.0);
        $this->elemental_conversion = array_fill(0, 9, 1.0);
        $this->all_elemental_mult = 0.0;
        $this->all_elemental_pen = 0.0;
        $this->all_elemental_curse = 0.0;
        $this->special_res = ["Storms" => 0.0, "Eclipse" => 0.0, "Horizon" => 0.0, "Frostfire" => 0.0, "Holy" => 0.0, "Chaos" => 0.0];
        $this->special_mult = ["Storms" => 0.0, "Eclipse" => 0.0, "Horizon" => 0.0, "Frostfire" => 0.0, "Holy" => 0.0, "Chaos" => 0.0];
        $this->special_pen = ["Storms" => 0.0, "Eclipse" => 0.0, "Horizon" => 0.0, "Frostfire" => 0.0, "Holy" => 0.0, "Chaos" => 0.0];
        $this->special_curse = ["Storms" => 0.0, "Eclipse" => 0.0, "Horizon" => 0.0, "Frostfire" => 0.0, "Holy" => 0.0, "Chaos" => 0.0];
        $this->singularity_mult = 0.0;
        $this->singularity_pen = 0.0;
        $this->singularity_curse = 0.0;

		$this->limit_shift = 1.0;
        $this->unique_glyph_ability = array_fill(0, 9, false);
        $this->elemental_capacity = 3;
        $this->mana_mult = 1.0;
        $this->start_mana = 250;
        $this->mana_limit = 250;
        $this->mana_shatter = false;
        $this->bleed_mult = 0.0;
        $this->bleed_pen = 0.0;
        $this->combo_mult = 0.05;
        $this->combo_pen = 0.0;
        $this->ultimate_mult = 0.0;
        $this->ultimate_pen = 0.0;
        $this->critical_mult = 1.0;
        $this->critical_pen = 0.0;
        $this->bloom_mult = 10.0;
        $this->temporal_mult = 1.0;
        $this->trigger_rate = ["Fractal" => 0.0, "Hyperbleed" => 0.0, "Critical" => 0.0, "Omega" => 0.0, "Temporal" => 0.0, "Bloom" => 0.0, "Combo" => 0.0, "Status" => 1.0];
        $this->perfect_rate = ["Fractal" => 0, "Hyperbleed" => 0, "Critical" => 0, "Temporal" => 0, "Bloom" => 0];
        $this->appli = ["Critical" => 0, "Bleed" => 0, "Ultimate" => 0, "Life" => 0, "Mana" => 0, "Temporal" => 0, "Elemental" => 0, "Combo" => 0, "Aqua" => 0];

        $this->banes = array_fill(0, 7, 0.0);
        $this->skill_damage_bonus = array_fill(0, 4, 0);
        $this->unique_conversion = array_fill(0, 5, 0.0);
        $this->spec_conv = ["Heavenly" => 0.0, "Stygian" => 0.0, "Calamity" => 0.0, "DarkDream" => 0.0, "LightDream" => 0.0];

        $this->charge_generation = 1;
        $this->attack_speed = 0.0;
        $this->class_multiplier = 0.05;
		$this->total_class_mult = 0.00;
        $this->final_damage = 0.0;
        $this->rng_bonus = 0.0;
        $this->defence_pen = 0.0;
        $this->resist_pen = 0.0;
        $this->aqua_mode = 0;
        $this->aqua_points = 0;
        $this->flare_type = "";
		$this->ruler_mult = 0.00;

        $this->hp_bonus = 0.0;
        $this->hp_regen = 0.0;
        $this->hp_multiplier = 0.0;
        $this->recovery = 3;
        $this->block = 0.01;
        $this->dodge = 0.01;
        $this->damage_mitigation = 0.0;
        $this->mitigation_bonus = 0.0;
        $this->elemental_res = array_fill(0, 9, 0.0);
        $this->all_elemental_res = 0.1;
    }
	
	public function player_header() {
		// Add the search bar container
		$html = '<div id="search-bar-section" class="highlight-text" style="display: none;">';
		$html .= '<button class="toggle-search" onclick="toggleSearchBar()"><span class="toggle-search-image search-char-active"></span></button>';
		$html .= '<form id="filter-form" method="get" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
		$html .= '<input type="text" name="search_input" placeholder="Enter Player ID/Username or Discord ID" required>';
		$html .= '<button type="submit" class="input-button">Search</button>';
		$html .= '</form></div>';
		// Character Name
		$html .= '<div id="char-name-section" class="highlight-text">';
		$html .= '<button class="toggle-search" onclick="toggleSearchBar()"><span class="toggle-search-image"></span></button>';
		$html .= '<div class="char-name">';
		$html .= '<div class="char-align"><img src="./gallery/Icons/Classes/' . $this->player_class . '.webp" class="icon-medium character-icon"/></div>';
		$html .= '<div class="char-align">' . $this->player_username . '</div>';
		$html .= '<div class="char-align"><i>Lv' . $this->player_level . '</i></div>';
		$html .= '<div class="char-align">' . $this->player_class . '</div>';
		$html .= '</div></div>';
		return $html;
	}

	public function update_player_data() {
		$query = "UPDATE PlayerList SET";
		$query .= " player_equipped = '" . implode(';', $this->player_equipped);
		$query .= "', player_exp = " . intval($this->player_exp);
		$query .= ", player_quest = " . intval($this->player_quest);
		$query .= ", player_level = " . intval($this->player_level);
		$query .= ", player_echelon = " . intval($this->player_echelon);
		$query .= ", player_coins = " . intval($this->player_coins);
		$query .= ", player_stamina = " . intval($this->player_stamina);
		$query .= ", player_pact = '" . addslashes($this->player_pact);
		$query .= "', player_insignia = '" . addslashes($this->player_insignia);
		$query .= "', player_tarot = '" . addslashes($this->equipped_tarot);
		$query .= "' WHERE player_id = " . intval($this->player_id);
		run_query($query, false);
		return;
	}
	
	public function load_misc_data() {
        $query = "SELECT * FROM MiscPlayerData WHERE player_id = " . intval($this->player_id);
        $result = run_query($query);
        if (!empty($result)) {
            $this->misc_data = $result[0];
            $oath_data = explode(';', $this->misc_data['oath_data']);
            $oath_data = array_map('intval', $oath_data);
            $this->player_oath_num = array_search(3, $oath_data);
            if ($this->player_oath_num === false) {
                $this->player_oath_num = -1;
            }
        }
    }

	public function update_misc_data() {
		$query = "UPDATE MiscPlayerData SET";
		$query .= " oath_data = '" . addslashes($this->misc_data['oath_data']) . "'";
		$query .= ", monument_data = '" . addslashes($this->misc_data['monument_data']) . "'";
		$query .= ", thana_visits = " . intval($this->misc_data['thana_visits']);
		$query .= ", eleuia_visits = " . intval($this->misc_data['eleuia_visits']);
		$query .= ", deaths = " . intval($this->misc_data['deaths']);
		$query .= ", quest_choice = " . intval($this->misc_data['quest_choice']);
		$query .= " WHERE player_id = " . intval($this->player_id);
		run_query($query, false);
	}
	
	public function display_player($w_item, $gear_score) {
		global $path_names, $glyph_data, $path_perks;	
		if ($this->player_id == 0) {
			return "<h1>No Character Loaded</h1>";
		} 
	
		$glyph_name = null;
		$exp = $this->player_exp;
		$level = $this->player_level;
		$max_exp = get_max_exp($level);
		$formatted_exp = number_format($exp);
		$formatted_max_exp = number_format($max_exp);
		$exp_percent = $exp / $max_exp;
	
		$html = '<div id="player-info">';
		$html .= $this->player_header();
    	$html .= '<div id="player-box-content">';
		$html .= '<table id="player-table">';
		$html .= '<tr class="player-table-title"><th colspan="2">GENERAL STATS</th></tr>';
		// Experience
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Experience:</td><td>';
		$html .= '<div class="exp-bar"><span class="tooltip">EXP: ' . $formatted_exp . ' / ' . $formatted_max_exp . '</span>';
		$html .= '<div class="exp-fill" style="width: ' . ($exp_percent * 100) . '%;"></div><div class="exp-empty" style="width: ' . (100 - $exp_percent * 100) . '%;"></div>';
		$html .= '</div></td></tr>';
		// Elemental Breakdown
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Element Spread:</td><td>' . $this->display_elemental_breakdown($w_item) . '</td></tr>';
		// Player ID
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Player ID:</td><td>' . $this->player_id . '</td></tr>';
		// Player ID
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Total Gear Score:</td><td><span class="star-symbol">★</span> ' . number_format($gear_score) . '</td></tr>';
		// Base Damage
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Base Damage:</td><td>' . number_format($this->player_damage_min) . ' - ' . number_format($this->player_damage_max) . '</td></tr>';
		// Attack Speed
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Attack Speed:</td><td>' . number_format(round(floor($this->attack_speed * 10) / 10, 2), 2) . ' / min</td></tr>';
		// Oath
		$oath_labels = [0 => "Pandora's Oath", 1 => "Thana's Oath", 2 => "Eleuia's Oath"];
		$oath_label = "Oathless";
		if (isset($this->player_oath_num) && array_key_exists($this->player_oath_num, $oath_labels)) {
			$oath_label = $oath_labels[$this->player_oath_num];
		}
		$html .= '<tr class="player-table-stat"><td><img src="/images/Icons/diamonds-four-fill.png" alt="stat icon" class="icon-small stat-icon"/>Oath:</td><td>' . $oath_label . '</td></tr>';
		// Lotus Coins
		$formatted_coin_value = number_format($this->player_coins);
		$html .= '<tr class="player-table-stat"><td><img src="./gallery/Icons/Misc/Lotus Coin.webp" alt="Coins" class="icon-small stat-icon"/>Lotus Coins:</td><td> ' . $formatted_coin_value . '</td></tr>';
		$html .= '</table>';
		// Skill Points & Glyphs
    	$html .= '<div class="player-table-title"><p>GLYPHS</p></div>';
		$html .= '<div id="skill-points-section" class="skill-points">';
		foreach ($this->player_stats as $index => $point) {
			$glyph_display = null;
			$combined_point = $point + $this->gear_points[$index];
			$skill_color = '';
			$title = "Path of " . $path_names[$index];
			$tier = floor($combined_point / 20);
			if ($tier >= 10) {
				$skill_color = ' ultimate';
			} else if ($tier >= 1) {
				$skill_color = ' tier-' . $tier;
			}
			if ($tier >= 1) {
				$glyph_name = "Glyph of " . $path_names[$index];
				$glyph_display = display_glyph($path_names[$index], $combined_point, $skill_color, $tier);
			}
			$html .= '<div class="skill-circle' . $skill_color . '">';
			$html .= '<div class="inner-skill-circle">';
			$html .= '<span class="glyph-tooltip highlight-text">' . $title . '</span>';
			if ($glyph_display) {
				$html .=  $glyph_display;
			}
			$html .= '<div class="' . $skill_color . '">' . $combined_point . '</div>';
			$html .= '</div></div>';
		}
		$html .= '</div></div></div>';
		return $html;
	}	

	public function get_player_multipliers() {
		global $sovereign_item_list, $element_dict;
		// Base values
		$base_critical_chance = 10.0;
		$base_attack_speed = 1.0;
		$base_mitigation = 0.0;
		$base_player_hp = 1000 + 50 * $this->player_level;
		// Class Bonuses
		$class_bonus = [
			"Ranger" => ["Critical", 1], 
			"Weaver" => ["Elemental", 2], 
			"Assassin" => ["Bleed", 1],
			"Mage" => ["Mana", 1], 
			"Summoner" => ["Combo", 1], 
			"Knight" => ["Ultimate", 1], 
			"Rider" => ["Life", 1]
		];
		if (isset($class_bonus[$this->player_class])) {
			$this->appli[$class_bonus[$this->player_class][0]] += $class_bonus[$this->player_class][1];
		}
		// Oath Bonuses
		$oath_bonus = [
			0 => ["Critical", "Ultimate"],
			1 => ["Immortal", "Life", "Mana"],
			2 => ["Elemental", "Elemental"]
		];
		foreach ($oath_bonus[$this->player_oath_num] as $bonus) {
			if ($bonus == "Immortal") {
				$this->immortal = true;
			} else {
				$this->appli[$bonus] += 1;
			}
		}
		// Item Multipliers
		$e_item = [];
		$equipped_gem_ids = [];
		$non_zero_equipped = array_filter($this->player_equipped, function($value) { return $value !== 0; });
		if (!empty($non_zero_equipped)) {
			$equipped_placeholder = implode(',', $non_zero_equipped);
			if ($equipped_placeholder) {
				$result_items = read_custom_item(null, $equipped_placeholder);
				foreach ($result_items as $row) {
					$e_item[] = $row;
					if ($row->item_inlaid_gem_id != 0) {
						$equipped_gem_ids[] = $row->item_inlaid_gem_id;
					}
				}
			}
			$equalize_damage = false;
			foreach ($e_item as $e_obj) {
				if ($e_obj->item_id != 0) {
					$this->player_damage_min += $e_obj->item_damage_min;
					$this->player_damage_max += $e_obj->item_damage_max;
					if (in_array($e_obj->item_base_type, $sovereign_item_list)) {
						$e_obj->assign_sovereign_values($this);
						if ($e_obj->item_type == 'W') {
							$equalize_damage = true;
						}
					} else {
						assign_roll_values($this, $e_obj);
					}
					assign_item_element_stats($this, $e_obj);
				}
				if ($e_obj->item_type == "W"){
					$base_attack_speed *= floatval($e_obj->item_base_stat);
					$w_item = $e_obj;
				} else {
					$this->unique_ability_multipliers($e_obj);
				}
				if ($e_obj->item_type == "A"){
					$base_mitigation = $e_obj->item_base_stat;
				}
				if ($e_obj->item_type == "R"){
					$r_item = $e_obj;
				}
			}
			if (!empty($equipped_gem_ids)) {
				$equipped_gem_ids_placeholder = implode(',', array_map('intval', $equipped_gem_ids));
				$result_gems = read_custom_item(null, $equipped_gem_ids_placeholder);
				foreach ($result_gems as $gem_item) {
					assign_gem_values($this, $gem_item);
				}
			}
		}

		// Non-Gear Item Multipliers
		assign_insignia_values($this);
		if ($this->equipped_tarot != "") {
			$resonance = check_resonance($w_item ?? null, $r_item ?? null);
			$e_tarot = get_tarot_by_id($this, $resonance);
			$e_tarot->assign_tarot_values($this);
		}
		
		// Path Multipliers
		$total_points = assign_path_multipliers($this); 
		
		// Application Bonuses
		$this->critical_mult += $this->appli["Critical"];
		$this->all_elemental_mult += $this->appli["Elemental"] * 0.25;
		$this->hp_multiplier += 0.1 * $this->appli["Life"];

		// Elemental Capacity
		$this->elemental_capacity += max(0, $this->appli["Elemental"]);

		// Pact Bonus Multipliers
		assign_pact_values($this);

		// Capacity Hard Limits
		$this->elemental_capacity = min(9, $this->elemental_capacity);
		$this->mana_limit = max(10, $this->mana_limit);
		$this->start_mana = $this->mana_limit != 0 ? $this->mana_limit : 0;

		// Special conditions for Solitude/Frostfire and Aqua
		if ($total_points[6] >= 100 || $this->aqua_mode != 0) {
			$this->elemental_capacity = 1;
		} elseif ($total_points[1] >= 80) {
			$this->elemental_capacity = 3;
		}

		// Final Calculations
		$this->trigger_rate["Critical"] = (int)((1 + $this->trigger_rate["Critical"]) * $base_critical_chance);
		$this->attack_speed = (1 + $this->attack_speed) * $base_attack_speed;
		$this->damage_mitigation = min((1 + ($this->mitigation_bonus + $this->damage_mitigation)) * $base_mitigation, 90);
		$this->player_cHP = $this->player_mHP = (int)(($base_player_hp + $this->hp_bonus) * (1 + $this->hp_multiplier));

		// Trigger Rates
		$this->trigger_rate["Omega"] = min(100, (int)($this->trigger_rate["Omega"] + round($this->appli["Critical"])) * 3);
		$this->trigger_rate["Hyperbleed"] = min(100, (int)(round($this->trigger_rate["Hyperbleed"] + $this->appli["Bleed"])) * 4);
		$this->trigger_rate["Fractal"] = min(100, (int)(round($this->trigger_rate["Fractal"] + $this->appli["Elemental"])) * 4);
		$this->trigger_rate["Temporal"] = min(100, (int)(round($this->trigger_rate["Temporal"] + $this->appli["Temporal"])) * 4);
		$this->trigger_rate["Combo"] = min(100, (int)(round($this->trigger_rate["Combo"] + $this->appli["Combo"])) * 4);

		// Perfect Rates
		$this->perfect_rate["Critical"] = $this->aqua_points >= 80 ? 1 : $this->perfect_rate["Critical"];
		foreach ($this->perfect_rate as $mechanic => $rate) {
			if ($rate > 0) {
				$this->trigger_rate[$mechanic] = 100;
			}
		}
		
		// Class Mastery
		$match_count = 0;
		foreach ($e_item as $item) {
			if ($item !== null && $item->item_damage_type == $this->player_class) {
				$match_count += 1;
			}
		}
		if ($this->unique_conversion[2] > 0) {
			$unique_damage_types = [];
			foreach ($e_item as $item) {
				if ($item !== null) {
					$unique_damage_types[$item->item_damage_type] = true;
				}
			}
			$match_count = count($unique_damage_types);
		}
		$this->class_multiplier += $this->unique_conversion[2];
		$this->total_class_mult = $this->class_multiplier * $match_count;


		// Unique Bonus
		$this->special_mult["Holy"] += $this->spec_conv["LightDream"];
		$this->special_pen["Holy"] += $this->spec_conv["LightDream"];
		$this->special_curse["Holy"] += $this->spec_conv["LightDream"];
		$this->special_mult["Chaos"] += $this->spec_conv["DarkDream"];
		$this->special_pen["Chaos"] += $this->spec_conv["DarkDream"];
		$this->special_curse["Chaos"] += $this->spec_conv["DarkDream"];

		// Hybrid multipliers
		foreach ($this->special_mult as $bonus_type => $mult_value) {
			$index_list = $element_dict[$bonus_type];
			if (($bonus_type == "Holy" && $this->spec_conv["LightDream"] != 0.0) ||
				($bonus_type == "Chaos" && $this->spec_conv["DarkDream"] != 0.0)) {
				$index_list[] = 8;
			}

			// Apply multipliers to each relevant element index
			foreach ($index_list as $ele_num) {
				$this->elemental_mult[$ele_num] += $this->special_mult[$bonus_type];
				$this->elemental_pen[$ele_num] += $this->special_pen[$bonus_type];
				$this->elemental_curse[$ele_num] += $this->special_curse[$bonus_type];
				$this->elemental_res[$ele_num] += $this->special_res[$bonus_type];
			}
		}

		// Singularity multipliers
		apply_singularity($this->elemental_mult, $this->singularity_mult);
		apply_singularity($this->elemental_pen, $this->singularity_pen);
		apply_singularity($this->elemental_curse, $this->singularity_curse);

		// Omni multipliers
		for ($x = 0; $x < 9; $x++) {
			$this->elemental_mult[$x] += $this->all_elemental_mult;
			$this->elemental_pen[$x] += $this->all_elemental_pen;
			$this->elemental_res[$x] += $this->all_elemental_res;
			$this->elemental_curse[$x] += $this->all_elemental_curse;
			$this->elemental_res[$x] = min(0.9, $this->elemental_res[$x]);

			// Apply unique resistance conversion
			$this->elemental_mult[$x] += $this->elemental_res[$x] * $this->unique_conversion[0];
		}

		// Bane multipliers
		for ($y = 0; $y < 6; $y++) {
			$this->banes[$y] += $this->banes[6];
		}
		

		// Unique Conversions
		$hp_reduction = (int)($this->player_mHP * $this->unique_conversion[1]);
		$this->player_mHP -= $hp_reduction;
		$this->final_damage += (int)($hp_reduction / 100);
		$this->final_damage += $this->damage_mitigation * $this->unique_conversion[3];

		if ($this->unique_conversion[4] >= 1) {
			$hp_blossom_bonus = (int)($this->player_mHP / 100);
			$this->bloom_mult += $hp_blossom_bonus;
			$this->bleed_mult += $hp_blossom_bonus;

			if ($this->unique_conversion[4] == 2) {
				$this->final_damage += $hp_blossom_bonus;
			}
		}
		
		if ($this->aqua_mode != 0 && $this->equipped_tarot != "" && $e_tarot->card_numeral == "XIV") {
			$this->critical_mult += $this->elemental_mult[1];
		}

		// Flat Damage Bonuses
		$this->player_damage_min += $this->appli["Life"] * $this->player_mHP * 5;
		$this->player_damage_max += $this->appli["Life"] * $this->player_mHP * 5;

		// Attack speed hard cap
		$this->attack_speed = min(10, $this->attack_speed);

		// Sovereign equalizer
		if ($equalize_damage) {
			$this->player_damage_min = $this->player_damage_max;
		}
	}
	
	public function display_element_stats($w_item = null) {
		global $element_names;
		$html = $this->player_header();
    	$html .= '<div id="player-box-content">';
    	$html .= '<div class="player-table-title"><span>Elements</span></div>';
		$element_breakdown = [];
		$total_contribution = 0;
		$temp_element_list = $w_item ? limit_elements($this, $w_item) : array_fill(0, 9, false);
		for ($x = 0; $x < 9; $x++) {
			$total_multi = (1 + $this->elemental_mult[$x]) * (1 + $this->elemental_pen[$x]);
			$total_multi *= (1 + $this->elemental_curse[$x]) * $this->elemental_conversion[$x];
			$element_breakdown[$x] = $total_multi * 100;
			if ($temp_element_list[$x] && $element_breakdown[$x] > 0) {
				$total_contribution += $element_breakdown[$x];
			}
		}
		$active_elements = [];
		$inactive_elements = [];
		foreach ($element_breakdown as $index => $value) {
			if ($temp_element_list[$index] && $value > 0) {
				$active_elements[$index] = $value;
			} else {
				$inactive_elements[$index] = $value;
			}
		}
		arsort($active_elements);
		arsort($inactive_elements);
		foreach ($active_elements as $z => $total_multi) {
			$html .= $this->create_element_section($z, $total_multi, true, $total_contribution);
		}
		foreach ($inactive_elements as $z => $total_multi) {
			$html .= $this->create_element_section($z, $total_multi, false);
		}
		$html = "<div id='player-info'>{$html}";
		$html .= "<div style='margin-top: auto;'></div></div></div>";
		return $html;
	}
	
	public function display_resistances() {
		global $element_names;
		$html = "<div id='player-info'>{$this->player_header()}";
		$html .= '<div id="player-box-content">';
    	$html .= '<div class="player-table-title"><span>Resistances</span></div>';
		$resistance_breakdown = [];
		$total_resistance = 0;
		for ($x = 0; $x < 9; $x++) {
			$resistance_value = $this->elemental_res[$x] * 100;
			$resistance_breakdown[$x] = [
				'index' => $x,
				'value' => $resistance_value
			];
			$total_resistance += $resistance_value;
		}
		usort($resistance_breakdown, function($a, $b) {
			return $b['value'] <=> $a['value'];
		});
		$temp = "";
		foreach ($resistance_breakdown as $resistance) {
			$index = $resistance['index'];
			$resistance_value = $resistance['value'];
			$temp_icon = '<img src="./gallery/Icons/Elements/' . $element_names[$index] . '.webp" class="icon-small" alt="' . $element_names[$index] . '">';
			$temp_res_str = "Resistance: " . number_format(round($resistance_value)) . "%";
			$temp .= "<div class='element-section eleBox-{$element_names[$index]}'>";
			$temp .= "<div class='total-box' class='detail-item'>{$temp_icon}<h1 class='elemental-highlight-" . $element_names[$index] . "'>" . $temp_res_str . "</h1></div>";
			$temp .= "</div>";
		}
		$html .= "<div id='resistance-box'>{$temp}</div>";
		$html .= "</div></div>";
		return $html;
	}

	public function display_defences() {
    	$html = $this->player_header();
		$html .= '<div id="player-box-content">';
		$html .= '<div class="player-table-title"><span>Defences</span></div>';
		$html .= "<div id='defensive-stats'>";
		$stats = [
		['label' => 'Max HP', 'value' => number_format(round($this->player_mHP))],
		['label' => 'HP Regen', 'value' => number_format(round($this->hp_regen * $this->player_mHP))],
		['label' => 'Recovery', 'value' => number_format($this->recovery)],
		['label' => 'Damage Mitigation', 'value' => number_format($this->damage_mitigation, 1)],
		['label' => 'Block Rate', 'value' => number_format(round($this->block * 100), 1)],
		['label' => 'Dodge Rate', 'value' => number_format(round($this->dodge * 100), 1)],
		['label' => 'Immortal', 'value' => $this->immortal ? 'Active' : 'Inactive']
		];
		foreach ($stats as $stat) {
			$html .= "<div class='defense-section player-table-stat'>";
			$html .= "  <div class='stat-section-left'>";
			$html .= "    <img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/>";
			$html .= "    <h3>{$stat['label']}: </h3>";
			$html .= "  </div>";
			$html .= "  <div class='stat-section-right'>";
			$html .= "    <p>{$stat['value']}</p>";
			$html .= "  </div>";
			$html .= "</div>";
		}
		$html .= "</div></div>";
    	$html = "<div id='player-info'>{$html}</div>";
		return $html;
	}
	
	
	public function display_details() {
		$appli_data = [
			'Elemental' => [
				["tag" => "Capacity", "value" => $this->elemental_capacity],
				["tag" => "Fractal Crit", "value" => $this->trigger_rate["Fractal"]]
			],
			'Critical' => [
				["tag" => "Critical Rate", "value" => $this->trigger_rate["Critical"]],
				["tag" => "Critical Multiplier", "value" => show_num($this->critical_mult)],
				["tag" => "Critical Penetration", "value" => show_num($this->critical_pen)],
				["tag" => "Omega Critical Rate", "value" => $this->trigger_rate["Omega"]]
			],
			'Combo' => [
				["tag" => "Combo Multiplier", "value" => show_num($this->combo_mult)],
				["tag" => "Combo Penetration", "value" => show_num($this->combo_pen)],
				["tag" => "Synchronize", "value" => $this->trigger_rate["Combo"]]
			],
			'Ultimate' => [
				["tag" => "Ultimate Multiplier", "value" => show_num($this->ultimate_mult)],
				["tag" => "Ultimate Penetration", "value" => show_num($this->ultimate_pen)]
			],
			'Bleed' => [
				["tag" => "Bleed Multiplier", "value" => show_num($this->bleed_mult)],
				["tag" => "Bleed Penetration", "value" => show_num($this->bleed_pen)],
				["tag" => "Hyperbleed Rate", "value" => $this->trigger_rate["Hyperbleed"]]
			],
			'Temporal' => [
				["tag" => "Time Lock Rate", "value" => $this->trigger_rate["Temporal"]],
				["tag" => "Time Shatter Multiplier", "value" => show_num($this->temporal_mult)]
			],
			'Life' => [
				["tag" => "Health Multiplier", "value" => show_num($this->hp_multiplier)],
				["tag" => "Flat Damage Bonus", "value" => $this->player_mHP * 5]
			],
			'Mana' => [
				["tag" => "Mana Multiplier", "value" => show_num($this->mana_mult)],
				["tag" => "Mana Limit", "value" => $this->mana_limit]
			]
		];
		
		// Bloom exception, because it's not an application
		$bloom_is_active = $this->trigger_rate["Bloom"] > 0;
		$bloom_html = "<div id='section-bloom' class='detail-section appBox-Bloom" . ($bloom_is_active ? "" : " inactive-element") . "'>";
		$bloom_html .= "<div><h1 class='appli-highlight-Bloom'>Bloom (Special)</h1></div>";
		$bloom_html .= "</div>";
		$bloom_side_html = "<div id='side-box-bloom' class='side-detail-list appli-highlight-Bloom appBox-Bloom'>";
		$bloom_side_html .= "<div class='detail-item app-item player-table-stat'>
			<div class='stat-section-left'>Bloom Damage:</div>
			<div class='stat-section-right'>" . number_format(show_num($this->bloom_mult)) . "%</div>
		</div>";
		$bloom_side_html .= "<div class='detail-item app-item player-table-stat'>
			<div class='stat-section-left'>Bloom Rate:</div>
			<div class='stat-section-right'>" . number_format(show_num($this->trigger_rate["Bloom"])) . "%</div>
		</div>";
		$bloom_side_html .= "</div>";

		uksort($appli_data, function($a, $b) {
			return $this->appli[$b] <=> $this->appli[$a];
		});
	
		$html = "<div id='player-info'>" . $this->player_header() . "<div id='player-box-content'>";
		$html .= "<div class='player-table-title'><span>Application/Trigger Details</span></div>";
		$html .= "<div id='detail-container'>";
		$html .= "<div id='main-detail-box'>";
		$side_html = '';
		$bloom_inserted = false;
		$first_active_set = false;
		foreach ($appli_data as $type => $data) {
			$appBoxClass = "appBox-" . $type;
			$is_active = $type === 'Elemental' || ($type === "Critical" && $this->trigger_rate["Critical"] > $this->trigger_rate["Fractal"]);
			$is_active = $is_active || $this->appli[$type] > 0;
			$class_modifier = $is_active ? "" : " inactive-element";
			$class_modifier .= $first_active_set ? "" : " focused-element";
			$highlight_class = "appli-highlight-" . $type;
			$section_id = "section-" . strtolower($type);
			$side_detail_id = "side-detail-" . strtolower($type);
			$box_class = "side-detail-list";
			// Bloom exception insertion
			if (!$is_active && !$bloom_inserted) {
				$html .= $bloom_html;
				$side_html .= $bloom_side_html;
				$bloom_inserted = true;
			}

			$html .= "<div id='{$section_id}' class='detail-section {$appBoxClass}{$class_modifier}'>";
			$html .= "<div><h1 class='{$highlight_class}'>{$type}: {$this->appli[$type]}</h1></div>";
			$html .= "</div>";
			
			if ($is_active && !$first_active_set) {
				$box_class = "side-detail-list-active";
				$first_active_set = true;
			}
			$side_html .= "<div id='side-box-" . strtolower($type) . "' class='{$box_class} {$highlight_class} {$appBoxClass}'>";
			foreach ($data as $item) {
				$tag = $item['tag'];
				$value = $item['value'];
				$value = $tag == "Flat Damage" && $this->appli["Life"] == 0 ? 0 : $value;
				$no_percentage_tags = ["Capacity", "Flat Damage Bonus", "Mana Limit", "Synchronize"];
				$extension = (!in_array($tag, $no_percentage_tags) && $tag !== "") ? "%" : "";
				$formatted_value = is_numeric($value) ? "" . number_format($value) : $value;
				$side_html .= "<div class='detail-item app-item player-table-stat'>
								<div class='stat-section-left'>{$tag}: </div>
								<div class='stat-section-right'>" . $formatted_value . "{$extension}</div>
								</div>";
			}
			$side_html .= "<div class='appli-image'></div>";
			$side_html .= "</div>";
		}
			$html .= "</div>";
			$html .= "<div id='side-detail-box'>" . $side_html . "</div>";
		$html .= "</div>";
		$html .= "</div></div>";
		return $html;
	}

	private function create_element_section($z, $total_multi, $is_active, $total_contribution = 0) {
		global $element_names;
		$temp_icon = '<img src="./gallery/Icons/Elements/' . $element_names[$z] . '.webp" class="icon-small"" alt="' . $element_names[$z] . '">';
		$temp_dmg_str = "&lpar;Mult: " . number_format(intval(round($this->elemental_mult[$z] * 100))) . "%&rpar;";
		$temp_pen_str = "&lpar;Pen: " . number_format(intval(round($this->elemental_pen[$z] * 100))) . "%&rpar;";
		$temp_curse_str = "&lpar;Curse: " . number_format(intval(round($this->elemental_curse[$z] * 100))) . "%&rpar;";
		$contribution_display = $is_active
			? "<h1 class='elemental-highlight-" . $element_names[$z] . "'>" . number_format(round(($total_multi / $total_contribution) * 100, 1), 1) . "%</h1>"
			: "<h1 class='elemental-highlight-" . $element_names[$z] . "'>0%</h1>";
		$class_modifier = $is_active ? "" : " inactive-element";
		$html = "<div class='element-section eleBox-{$element_names[$z]}{$class_modifier}'>";
		$html .= "<div class='total-box detail-item'>{$temp_icon}<h1 class='elemental-highlight-" . $element_names[$z] . "'>" . number_format(intval(round($total_multi))) . "%</h1></div>";
		$html .= "<div class='detail-item'>{$temp_dmg_str}</div>";
		$html .= "<div class='detail-item'>{$temp_pen_str}</div>";
		$html .= "<div class='detail-item'>{$temp_curse_str}</div>";
		$html .= $contribution_display;
		$html .= "</div>";
		return $html;
	}
	
	public function display_misc_stats() {
		global $boss_list;
		$html = "<div id='player-info'>" . $this->player_header() . "<div id='player-box-content'>";
			$html .= "<div class='player-table-title'><span>Misc. Stats</span></div>";
			$html .= "<div id='stat-section'>";
				// Banes
				$banes_sliced = array_slice($this->banes, 0, -1);
				foreach ($banes_sliced as $idh => $bane_value) {
					if ($idh < 5) {
						$html .= "<div class='player-table-stat'>";
							$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3>{$boss_list[$idh]} Bane: </h3></div>";
							$html .= "<div class='stat-section-right'><p>" . show_num($bane_value) . "%</p></div>";
						$html .= "</div>";
					} else {
						$html .= "<div class='player-table-stat'>";
							$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3>Human Bane: </h3></div>";
							$html .= "<div class='stat-section-right'><p>" . show_num($bane_value) . "%</p></div>";
						$html .= "</div>";
					}
				}
				// Class Mastery
				$html .= "<div class='player-table-stat'>";
					$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3 class='unconditional'>Class Mastery Rate: </h3></div>";
					$html .= "<div class='stat-section-right'><p>" . number_format(show_num($this->class_multiplier)) . "%</p></div>";
				$html .= "</div>";
				// Class Mastery TOTAL
				$html .= "<div class='player-table-stat'>";
					$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3 class='unconditional'>Class Mastery Total: </h3></div>";
					$html .= "<div class='stat-section-right'><p>" . number_format(show_num($this->total_class_mult)) . "%</p></div>";
				$html .= "</div>";
				// Final Damage
				$html .= "<div class='player-table-stat'>";
					$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3 class='unconditional'>Final Damage: </h3></div>";
					$html .= "<div class='stat-section-right'><p>" . number_format(show_num($this->final_damage)) . "%</p></div>";
				$html .= "</div>";
				// Defence Penetration
				$html .= "<div class='player-table-stat'>";
					$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3 class='unconditional'>Defence Penetration: </h3></div>";
					$html .= "<div class='stat-section-right'><p>" . number_format(show_num($this->defence_pen)) . "%</p></div>";
				$html .= "</div>";
				// Damage Limit
				$html .= "<div class='player-table-stat'>";
					$html .= "<div class='stat-section-left'><img src='/images/Icons/diamonds-four-fill.png' alt='stat icon' class='icon-small stat-icon'/><h3 class='unconditional'>Boss Damage Limit: </h3></div>";
					$limit_display = $this->limit_shift <= 1 ? "FREE" : number_format(show_num($this->limit_shift));
					$html .= "<div class='stat-section-right'><p>" . $limit_display . "%</p></div>";
				$html .= "</div>";
			$html .= "</div>";
		$html .= "</div></div>";
		return $html;
	}
	
	
	
	public function display_elemental_breakdown($e_weapon) {
		global $element_names;
		if ($e_weapon) {
			$html = '<div id="spread-section"><div class="elemental-bar">';
			$temp_element_list = limit_elements($this, $e_weapon);
			$used_elements = [];
			$used_multipliers = [];
			foreach ($temp_element_list as $i => $is_used) {
				if ($is_used) {
					$element_icon = '<img src="./gallery/Icons/Elements/' . $element_names[$i] . '.webp" class="tooltip-icon" alt="' . $element_names[$i] . '">';
					$used_elements[] = [
						'icon' => $element_icon,
						'name' => $element_names[$i],
						'multiplier' => (1 + $this->elemental_mult[$i]) * (1 + $this->elemental_pen[$i]) * (1 + $this->elemental_curse[$i])
					];
				}
			}
			usort($used_elements, function($a, $b) {
				return $b['multiplier'] <=> $a['multiplier'];
			});
			if (!empty($used_elements)) {
				$total_contribution = array_sum(array_column($used_elements, 'multiplier'));
				$segment_count = count($used_elements);
				foreach ($used_elements as $index => $element) {
					$contribution = number_format(round(($element['multiplier'] / $total_contribution) * 100, 1), 1);
					$width = $contribution . '%';
					$class = '';
					if ($index === 0) {
						$class .= ' start';
					}
					if ($index === $segment_count - 1) {
						$class .= ' end';
					}
					$html .= '<div class="element-segment ' . $element['name'] . $class . '" style="width: ' . $width . ';">
								<span class="tooltip eleBox-' . $element['name'] . '">' . $element['icon'] . ' ' . $element['name'] . ' ' . $contribution . '%</span>
							  </div>';
				}
			}
			$html .= '</div></div>';
			return $html;
		}
		return '';
	}
	
	public function unique_ability_multipliers($item) {
        global $rare_ability_dict, $boss_list, $element_special_names;
        if (empty($item->item_bonus_stat)) {
            return;
        }
        if ($item->item_tier >= 5) {
            $current_ability = $rare_ability_dict[$item->item_bonus_stat];
            $this->final_damage += 0.25 * ($item->item_tier - 4);
            if (array_key_exists($current_ability[0], $this->appli)) {
                $this->appli[$current_ability[0]] += $current_ability[1];
            } else {
                $this->{$current_ability[0]} = $current_ability[1];
            }
        } else {
            $keywords = explode(' ', $item->item_bonus_stat);
            if ($item->item_type == "Y") {
                if (in_array($keywords[0], $boss_list)) {
                    $this->banes[array_search($keywords[0], $boss_list)] += 0.5;
                } elseif ($keywords[0] == "Human") {
                    $this->banes[5] += 0.5;
                }
                return;
            }
            $element_position = array_search($keywords[0], $element_special_names);
            if ($item->item_type == "G") {
                $this->elemental_mult[$element_position] += 0.25;
                return;
            }
            if ($item->item_type == "C") {
                $this->elemental_pen[$element_position] += 0.25;
                return;
            }
        }
    }
}

function get_player_by_id($search_input, $check_method="player") {
	$query = "SELECT * FROM PlayerList WHERE player_id = " . intval($search_input);
	if ($check_method == "all"){
		$query = "SELECT * FROM PlayerList WHERE player_id = '" . intval($search_input) . "' OR player_username = '" . addslashes($search_input) . "' OR discord_id = '" . addslashes($search_input) . "'";
	} else if ($check_method == "discord") {
		$query = "SELECT * FROM PlayerList WHERE discord_id = " . addslashes($search_input);
	}
	$result = run_query($query);
	if ($result && count($result) > 0) {
		$player_profile = new PlayerProfile();
		$data = $result[0];
		$player_profile->player_id = $data['player_id'];
		$player_profile->discord_id = $data['discord_id'];
		$player_profile->player_username = $data['player_username'];
		$player_profile->player_exp = $data['player_exp'];
		$player_profile->player_level = $data['player_level'];
		$player_profile->player_echelon = $data['player_echelon'];
		$player_profile->player_class = $data['player_class'];
		$player_profile->player_quest = $data['player_quest'];
		$player_profile->quest_tokens = array_map('intval', explode(';', $data['quest_tokens']));
		$player_profile->player_coins = $data['player_coins'];
		$player_profile->player_stamina = $data['player_stamina'];
		$player_profile->player_stats = array_map('intval', explode(';', $data['player_stats']));
		$player_profile->player_equipped = array_map('intval', explode(';', $data['player_equipped']));
		$player_profile->player_pact = $data['player_pact'];
		$player_profile->player_insignia = $data['player_insignia'];
		$player_profile->equipped_tarot = $data['player_tarot'];
		$player_profile->load_misc_data();
		return $player_profile;
	} else {
		return null;
	}
}

function apply_singularity($data_list, $bonus) {
    $highest_index = array_keys($data_list, max($data_list))[0];
    $data_list[$highest_index] += $bonus;
}

function show_num($input_number, $adjust = 100) {
    return intval(round($input_number * $adjust));
}

function get_max_exp($level) {
    return ($level < 100) ? 1000 * $level : 100000 + (50000 * floor($level / 100));
}

?>
