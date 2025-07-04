<?php
	class ItemRoll {
		public $roll_id;
		public $roll_tier;
		public $roll_icon;
		public $roll_category;
		public $roll_code;
		public $roll_value;
		public $roll_msg;
		public $roll_name;

		public function __construct($roll_id) {
			global $unique_skill_rolls, $unique_rolls, $class_names;
			// Initialize values
			$this->roll_id = $roll_id;
			$roll_details = explode("-", $roll_id);
			$this->roll_tier = intval($roll_details[0]);
			$this->roll_icon = $this->get_roll_icon($this->roll_tier);
			$this->roll_category = $roll_details[1];
			$this->roll_code = "{$roll_details[1]}-{$roll_details[2]}";
			$this->roll_value = 0;
			$this->roll_msg = "";
			// Adjust specific values
			$roll_adjust = 0.01 * $this->roll_tier;
			// Handle non-unique rolls
			if ($this->roll_category !== "unique") {
				global $item_roll_master_dict;
				$category_dict = $item_roll_master_dict[$this->roll_category];
				$current = $category_dict[0];
				$current_roll = $current[$this->roll_code];
				$this->roll_value = $current_roll[1] * $roll_adjust;
				$roll_percentage = $this->roll_value * 100;
				$this->roll_msg = "{$current_roll[0]} " . (floor($roll_percentage) == $roll_percentage ? intval($roll_percentage) : number_format($roll_percentage, 1)) . "%";
				$this->roll_name = $current_roll[0];
				return;
			}
			// Handle unique rolls
			$this->roll_code .= "-{$roll_details[3]}";
			$current = in_array($roll_details[3], $class_names) ? $unique_skill_rolls : $unique_rolls[$roll_details[3]][0];
			$current_roll = $current[$this->roll_code];
			$this->roll_value = $current_roll[1] * $roll_adjust;
			$temp_msg = "{$current_roll[0]}";
			$this->roll_msg = "{$temp_msg} " . intval(round($this->roll_value * 100)) . "%";
			if (strpos($temp_msg, "X") !== false) {
				$this->roll_msg = str_replace("X", strval(intval(round($this->roll_value * 100))), $temp_msg);
			}
			$this->roll_name = $current_roll[0];
		}
		public function get_roll_icon($roll_tier) {
			global $augment_icons;
			return $augment_icons[$roll_tier - 1];
		}
	}

	function add_roll($selected_item, $num_rolls) {
		global $item_roll_master_dict, $roll_structure_dict;
		for ($i = 0; $i < $num_rolls; $i++) {
			$exclusions_list = [];
			$exclusions_weighting = [];
			$new_roll_type = $roll_structure_dict[$selected_item->item_type][$selected_item->item_num_rolls];
			if ($new_roll_type === "unique") {
				list($roll_list, $total_weighting) = handle_unique($selected_item);
			} else {
				$roll_list = $item_roll_master_dict[$new_roll_type][0];
				$total_weighting = $item_roll_master_dict[$new_roll_type][1];
			}
			foreach ($selected_item->item_roll_values as $roll_id) {
				$current_roll = new ItemRoll($roll_id);
				if ($current_roll->roll_category === $new_roll_type) {
					$exclusions_list[] = $current_roll->roll_code;
					$exclusions_weighting[] = $roll_list[$current_roll->roll_code][2];
				}
			}
			$available_rolls = array_diff(array_keys($roll_list), $exclusions_list);
			$selected_roll_code = select_roll($total_weighting, $exclusions_weighting, $available_rolls, $roll_list);
			$roll_tier = (strpos($selected_item->item_type, "D") !== false) ? $selected_item->item_tier : 1;
			$new_roll_id = "$roll_tier-$selected_roll_code";
			$selected_item->item_roll_values[] = $new_roll_id;
			$selected_item->item_num_rolls += 1;
		}
	}

	function handle_unique($selected_item, $specific_class = null) {
		global $unique_rolls, $class_names;
		// Determine the unique rolls to use.
		$shared_roll_dict = ["W" => "w", "A" => "a", "V" => "a", "Y" => "y", "G" => "y", "C" => "y"];
		$specific_type = $shared_roll_dict[$selected_item->item_type];
		$selected_unique_type = ["s", $specific_type];
		// Combine the data.
		$combined_dict = array_merge($unique_rolls[$selected_unique_type[0]][0], $unique_rolls[$selected_unique_type[1]][0]);
		$combined_weighting = $unique_rolls[$selected_unique_type[0]][1] + $unique_rolls[$selected_unique_type[1]][1];
		if ($specific_type == "y") {
			$temp_list = $unique_rolls["SKILL"][0];
			if ($specific_class !== null) {
				$temp_list = array_filter($temp_list, function($key) use ($specific_class) {
					return substr($key, -strlen($specific_class)) === $specific_class;
				}, ARRAY_FILTER_USE_KEY);
			}
			$combined_dict = array_merge($combined_dict, $temp_list);
			$combined_weighting += array_sum(array_column($temp_list, 2));
		}
		return [$combined_dict, $combined_weighting];
	}

	function select_roll($total_weighting, $exclusions_weighting, $available_rolls, $roll_list) {
		$adjusted_weighting = $total_weighting - array_sum($exclusions_weighting) - 1;
		$random_value = rand(0, $adjusted_weighting);
		$cumulative_weight = 0;
		foreach ($available_rolls as $roll_code) {
			$cumulative_weight += $roll_list[$roll_code][2];
			if ($random_value < $cumulative_weight) {
				return $roll_code;
			}
		}
		return "ERROR";
	}
	
	function assign_gem_values($player_obj, $e_gem) {
		global $gem_point_dict;
		$player_obj->player_damage_min += $e_gem->item_damage_min;
		$player_obj->player_damage_max += $e_gem->item_damage_max;
		$points_value = intval($gem_point_dict[$e_gem->item_tier]);
		$player_obj->gear_points[intval($e_gem->item_bonus_stat)] += $points_value;
		assign_roll_values($player_obj, $e_gem);
	}
	
	function assign_roll_values($player_obj, $equipped_item) {
		if ($equipped_item->item_type == "R") {
			$equipped_item->assign_ring_values($player_obj);
			return;
		}
		foreach ($equipped_item->item_roll_values as $roll_id) {
			$current_roll = new ItemRoll($roll_id);
			// Locate the roll information
			if ($current_roll->roll_category == "unique") {
				list($roll_list, $_) = handle_unique($equipped_item);
				$roll_data = $roll_list[$current_roll->roll_code][3];
			} else {
				global $item_roll_master_dict;
				$roll_data = $item_roll_master_dict[$current_roll->roll_category][0][$current_roll->roll_code][3];
			}
			// Check for exception rolls that require special handling
			if (!handle_roll_exceptions($player_obj, $current_roll, $roll_data)) {
				foreach ($roll_data as $attribute_info) {
					list($attribute_name, $attribute_position) = $attribute_info;
					if ($attribute_position == -1) {
						$player_obj->$attribute_name += $current_roll->roll_value;
					} else {
						$target_list = &$player_obj->$attribute_name;
						$target_list[$attribute_position] += $current_roll->roll_value;
					}
				}
			}
		}
	}
	
	function handle_roll_exceptions($player_obj, $selected_roll, $selected_data) {
		if (substr($selected_roll->roll_id, -strlen($player_obj->player_class)) === $player_obj->player_class) {
			return true;
		}
		if (strpos($selected_data[0][0], "elemental_conversion") !== false) {
			list(, $attribute_position) = $selected_data[0];
			$player_obj->apply_elemental_conversion($attribute_position, $selected_roll->roll_value, $selected_roll->roll_value);
			return true;
		}
		return false;
	}
	
	function assign_item_element_stats($player_obj, $equipped_item) {
		$associated_stats = [
			"A" => [&$player_obj->elemental_res, 0.1],
			"V" => [&$player_obj->elemental_res, 0.1],
			"Y" => [&$player_obj->elemental_damage, 0.25],
			"G" => [&$player_obj->elemental_pen, 0.15],
			"C" => [&$player_obj->elemental_curse, 0.1]
		];
		foreach ($equipped_item->item_elements as $idz => $z) {
			if ($z == 1 && isset($associated_stats[$equipped_item->item_type])) {
				$associated_stats[$equipped_item->item_type][0][$idz] += $associated_stats[$equipped_item->item_type][1];
			}
		}
	}

	function reroll_roll(&$item, $method_type, $player_class = null) {
		global $class_names;
        global $roll_structure_dict, $item_roll_master_dict;
        if ($method_type === "all") {
            $tier_list = array_map(fn($roll) => (new ItemRoll($roll))->roll_tier, $item->item_roll_values);
            $item->item_num_rolls = 0;
            $item->item_roll_values = [];
            add_roll($item, 6);
            foreach ($item->item_roll_values as $index => $roll) {
                $tier = $tier_list[$index] ?? 1;
                $item->item_roll_values[$index] = "$tier-" . explode("-", $roll, 2)[1];
            }
            return;
        }
        $method = ($method_type === "any") ? $roll_structure_dict[$item->item_type][array_rand($roll_structure_dict[$item->item_type])] : $method_type;
        if ($method_type === "Salvation") { $method = "unique"; }
        $eligible_rolls = [];
        $roll_index = null;
        foreach ($item->item_roll_values as $index => $roll_id) {
            $current_roll = new ItemRoll($roll_id);
            if ($current_roll->roll_category === $method) {
                $eligible_rolls[$index] = $current_roll;
            }
        }
        if (empty($eligible_rolls)) return;
        $selected_index = array_rand($eligible_rolls);
        $selected_roll = $eligible_rolls[$selected_index];
        $roll_list = ($method === "unique") ? handle_unique($item, $player_class)[0] : $item_roll_master_dict[$method][0];
		if ($method_type === "Salvation") {
			$roll_list = array_filter($roll_list, function($k) use ($class_names) {
				foreach ($class_names as $class) {
					if (str_ends_with($k, '-' . $class)) return true;
				}
				return false;
			}, ARRAY_FILTER_USE_KEY);
		}
        $exclusions_list = [];
        $exclusions_weighting = [];
        foreach ($eligible_rolls as $roll) {
			if (isset($roll_list[$roll->roll_code])) {
				$exclusions_list[] = $roll->roll_code;
				$exclusions_weighting[] = $roll_list[$roll->roll_code][2];
			}
		}
        $available_rolls = array_diff(array_keys($roll_list), $exclusions_list);
        $selected_roll_code = select_roll(array_sum(array_column($roll_list, 2)), $exclusions_weighting, $available_rolls, $roll_list);
        $tier = $selected_roll->roll_tier;
        $item->item_roll_values[$selected_index] = "$tier-$selected_roll_code";
    }

?>