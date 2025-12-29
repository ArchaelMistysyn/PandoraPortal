<?php
	$sov_item = [
		"Pandora's Universe Hammer" => [
			[9999999, 9999999], // Base damage min/max
			[2, 3], // Attack speed min/max
			["Genesis Dream (TYPE)", "Universal Advent"], // Skills
			["Divine Genesis (TYPE)", "Entwined Universe"] // Sacred Skills
		],
		"Fallen Lotus of Nephilim" => [
			[1, 1], [1, 1],
			["Blood Blossom", "Phase Breaker"],
			["Divine Blossom", "Reality Breaker"]
		],
		"Solar Flare Blaster" => [
			[1111111, 7777777], [7, 7],
			["Blazing Dawn [VALUE]%", "Solar Winds"],
			["Blazing Apex [VALUE]%", "Divine Winds"]
		],
		"Bathyal, Enigmatic Chasm Bauble" => [
			[1234567, 7654321], [1, 4],
			["Mana of the Boundless Ocean", "Disruption Boundary (TYPE)"],
			["Mana of the Divine Sea", "Forbidden Boundary (TYPE)"]
		],
		"Ruler's Crest" => [
			[9999999, 9999999], null,
			["Ruler's Glare", "Ruler's Tenacity"],
			["Stasis Zone", "Divine Aegis"]
		]
	];
	
	$low_tier_skills = [
		// Wings (Feathers): Elemental Damage 25%
		"Volcanic Feathers" => "Fire Damage 25%",
		"Aquatic Feathers" => "Water Damage 25%",
		"Voltaic Feathers" => "Lightning Damage 25%",
		"Seismic Feathers" => "Earth Damage 25%",
		"Cyclonic Feathers" => "Wind Damage 25%",
		"Arctic Feathers" => "Ice Damage 25%",
		"Aphotic Feathers" => "Shadow Damage 25%",
		"Seraphic Feathers" => "Light Damage 25%",
		"Cosmic Feathers" => "Celestial Damage 25%",

		// Crests (Authority): Elemental Penetration 25%
		"Volcanic Authority" => "Fire Penetration 25%",
		"Aquatic Authority" => "Water Penetration 25%",
		"Voltaic Authority" => "Lightning Penetration 25%",
		"Seismic Authority" => "Earth Penetration 25%",
		"Cyclonic Authority" => "Wind Penetration 25%",
		"Arctic Authority" => "Ice Penetration 25%",
		"Aphotic Authority" => "Shadow Penetration 25%",
		"Seraphic Authority" => "Light Penetration 25%",
		"Cosmic Authority" => "Celestial Penetration 25%"
	];
	
	$random_values_dict = [
		"Solar Flare Blaster" => [100, 500]
	];

	$type_dict = [
		"Bathyal, Enigmatic Chasm Bauble" => ["Critical", "Fractal", "Temporal", "Hyperbleed", "Bloom"],
		"Pandora's Universe Hammer" => array_merge($element_names, array_slice($path_names, 0, -4), ["Omni"])
	];


	class CustomItem {
		public $player_id, $item_type, $item_tier, $item_base_type, $is_sacred;
		public $item_id, $item_name, $item_enhancement, $item_quality_tier, $item_elements;
		public $item_num_rolls, $item_roll_values, $item_base_stat, $item_bonus_stat;
		public $base_damage_min, $base_damage_max, $item_damage_min, $item_damage_max;
		public $item_num_sockets, $item_inlaid_gem_id, $item_damage_type;

		public function __construct($player_id, $item_type, $item_tier, $item_base_type="", $is_sacred=false, $random_enhance=false) {
			global $sovereign_item_list, $ring_skill_data, $max_enhancement, $class_names;
			$this->player_id = $player_id;
			$this->item_type = $item_type;
			$this->item_tier = $item_tier;
			$this->item_base_type = $item_base_type;
			$this->is_sacred = $is_sacred;
			$this->item_id = 0;
			$this->item_name = "";
			$this->item_enhancement = 0;
			$this->item_quality_tier = 1;
			$this->item_elements = array_fill(0, 9, 0);
			$this->item_num_rolls = 0;
			$this->item_roll_values = [];
			$this->item_base_stat = 0.0;
			$this->item_bonus_stat = "";
			$this->base_damage_min = 0;
			$this->base_damage_max = 0;
			$this->item_damage_min = 0;
			$this->item_damage_max = 0;
			$this->item_num_sockets = $this->is_sacred ? 1 : 0;
			$this->item_inlaid_gem_id = 0;
			$this->item_damage_type = $class_names[array_rand($class_names)];
			$this->generate_base();
			$this->get_tier_damage();
			
			if ($this->item_type == "R") {
				$this->item_quality_tier = 0;
				$ring_element_data = $ring_skill_data[$this->item_base_type]['element'];
				if ($ring_element_data[0] == "all") {
					$this->item_elements = array_fill(0, 9, 1);
				} else {
					foreach ($ring_element_data as $element_index) {
						$this->item_elements[$element_index] = 1;
					}
				}
			} else if (in_array($this->item_base_type, $sovereign_item_list)) {
				$this->item_quality_tier = 0;
				$this->build_sovereign_item();
			}
			if ($random_enhance && $this->item_tier > 0) {
				$max = $max_enhancement[$this->item_tier - 1];
				$roll = random_int(1, 1000);
				if ($roll <= 1) {
					$this->item_enhancement = $max;
				} else {
					$rare_bonus = ($roll >= 50) ? 1 : (($roll > 10) ? 2 : 4);
					$tier_val = generate_random_tier(1, min($this->item_tier, 8));
					$this->item_enhancement = min(($tier_val - 1) * 5 * $rare_bonus + random_int(5 * $rare_bonus, 15 * $rare_bonus), $max);
				}
			}
			$this->update_damage();
			$this->set_item_name();
		}

		public function saveChanges($method = "update") {
			$updated_roll_values = implode(";", array_map('strval', $this->item_roll_values));
			$updated_elements = implode(";", array_map('intval', $this->item_elements));
			$fields = [
				"player_id" => intval($this->player_id),
				"item_name" => "'" . addslashes($this->item_name) . "'",
				"item_type" => "'" . addslashes($this->item_type) . "'",
				"item_base_type" => "'" . addslashes($this->item_base_type) . "'",
				"item_damage_type" => "'" . addslashes($this->item_damage_type) . "'",
				"item_tier" => intval($this->item_tier),
				"item_enhancement" => intval($this->item_enhancement),
				"item_quality_tier" => intval($this->item_quality_tier),
				"item_elements" => "'" . addslashes($updated_elements) . "'",
				"item_roll_values" => "'" . addslashes($updated_roll_values) . "'",
				"item_base_stat" => number_format($this->item_base_stat, 2, '.', ''),
				"item_bonus_stat" => "'" . addslashes(strval($this->item_bonus_stat)) . "'",
				"item_base_dmg_min" => intval($this->base_damage_min),
				"item_base_dmg_max" => intval($this->base_damage_max),
				"item_num_sockets" => intval($this->item_num_sockets),
				"item_inlaid_gem_id" => intval($this->item_inlaid_gem_id)
			];
			if ($method === "new") {
				$columns = implode(", ", array_keys($fields));
				$values = implode(", ", array_values($fields));
				$query = "INSERT INTO CustomInventory ($columns) VALUES ($values)";
			} else {
				$assignments = implode(", ", array_map(fn($k, $v) => "$k = $v", array_keys($fields), $fields));
				$query = "UPDATE CustomInventory SET $assignments WHERE item_id = " . intval($this->item_id);
			}
			run_query($query, false);
			$this->item_id = fetchInsertedItemId($this);
		}
		
		public function display_item($is_gem=false, $method=null) {
			global $sovereign_item_list, $ring_skill_data, $ring_category, $path_names, $gem_point_dict, $low_tier_skills;
			$quality = '';
			$name = '';
			$tooltip = '';
			if (preg_match('/^(.*?)\s*(\[[^\]]+\])$/', $this->item_name, $matches)) {
				$name = $matches[1];
				$quality = $matches[2];
			} else {
				$name = $this->item_name;
			}
			if (empty ($quality)) {
				if (in_array($this->item_name, $sovereign_item_list)) {
					$quality = "[Sovereign]";
				} else if (array_key_exists($this->item_name, $ring_skill_data)) {
					$ring_tier = $ring_skill_data[$this->item_name]['tier'];
					$quality = "[" . $ring_category[$ring_tier] . "]";
				}
			}
			$thumbnail_url = $this->get_gear_thumbnail();
			$html = '<img src="' . $thumbnail_url . '" alt="' . $this->item_name . '" class="item-thumbnail">';
			if ($method !== "basic" && ($this->item_inlaid_gem_id != 0 || $is_gem)) {
				$html .= '<button type="button" class="slot-toggle input-button" onclick="toggleSlotDisplay(\'' . $this->item_type . '\')">Toggle</button>';
			}
			$html .= "<h1 class='item-name highlight-text'>" . $name . "</h1>";
			$html .= "<div class='style-line'></div>";
			$html .= $this->generate_stars();
			$html .= $this->generate_element_icons();
			$html .= "<div class='style-line'></div>";
			// Tags
			$html .= "<div class='badge-container'>";	
			$html .= "<div class='item-name-badge'>" . $quality . "</div>";
			$html .= "<div class='item-id-badge'>ID: " . $this->item_id . "</div>";
			$html .= "<div class='item-tier-badge'>Tier: " . $this->item_tier . "</div>";
			$html .= "<div class='item-gear-score-badge'><span class='star-symbol'>★</span>: " . $this->get_gear_score() . "</div>";
			$html .="</div>";
			// Item Data
			$html .= "<div class='style-line'></div>";
			$html .= "<div class='item-dmg-stat'>Base: " . number_format($this->item_damage_min) . " - " . number_format($this->item_damage_max) . "</div>";
			if (!$is_gem) {
				$html .= '<img src="../gallery/Icons/Classes/' . $this->item_damage_type . '.webp" alt="' . $this->item_damage_type . '" class="item-class-thumbnail">';
				if ($this->item_type == 'R'){
					$bonus_stat_msg = '- - -';
				}
				$bonus_stat_msg = $this->item_bonus_stat;
				$base_type = '';
				$aux_suffix = '';
				if ($this->item_type == "W") {
					$base_type = "Base Attack Speed ";
					$aux_suffix = "/min";
				} elseif ($this->item_type == "A") {
					$base_type = "Base Damage Mitigation ";
					$aux_suffix = "%";
				}
				if ($this->item_base_stat != 0.0) {
					if (is_int($this->item_base_stat)) {
						$display_base_stat = intval($this->item_base_stat);
					} else {
						$display_base_stat = rtrim(rtrim(number_format($this->item_base_stat, 2), '0'), '.');
					}
					$base_stat_msg = "{$base_type}{$display_base_stat}{$aux_suffix}";
				}
				$tier_specifier = [5 => "Void", 6 => "Wish", 7 => "Abyss", 8 => "Divine", 9 => "Sacred"];
				$application_mapping = [
					"Overflow" => "Elemental Application +2",
					"Mastery" => "Class Mastery 10%",
					"Immortality" => "HP cannot reduce below 1 (Doesn't work in PvP)",
					"Omega" => "Critical Application +1",
					"Combo" => "Combo Application +1",
					"Reaper" => "Bleed Application +1",
					"Overdrive" => "Ultimate Application +1",
					"Unravel" => "Temporal Application +1",
					"Manatide" => "Mana Application +1",
					"Vitality" => "Life Application +1"
				];
				if ($this->item_tier >= 5 && $this->item_type != "W") {
					$bonus_stat_msg = "{$tier_specifier[$this->item_tier]} Application ({$this->item_bonus_stat})";
					$final_damage = ($this->item_tier - 4) * 25;
					$tooltip = $application_mapping[$this->item_bonus_stat];
					$tooltip = "<span class='tooltip'>{$tooltip}. Final Damage +{$final_damage}%</span>";
				} else if ($this->item_tier < 5 && in_array($this->item_type, ["G", "C"])) {
					if (isset($low_tier_skills[$this->item_bonus_stat])) {
						$bonus_stat_msg = $this->item_bonus_stat;
						$tooltip = "<span class='tooltip'>{$low_tier_skills[$this->item_bonus_stat]}</span>";
					}
				}
			} else {
				$points_value = $gem_point_dict[$this->item_tier];
				$path_name = $path_names[intval($this->item_bonus_stat)];
				$bonus_stat_msg = "Path of {$path_name} +{$points_value}";
			}
			if (isset($base_stat_msg)) {
				$html .= "<div class='stat-message'>" . $base_stat_msg . "</div>";
			}
			if (isset($bonus_stat_msg)) {
				$html .= "<div class='stat-message'>" . $bonus_stat_msg . $tooltip . "</div>";
			}
			$html .= "<div class='style-line'></div></span>";
			if ($this->item_type == 'R') {
				$html .= $this->display_ring_skills();
			} else {
				$html .= $this->display_item_skills();
			}
			return $html;
		}
		
		public function display_item_skills() {
			global $skill_data, $sovereign_item_list, $keyword_data;
			if (empty($this->item_roll_values)) {
				return "No skills found.";
			}
			$skill_display = '';
			foreach ($this->item_roll_values as $skill) {
				if (!in_array($this->item_base_type, $sovereign_item_list)) {
					$skill_obj = new ItemRoll($skill);
					$skill_icon = $skill_obj->roll_icon;
					$skill_name = $skill_data[$skill_obj->roll_code][0];
					$skill_value = $skill_obj->roll_tier * $skill_data[$skill_obj->roll_code][1];
					if (strpos($skill_name, 'X') !== false) {
						$skill_name = str_replace('X', $skill_value, $skill_name);
					} else {
						$skill_name = "$skill_name +$skill_value%";
					}
					$tier_class = "tier-$skill_obj->roll_tier";
				} else if ($this->item_tier >= 8 && !empty($skill)) {
					$skill_icon = '';
					$skill_name = $skill;
					$tier_class = "tier-$this->item_tier";
				}
				$skill_tag = preg_replace('/[\(\[].*?[\)\]]/', '', $skill_name);
				$skill_tag = trim($skill_tag);
				$tooltip = "<span class='skill-name'>$skill_name</span>";
				if (isset($skill_icon)){
					//$tooltip = "<div class='format-icon'><img class='aug-icon' src='" . $skill_icon . "' />" . $tooltip . "</div>";
				}
				if (isset($keyword_data[$skill_tag])) {
					$description = $keyword_data[$skill_tag]['description'];
					$tooltip .= "<span class='tooltip'>{$description}</span>";
					$tooltip .= "<span class='tooltip'>{$description}</span>";
				}
				$skill_display .= "<div class=\"skill-slot $tier_class\">$tooltip</div>";
			}
			return $skill_display;
		}

		public function display_ring_skills() {
			global $ring_skill_data, $sovereign_item_list, $tarot_data, $keyword_data, $scaling_rings;
			if (!isset($this->item_base_type) || !isset($ring_skill_data[$this->item_base_type])) {
				return "No ring skills found.";
			}
			$skills = $ring_skill_data[$this->item_base_type]['skills'];
			$final_damage = $this->item_tier * 10;
			$attack_speed = $this->item_tier * 5;
			$resonance = null;
			if (isset($ring_skill_data[$this->item_base_type]['resonance'])) {
				$resonance_index = $ring_skill_data[$this->item_base_type]['resonance'];
				if ($resonance_index === "Random") {
					$pos = ($this->item_base_type == "Crown of Skulls" || $this->item_base_type == "Chromatic Tears") ? 2 : 0;
					$resonance_roll = $this->item_roll_values[$pos] ?? 'Unknown';
					$resonance = get_resonance_text($resonance_roll);
				} else {
					$resonance = get_resonance_text($resonance_index);
				}
			}
			$skill_display = "<div class=\"skill-slot tier-$this->item_tier\">Final Damage +$final_damage%</div>";
			$skill_display .= "<div class=\"skill-slot tier-$this->item_tier\">Attack Speed +$attack_speed%</div>";
			$scaling_count = 0;
			foreach ($skills as $skill) {
				$skill_name = $skill['name'];
				$skill_tag = preg_replace('/[\(\[].*?[\)\]]/', '', $skill_name);
				$skill_tag = trim($skill_tag);
				if (!isset($skill['value'])) {
					if (in_array($this->item_base_type, $scaling_rings)) {
						$scaling_bonus = intval($this->item_roll_values[$scaling_count]);
						$skill_output = "$skill_name [$scaling_bonus]";
						$scaling_count++;
					} else {
						$skill_output = $skill_name;
					}
				} else {
					$skill_output = $skill_name . " +" . $skill['value'] . "%";
				}
				
				if (isset($keyword_data[$skill_tag])) {
					$skill_display .= "<div class=\"skill-slot tier-$this->item_tier\">";
					$skill_display .= "<span class='skill-name'>$skill_output</span>";
					$skill_display .= "<span class='tooltip'>{$keyword_data[$skill_tag]['description']}</span>";
					$skill_display .= "</div>";
				} else {
					$skill_display .= "<div class=\"skill-slot tier-$this->item_tier\">$skill_output</div>";
				}
			}
			if ($resonance) {
				$skill_display .= "<div class=\"skill-slot tier-$this->item_tier\">Resonance [$resonance]</div>";
			}
			return $skill_display;
		}
		
		public function assign_ring_values($player_obj) {
			global $ring_skill_data, $scaling_rings;
			if ($this->is_sacred) {
				$player_obj->defence_pen += 0.5;
			}
			$ring_data = $ring_skill_data[$this->item_base_type];
			$bonuses = $ring_data['skills'];
			if (isset($ring_skill_data[$this->item_base_type]['path'])) {
				$path_index = $ring_skill_data[$this->item_base_type]['path'];
				$player_obj->gear_points[$path_index] += 10;
			}
			$player_obj->final_damage += $this->item_tier * 0.1;
			$player_obj->attack_speed += $this->item_tier * 0.05;
			if (in_array($this->item_base_type, $scaling_rings)) {
				if ($this->item_base_type == "Chromatic Tears") {
					$player_obj->all_elemental_mult += intdiv(intval($this->item_roll_values[1]), 100);
					$player_obj->all_elemental_pen += intdiv(intval($this->item_roll_values[1]), 100);
					$player_obj->all_elemental_curse += intdiv(intval($this->item_roll_values[0]), 100) + intdiv(intval($this->item_roll_values[1]), 100);
				}
				return;
			}
			foreach ($bonuses as $bonus) {
				if (!isset($bonus['attr_name'])) {
					continue;
				}
				$attr_name = $bonus['attr_name'];
				$value = $bonus['value'] ?? 0;
				$index = $bonus['index'] ?? null;
				$percent_adjust = (strpos($attr_name, "Application") === false && strpos($attr_name, "All-In") === false) ? 0.01 : 1;
				if (is_null($index)) {
					if (isset($player_obj->$attr_name)) {
						$player_obj->$attr_name += $value * $percent_adjust;
					}
				} elseif (is_array($index)) {
					foreach ($index as $idx) {
						if (isset($player_obj->{$attr_name}[$idx])) {
							$player_obj->{$attr_name}[$idx] += $value * $percent_adjust;
						}
					}
				} else {
					if (isset($player_obj->{$attr_name}[$index])) {
						$player_obj->{$attr_name}[$index] += $value * $percent_adjust;
					}
				}
			}
		}

		public function get_gear_score() {
			$tier_score = ($this->item_tier > 8) ? 999 : 0;
			$enhancement_score = 1500;
			$quality_score = 1500;
			$base_damage_score = 0;
			$rolls_score = 3000;
			$base_max = 150000;
		
			if ($this->item_type !== "R" && strpos($this->item_type, "D") === false) {
				$enhancement_score = round(($this->item_enhancement / 200) * 1500);
				$quality_score = round(($this->item_quality_tier / 5) * 1500);
				$base_max = 250000;
			}
		
			$base_stat_max = ($this->item_type === "W") ? 4.0 : (($this->item_type === "A") ? 30.0 : 0);
			$base_stat_score = ($base_stat_max > 0) ? round(($this->item_base_stat / $base_stat_max) * 1500) : 1500;
		
			if ($this->item_type !== "R" && in_array($this->item_base_type, $GLOBALS['sovereign_item_list'])) {
				$base_damage_score = 1500;
			} elseif ($this->base_damage_min > 0 && $this->base_damage_max > 0) {
				$base_damage_score = round(($this->base_damage_min / $base_max) * 750) +
									 round(($this->base_damage_max / $base_max) * 750);
			}
		
			if ($this->item_type === "R") {
				$rolls_score = round(min($this->item_tier, 8) / 8 * 3000);
			} elseif (!in_array($this->item_base_type, $GLOBALS['sovereign_item_list'])) {
				$roll_scores = array_map(function ($roll) {
					return round(min(intval($roll[0]), 8) / 8 * 500);
				}, $this->item_roll_values);
				$rolls_score = array_sum($roll_scores);
			}
		
			return $tier_score + $enhancement_score + $quality_score + $base_stat_score + $base_damage_score + $rolls_score;
		}
		
		
		public function generate_base() {
			global $weapon_type_dict, $armour_base_dict, $wing_base_dict, $crest_base_list;
			// Skip weapons and gems
			if ($this->item_type !== "W" && strpos($this->item_type, "D") === false) {
				$this->assign_bonus_stat();
			}
			if ($this->item_type === "R") {
				// Specific handling for rings
				$this->item_roll_values = array_fill(0, 6, null);
				return;
			}
			// Handle gem types
			if (strpos($this->item_type, "D") !== false) {
				add_roll($this, 6);
				$this->item_bonus_stat = strval(rand(0, 6));
				return;
			}
			// Handle base types for non-gem items
			if ($this->item_base_type !== "") {
				return;
			}
			// Add a roll and element to non-gem items
			add_roll($this, 1);
			$this->add_item_element(9);
			// Handle non-gem items
			switch ($this->item_type) {
				case "W":
					$this->set_base_attack_speed();
					$item_data = $weapon_type_dict[$this->item_damage_type];
					$combined_list = $this->item_tier >= 5 ? array_merge($item_data[1], $item_data[2]) : array_merge($item_data[0], $item_data[1]);
					$this->item_base_type = $combined_list[array_rand($combined_list)];
				case "A":
					$this->set_base_damage_mitigation();
					$this->item_base_type = $armour_base_dict[min(5, $this->item_tier)];
					break;
				case "V":
					$this->item_base_type = "Greaves";
					break;
				case "Y":
					$this->item_base_type = $this->item_tier >= 5 ? "Amulet" : "Necklace";
					break;
				case "G":
					$this->item_base_type = $wing_base_dict[$this->item_tier];
					break;
				case "C":
					$this->item_base_type = $this->item_tier >= 5 ? "Diadem" : $crest_base_list[array_rand($crest_base_list)];
					break;
				default:
					$this->item_base_type = "base_type_error";
					break;
			}
		}
		
		public function set_base_attack_speed() {
			global $speed_range_list;
			$selected_range = $speed_range_list[$this->item_tier - 1];
			$this->item_base_stat = round(mt_rand(intval($selected_range[0] * 100), intval($selected_range[1] * 100)) / 100, 2);
		}

		public function set_base_damage_mitigation() {
			$this->item_base_stat = 30.00;
			if ($this->item_tier < 9) {
				$this->item_base_stat = round(mt_rand(1000, 1400) / 100, 2) + $this->item_tier * 2;
			}
		}
		
		public function get_tier_damage() {
			global $damage_tier_list;
			$damage_values = $damage_tier_list[$this->item_tier - 1];
			if ($this->item_base_type === "Crown of Skulls") {
				return;
			}
			if (strpos($this->item_type, "D") !== false && $this->item_tier == 8) {
				$damage_values = [150000, 150000];
			}
			$damage_adjust = ($this->item_type === "W") ? 2 : 1;
			// Generate two random damage values
			$damage1 = rand($damage_values[0], $damage_values[1]) * $damage_adjust;
			$damage2 = rand($damage_values[0], $damage_values[1]) * $damage_adjust;
			$this->base_damage_min = min($damage1, $damage2);
			$this->base_damage_max = max($damage1, $damage2);
		}
		
		public function add_item_element($element) {
			$new_element = ($element === 9) ? rand(0, 8) : $element;
			$this->item_elements[$new_element] = 1;
		}

		public function assign_bonus_stat() {
			global $element_special_names, $boss_list, $rare_ability_dict;
			if ($this->item_type === "W" || strpos($this->item_type, "D") !== false) {
				return;
			}
			if ($this->item_tier < 5) {
				if (in_array($this->item_type, ["A", "V"])) {
					return;
				}
				$keyword = $element_special_names[array_rand($element_special_names)];
				$this->item_bonus_stat = "{$keyword} Authority";
				if (in_array($this->item_type, ["Y", "R"])) {
					$bane_type = rand(0, 5);
					$keyword = ($bane_type === 5) ? "Human" : $boss_list[$bane_type];
					$this->item_bonus_stat = "{$keyword} Bane";
					return;
				}
				if ($this->item_type === "G") {
					$this->item_bonus_stat = "{$keyword} Feathers";
					return;
				}
				return;
			}
			$this->item_bonus_stat = array_rand($rare_ability_dict);
		}
		
		public function update_damage() {
			global $sovereign_item_list;
			$flat_bonus = 0;
			$mult_bonus = 1;
			if (strpos($this->item_type, "D") !== false) {
				$this->item_damage_min = $this->base_damage_min;
				$this->item_damage_max = $this->base_damage_max;
				return;
			}
			if ($this->item_base_type === "Crown of Skulls" && !empty($this->roll_values[0])) {
				$flat_bonus = intval($this->item_roll_values[0]) * 100000;
				$mult_bonus = 1 + (intval($this->item_roll_values[1]) * 0.001);
			}
			$enh_multiplier = 1 + $this->item_enhancement * (0.01 * $this->item_tier);
			$quality_damage = 1 + ($this->item_quality_tier * 0.2);
			$this->item_damage_min = intval(($this->base_damage_min + $flat_bonus) * $quality_damage * $enh_multiplier * $mult_bonus);
			$this->item_damage_max = intval(($this->base_damage_max + $flat_bonus) * $quality_damage * $enh_multiplier * $mult_bonus);
		}
		
		public function set_item_name() {
			global $sovereign_item_list, $tier_keywords, $quality_damage_map;
			global $tier_keywords, $gem_tier_keywords, $boss_list;
			if (in_array($this->item_base_type, $sovereign_item_list)) {
				$this->item_name = $this->item_base_type;
				if ($this->is_sacred) {
					$this->item_name .= " [Sacred]";
				}
				return;
			}
			if ($this->item_type === "R") {
				$this->item_name = $this->item_base_type;
				return;
			}
			if (strpos($this->item_type, "D") !== false) {
				$tier_keyword = $tier_keywords[$this->item_tier];
				$type_keyword = $gem_tier_keywords[$this->item_tier];
				$item_type = ($this->item_tier <= 4) ? "Gem" : "Jewel";
				$boss_name = $boss_list[intval(substr($this->item_type, 1))];
				$this->item_name = "$tier_keyword $boss_name Heart $item_type [$type_keyword]";
				return;
			}
			$tier_keyword = $tier_keywords[$this->item_tier];
			$quality_name = $quality_damage_map[max(4, $this->item_tier)][$this->item_quality_tier];
			$this->item_name = "+$this->item_enhancement $tier_keyword $this->item_base_type [$quality_name]";
		}
	
		public function generate_stars() {
			$star_display = '<div class="star-container">';
			for ($i = 1; $i <= 9; $i++) {
				if ($i <= $this->item_tier) {
					$star_display .= '<img src="./gallery/Icons/Stars/Star' . $this->item_tier . '.webp" class="icon-small">';
				} else {
					$star_display .= '<img src="./gallery/Icons/Stars/StarBlank.webp" class="icon-small">';
				}
			}
			$star_display .= '</div>';
			return $star_display;
		}
		
		public function get_gear_thumbnail($encode_filename = false) {
			global $ring_item_type, $sovereign_item_list, $tag_dict, $path_names;
			$folder = $item_tag = $this->item_base_type;
			$sub_folder = $element_index = "";
			if (in_array($this->item_base_type, $sovereign_item_list)) {
				$folder = "Sovereign";
			} elseif (!in_array($this->item_type, ["W", "R"])) {
				$item_tag = "Gem";
				if (isset($tag_dict[$this->item_type])) {
					$item_tag = $tag_dict[$this->item_type];
				}
				$folder = $item_tag;
			} elseif ($this->item_type == "R") {
				$folder = "Ring";
				$sub_folder = $ring_item_type[$this->item_tier - 1] . "/";
				if (in_array($this->item_tier, [4, 5])) {
					$item_tag = $ring_item_type[$this->item_tier - 1];
					$elements = is_array($this->item_elements)
						? array_map('intval', $this->item_elements)
						: array_map('intval', explode(';', $this->item_elements));
					$element_index = array_search(1, $elements, true);
				} elseif ($this->item_tier == 6) {
					$item_tag = $ring_item_type[$this->item_tier - 1];
					$path_name = trim(explode("Ring of ", $this->item_name)[1] ?? "");
					$element_index = array_search($path_name, $path_names);
				} elseif ($this->item_tier == 7) {
					$item_tag = $this->item_base_type;
				} else {
					return null;
				}
			}
			$new_tag = str_replace(' ', '_', $item_tag);
			$filename = "Frame_{$new_tag}{$element_index}_{$this->item_tier}.png";
			if ($encode_filename) {
				$filename = urlencode($filename);
			}
			return "./botimages/Gear_Icon/$folder/$sub_folder$filename";
		}
		
		public function generate_element_icons() {
			global $element_names;
			$tooltip_dict = [
				"W" => "Grants X Damage",
				"A" => "Grants X Res 10%",
				"V" => "Grants X Res 10%",
				"Y" => "Grants X Damage 25%",
				"R" => "Grants X Damage 25%",
				"G" => "Grants X Pen 25%",
				"C" => "Grants X Curse 10%"
			];
			if (is_string($this->item_elements)) {
				$elements = is_array($this->item_elements)
						? array_map('intval', $this->item_elements)
						: array_map('intval', explode(';', $this->item_elements));
			} elseif (is_array($this->item_elements)) {
				$elements = $this->item_elements;
			} else {
				return '';
			}
			$element_display = '<div class="element-icons">';
			foreach ($elements as $index => $value) {
				if ($value == 1) {
					$element_name = $element_names[$index];
					$tooltip = isset($tooltip_dict[$this->item_type]) ? str_replace('X', $element_name, $tooltip_dict[$this->item_type]) : "{$element_name}";
					$element_display .= '<div class="element-icon-container"><img src="./gallery/Icons/Elements/';
					$element_display .= $element_name . '.webp" class="icon-small" alt="' . $element_name . '">';
					$element_display .= '<span class="tooltip">' . $tooltip . '</span></div>';
				}
			}
			$element_display .= '</div>';
			return $element_display;
		}
		
		public function build_sovereign_item() {
			global $sov_item, $random_values_dict, $sov_type_dict;
			// Get the data for the specific sovereign item
			$sov_data = $sov_item[$this->item_base_type];
			$this->base_damage_min = $sov_data[0][0];
			$this->base_damage_max = $sov_data[0][1];
			if ($this->item_type == "W") {
				$this->item_base_stat = round(mt_rand($sov_data[1][0] * 100, $sov_data[1][1] * 100) / 100, 2);
			}
			// Assign random elements
			$num_elements = $this->weighted_random([1, 1, 2, 3, 4, 3, 2, 1, 1], 9);
			$range = range(0, 8);
			shuffle($range);
			$random_numbers = array_slice($range, 0, $num_elements);
			foreach ($random_numbers as $index) {
				$this->item_elements[$index] = 1;
			}
			// Assign skills
			$skill_data = $this->is_sacred ? $sov_data[3] : $sov_data[2];
			$this->item_name = $this->item_base_type;
			foreach ($skill_data as $skill) {
				$skill_text = $skill;
				if (strpos($skill, '[VALUE]') !== false) {
					$value_min = $random_values_dict[$this->item_base_type][0];
					$value_max = $random_values_dict[$this->item_base_type][1];
					$value = mt_rand($value_min, $value_max);
					$skill_text = str_replace('[VALUE]', number_format($value), $skill);
				} elseif (strpos($skill, '(TYPE)') !== false) {
					$new_type = $sov_type_dict[$this->item_base_type][array_rand($sov_type_dict[$this->item_base_type])];
					$skill_text = str_replace('TYPE', $new_type, $skill);
				}
				$this->item_roll_values[] = $skill_text;
			}
			if ($this->item_type == "W") {
				$this->item_roll_values[] = "Sovereign's Omniscience";
			}
			if ($this->is_sacred) {
				$this->item_roll_values[] = "Sacred Core";
			}
		}

		public function weighted_random($weights, $num_choices) {
			$cumulative_weight = array_sum($weights);
			$random_value = mt_rand(1, $cumulative_weight);
			$cumulative = 0;
			foreach ($weights as $key => $weight) {
				$cumulative += $weight;
				if ($random_value <= $cumulative) {
					return $key;
				}
			}
			return array_rand(range(0, $num_choices - 1), 1);
		}
		
		public function assign_sovereign_values($player_obj) {
			global $tarot_damage, $element_dict;
			if ($this->item_type == "R"){
				$this->assign_ring_values($player_obj);
				return;
			}
			if ($this->is_sacred) {
				$player_obj->defence_pen += 0.5;
			}
			switch ($this->item_base_type) {
				case "Pandora's Universe Hammer":
					$full_text = $this->item_roll_values[0];
					preg_match('/\((.*?)\)/', $full_text, $matches);
					if (!empty($matches)) {
						$variant = $matches[1];
					} else {
						$variant = '';
					}
					if ($this->is_sacred) {
						$damage_values = [30, 30, 30];
						$query = "SELECT num_stars FROM TarotInventory WHERE player_id = " . $player_obj->player_id;
						$result = run_query($query);
						$star_counts = array_count_values(array_column($result, 'num_stars'));
						$dmg_bonus = 0;
						foreach ($tarot_damage as $index => $value) {
							if (isset($star_counts[$index])) {
								$dmg_bonus += $value * $star_counts[$index];
							}
						}
						$player_obj->player_damage_min = $dmg_bonus;
						$player_obj->player_damage_max = $dmg_bonus;
					} else {
						$damage_values = [30, 20, 10];
					}
					foreach ($element_dict[$variant] as $ele_idx) {
						$player_obj->elemental_mult[$ele_idx] += $damage_values[0];
						$player_obj->elemental_pen[$ele_idx] += $damage_values[1];
						$player_obj->elemental_curse[$ele_idx] += $damage_values[2];
					}
					break;
				case "Fallen Lotus of Nephilim":
					$player_obj->resist_pen += 0.05 * min($player_obj->capacity, array_sum($this->item_elements));
					$player_obj->unique_conversion[4] = 1;
					if ($this->is_sacred) {
						$player_obj->resist_pen += 0.25;
						$player_obj->unique_conversion[4] = 2;
					}
					$player_obj->hp_multiplier += 0.5;
					break;
				case "Solar Flare Blaster":
					$player_obj->flare_type = "Solar";
					$reduction_value = 99;
					if ($this->is_sacred) {
						$player_obj->flare_type = "Zenith";
						$reduction_value = 0;
					}
					$parts = explode(' ', $this->item_roll_values[0]);
					$numeric_value = str_replace(",", "", rtrim($parts[2], "%"));
					$player_obj->apply_elemental_conversion($element_dict['Solar'], $reduction_value, intval($numeric_value));
					$player_obj->trigger_rate['Status'] = 5;
					break;
				case "Bathyal, Enigmatic Chasm Bauble":
					$parts = explode(' ', $this->item_roll_values[1]);
					$player_obj->perfect_rate[trim($parts[2], '()')] = 1;
					$parts = explode(' ', $this->item_roll_values[1]);
					$numeric_value = str_replace(",", "", rtrim($parts[5], "[]"));
					$player_obj->mana_limit = intval($numeric_value);
					$player_obj->mana_shatter = true;
					if ($this->is_sacred) {
						$player_obj->appli['Life'] += 5;
						$player_obj->appli['Mana'] += 5;
						$player_obj->start_mana = 0;
					}
					break;
				case "Ruler's Crest":
					$player_obj->hp_multiplier += 10;
					$player_obj->ruler_mult = $player_obj->player_level * 0.05;
					if ($this->is_sacred) {
						$player_obj->hp_multiplier += 10;
						$player_obj->ruler_mult *= 2;
					}
					break;
				default:
					return;
			}
		}
	}

	function fetchInsertedItemId($item) {
		$element_str = implode(";", array_map('intval', $item->item_elements ?? []));
		$query  = "SELECT item_id FROM CustomInventory WHERE player_id = {$item->player_id}";
		$query .= " AND item_name = '" . addslashes($item->item_name) . "'";
		$query .= " AND item_base_dmg_min = {$item->base_damage_min}";
		$query .= " AND item_base_dmg_max = {$item->base_damage_max}";
		$query .= " AND item_elements = '" . $element_str . "'";
		$query .= " ORDER BY item_id DESC LIMIT 1";
		$result = run_query($query);
		return $result[0]['item_id'] ?? 0;
	}

	function get_resonance_text($index) {
		global $tarot_data;
		if (isset($tarot_data[$index])) {
			$parts = explode(', ', $tarot_data[$index]['Name']);
			return $parts[1];
		}
		return 'Unknown';
	}
	
	
	function read_custom_item($item_id = null, $multi_id = null) {
		$item_list = [];
		if ($multi_id === null) {
			$query = "SELECT * FROM CustomInventory WHERE item_id = " . intval($item_id);
		} else {
			$query = "SELECT * FROM CustomInventory WHERE item_id IN ($multi_id)";
		}
		$result = run_query($query);
		if ($result && count($result) > 0) {
			foreach ($result as $item_data) {
				$item = new CustomItem(
					$item_data['player_id'], 
					$item_data['item_type'], 
					$item_data['item_tier'], 
					$item_data['item_base_type']
				);
				$item->item_id = $item_data['item_id'];
				$item->item_name = $item_data['item_name'];
				$item->item_enhancement = $item_data['item_enhancement'];
				$item->item_quality_tier = $item_data['item_quality_tier'];
				$item->item_elements = array_map('intval', explode(';', $item_data['item_elements']));
				$item->item_roll_values = explode(';', $item_data['item_roll_values']);
				$item->item_num_rolls = count($item->item_roll_values);
				$item->item_base_stat = $item_data['item_base_stat'];
				$item->item_bonus_stat = $item_data['item_bonus_stat'];
				$item->base_damage_min = $item_data['item_base_dmg_min'];
				$item->base_damage_max = $item_data['item_base_dmg_max'];
				$item->item_num_sockets = $item_data['item_num_sockets'];
				$item->item_inlaid_gem_id = $item_data['item_inlaid_gem_id'];
				$item->item_damage_type = $item_data['item_damage_type'];
				if (strpos($item->item_name, "[Sacred]") !== false) {
					$item->is_sacred = true;
				}
				if ($item->player_id == 0) {
					$item_list[$item_data['item_id']] = null;
				} else {
					$item->update_damage();
					$item_list[$item_data['item_id']] = $item;
				}
			}
			if ($multi_id === null) {
				if (empty($item_list)) {
					return null;
				} else {
					return reset($item_list); 
				}
			} else {
				return $item_list;
			}
		} else {
			return null;
		}
	}
	
	function limit_elements($player_obj, $e_weapon) {
		$elemental_breakdown = [];
		$temp_list = $e_weapon->item_elements;
		if ($player_obj->elemental_capacity == 9) {
			return $temp_list;
		}
		foreach ($temp_list as $x => $is_used) {
			if ($is_used) {
				$temp_total = $player_obj->elemental_mult[$x] * $player_obj->elemental_pen[$x];
				$temp_total *= $player_obj->elemental_curse[$x];
				$elemental_breakdown[] = [$x, $temp_total];
			}
		}
		usort($elemental_breakdown, function ($a, $b) {
			return $b[1] <=> $a[1];
		});
		$damage_limitation = array_column(array_slice($elemental_breakdown, 0, $player_obj->elemental_capacity), 0);
		foreach (array_keys($temp_list) as $i) {
			if (!in_array($i, $damage_limitation)) {
				$temp_list[$i] = 0;
			}
		}
		return $temp_list;
	}
	
	function generate_stars($tier) {
		$star_display = '<div class="star-container">';
		for ($i = 1; $i <= 9; $i++) {
			if ($i <= $tier) {
				$star_display .= '<img src="./gallery/Icons/Stars/Star' . $tier . '.webp" class="icon-small">';
			} else {
				$star_display .= '<img src="./gallery/Icons/Stars/StarBlank.webp" class="icon-small">';
			}
		}
		$star_display .= '</div>';
		return $star_display;
	}
	
	function generate_element_icons($item_elements) {
		global $element_names;
		if (is_string($item_elements)) {
			$elements = explode(';', $item_elements);
		} elseif (is_array($item_elements)) {
			$elements = $item_elements;
		} else {
			return '';
		}
		$element_display = '<div class="element-icons">';
		foreach ($elements as $index => $value) {
			if ($value == 1) {
				$element_name = $element_names[$index];
				$element_display .= '<img src="./gallery/Icons/Elements/' . $element_name . '.webp" class="icon-small" alt="' . $element_name . '">';
			}
		}
		$element_display .= '</div>';
		return $element_display;
	}

	class BasicItem {
		public string $item_id;
		public string $item_name;
		public int $item_tier;
		public string $item_category;
		public string $item_description;
		public string $image_link;
	
		public function __construct(string $item_id) {
			global $itemData, $web_url_base;
			$this->item_id = $item_id;
			$item_info = $itemData[$item_id] ?? [];
			$this->item_name = $item_info['name'] ?? '';
			$this->item_tier = $item_info['tier'] ?? 0;
			$this->item_category = $item_info['category'] ?? '';
			$this->item_description = $item_info['description'] ?? '';
			$this->image_link = $item_info['image_link'] ?? '';
		}
	}

	function get_frame_image($item_id) {
		global $itemData;
		$filename = "";
		$directory = "NonGear_Icon";
		$category = $itemData[$item_id]["category"] ?? "";
		$tier = $itemData[$item_id]["tier"] ?? 0;
		$filename = "Frame_{$item_id}.png";
		if ($category === "Fish") {
			$filename = "Frame_Fish{$tier}.png";
			if ($item_id == "Nadir"){
				$category = "Misc";
				$filename = "Frame_Nadir.png";
			}
		} else if ($category === "Essence") {
			$filename = "Frame_Essence_{$tier}.png";
		} else if ($category === "Gem" || $category === "Jewel") {
			$directory = "Gear_Icon";
			$category = "Gem";
			$filename = "Frame_Gem_{$tier}.png";
		} else if ($category === "Unrefined" or $category === "Void") {
			$directory = "Gear_Icon";
			$parts = explode(" ", $itemData[$item_id]["name"] ?? "");
			$category = trim(end($parts), "()");
			if ($category === "Weapon") {
        		$category = ($tier == 5 ? "Saber" : "Sword");
			}
			$filename = "Frame_{$category}_{$tier}.png";
		}
		$filename = htmlspecialchars("./botimages/{$directory}/{$category}/{$filename}", ENT_QUOTES, 'UTF-8');
		return $filename;
	}
	
	function update_stock($player_id, $item_id, $qty) {
		$query = "INSERT INTO BasicInventory (player_id, item_id, item_qty)
				  VALUES ($player_id, '$item_id', $qty)
				  ON DUPLICATE KEY UPDATE item_qty = item_qty + VALUES(item_qty)";
		run_query($query, false);
	}	

	function update_reward_stock($player_id, $reward_items) {
		if (empty($reward_items)) return;
		$values = array_map(
			fn($id, $qty) => '(' . $player_id . ", '" . $id . "', " . $qty . ')',
			array_keys($reward_items),
			$reward_items
		);
		$value_str = implode(',', $values);
		$query = "INSERT INTO BasicInventory (player_id, item_id, item_qty)
				  VALUES $value_str
				  ON DUPLICATE KEY UPDATE item_qty = item_qty + VALUES(item_qty)";
		run_query($query, false);
	}

	function pay_batch_cost($player_id, $cost_items) {
		if (empty($cost_items)) return;
		$cases = [];
		$ids = [];
		foreach ($cost_items as [$id, $qty]) {
			$cases[] = "WHEN item_id = '" . addslashes($id) . "' THEN GREATEST(0, item_qty - " . intval($qty) . ")";
			$ids[] = "'" . addslashes($id) . "'";
		}
		$query = "UPDATE BasicInventory SET item_qty = CASE " . implode(' ', $cases) . " END
				WHERE player_id = " . intval($player_id) . " AND item_id IN (" . implode(',', $ids) . ")";
		run_query($query, false);
	}

	function calculate_gear_score($player_profile) {
		$score = 0;	
		$equipped_items = [];
		$equipped_gems = [];
		$gem_ids = [];
		// Score Items
		$equipped_items = read_custom_item(null, implode(',', $player_profile->player_equipped));
		if(empty($equipped_items)) { return $score; }
		foreach ($equipped_items as $item) {
			$score += $item->get_gear_score();
			if ($item->item_inlaid_gem_id != 0) {
				$gem_ids[] = $item->item_inlaid_gem_id;
			}
		}
		// Score Gems
		$equipped_gems = read_custom_item(null, implode(',', $gem_ids));
		if(empty($equipped_gems)) { return $score; }
		foreach ($equipped_gems as $gem) {
			$score += $gem->get_gear_score();
		}
		return $score;
	}
	
	function generate_random_tier($min = 1, $max = 8, $luck_bonus = 0) {
		$weights = [];
		for ($tier = $min; $tier <= $max; $tier++) {
			$weights[$tier] = pow(2, $max - $tier);
		}
		$total = array_sum($weights);
		$rand = mt_rand(0, 1000) / 1000 - ($luck_bonus * 0.001);
		$cumulative = 0;
		foreach ($weights as $tier => $weight) {
			$cumulative += $weight / $total;
			if ($rand <= $cumulative) {
				return $tier;
			}
		}
		return $min;
	}

	function check_custom_inventory_capacity($player_id, $item_type) {
		if ($item_type === 'D') {
			$query = "SELECT COUNT(*) as count FROM CustomInventory";
			$query .= " WHERE player_id = " . intval($player_id) . " AND (item_type = 'D1' OR item_type = 'D2' OR item_type = 'D3' OR item_type = 'D4' OR item_type = 'D5')";
		} else {
			$query = "SELECT COUNT(*) as count FROM CustomInventory WHERE player_id = " . intval($player_id) . " AND item_type = '" . $item_type . "'";
		}
		$result = run_query($query);
		return $result[0]['count'] ?? 0;
	}

	function generate_random_item($quantity = 1) {
		global $itemdata_dict;
		$rewards = [];
		$quantity_table = [1, 1, 1, 1, 1, 1, 2, 2, 2, 3];
		$probability_rewards = [
			[10, null, "Lotus"], [1, "DarkStar", null], [1, "LightStar", null], [1, null, "Skull"], [22, null, "Gemstone"],
			[99, null, "Essence"], [200, null, "Trove"], [100, "Catalyst", null], [50, null, "Crystal"],
			[5, null, "Skull3"], [30, null, "Skull2"], [67, null, "Skull1"],
			[200, null, "Token"], [100, null, "Jewel"], [200, null, "Summon"], [50, "Compass", null],
			[1000, "Pearl", null], [2000, "Hammer", null], [250, null, "Gem"], [2600, null, "Ore"],
			[750, null, "Fragment"], [500, "Flame1", null], [500, "Matrix", null], [400, null, "Potion"],
			[864, null, "Fae"]
		];
		$max_reward = 10000;
		for ($i = 0; $i < $quantity; $i++) {
			$current_breakpoint = 0;
			$random_item = rand(1, $max_reward);
			foreach ($probability_rewards as $item) {
				$current_breakpoint += $item[0];
				if ($random_item <= $current_breakpoint) {

					$reward_id = $item[1];
					$prefix = $item[2];

					if ($prefix !== null) {
						$reward_types = [];
						foreach ($itemdata_dict as $key => $_) {
							if ($key !== null && strpos($key, $prefix) !== false) {
								$reward_types[] = $key;
							}
						}
						$reward_id = $reward_types[array_rand($reward_types)];
					}
					break;
				}
			}
			$reward_obj = new BasicItem($reward_id);
			if ($reward_obj->item_tier >= 4) {
				$item_qty = 1;
			} else {
				$item_qty = $quantity_table[array_rand($quantity_table)];
			}
			if (strpos($reward_id, "Fae") !== false) {
				$item_qty = rand(5, 50 * $item_qty);
			} elseif (strpos($reward_id, "Fragment") !== false) {
				$item_qty = 5;
			}
			if (isset($rewards[$reward_id])) { 
				$rewards[$reward_id] += $item_qty;
			} else {
				$rewards[$reward_id] = $item_qty;
			}
		}
		$result = [];
		foreach ($rewards as $id => $qty) {
			$result[] = [$id, $qty];
		}
		return $result;
	}

?>