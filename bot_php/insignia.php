<?php
	class Insignia {
		public $player_id, $insignia_code, $mutation_tier, $element_list;
		public $num_elements, $insignia_stars, $insignia_name, $insignia_link;
		public $hp_bonus, $luck_bonus, $final_damage, $attack_speed, $insignia_damage, $pen;

		public function __construct($player_obj) {
			global $insignia_hp_list, $insignia_multipliers, $insignia_damage;
			global $insignia_name_list, $insignia_description_list;
			$this->player_id = $player_obj->player_id;
			$this->insignia_code = $player_obj->player_insignia;
			$temp_code = explode(";", $this->insignia_code);
			$temp_elements = array_slice($temp_code, 0, 9);
			$this->mutation_tier = (int)end($temp_code);
			$mutation_adjust = max(1, $this->mutation_tier);
			$this->element_list = array_map('intval', $temp_elements);
			$this->num_elements = array_sum($this->element_list);
			$this->insignia_stars = $this->get_insignia_stars($player_obj->player_echelon, $this->mutation_tier);
			$this->hp_bonus = $insignia_hp_list[$this->insignia_stars];
			$this->luck_bonus = $this->insignia_stars;
			$this->final_damage = $player_obj->player_level * $mutation_adjust;
			$this->attack_speed = $this->insignia_stars * 5;
			$this->insignia_damage = $insignia_damage[$this->insignia_stars];
			$this->pen = $insignia_multipliers[$this->num_elements][0];
			$this->pen += $insignia_multipliers[$this->num_elements][1] * $this->insignia_stars * $mutation_adjust;
			$this->insignia_name = "{$insignia_name_list[$this->num_elements]} Insignia";
			$selected = [];
			foreach ($this->element_list as $idx => $val) {
				if ($val === 1) {
					$selected[] = $idx;
				}
			}
			sort($selected);
			$digits = (count($selected) === 9) ? "012345678" : implode("", $selected);
			$this->insignia_link = "https://PandoraPortal.ca/botimages/Gear_Icon/Insignia/Frame_Insignia/Frame_Insignia{$digits}_{$this->insignia_stars}.png";
		}
		
		private function get_insignia_stars($echelon, $mutation_tier) {
			return 1 + ($echelon >= 5 ? 1 : 0) + ($echelon >= 7 ? 1 : 0) + ($echelon >= 8 ? 1 : 0) + ($echelon >= 9 ? 1 : 0) + $mutation_tier;
		}
	}
	
	function display_insignia($player_profile) {
		global $element_names, $insignia_prefix;
		$output = "<div class='item-slot' id='item-Insignia'>";	
		if (empty($player_profile->player_insignia)) {
			return $output . "Empty Slot: Insignia</div>";
		}
		$insignia = new Insignia($player_profile);	
		$output .= "<img class='item-thumbnail' src='" . $insignia->insignia_link . "'>";
		$output .= "<h1 class='item-name highlight-text'>{$insignia->insignia_name}</h1>";
		$output .= "<div class='style-line'></div>";
		$output .= generate_stars($insignia->insignia_stars);
		$output .= generate_element_icons($insignia->element_list);		
		$output .= "<div class='style-line'></div>";
		$output .= "<div class='badge-container'>";
		$output .= "<div class='item-id-badge'>[{$insignia_prefix[$insignia->insignia_stars]}]</div>";
		$output .= "<div class='item-tier-badge'>Tier: {$insignia->insignia_stars}</div>";
		$output .= "</div>";
		$formatted_hp = number_format($insignia->hp_bonus);
		$formatted_fd = number_format($insignia->final_damage);
		$formatted_as = number_format($insignia->attack_speed);
		$formatted_damage = number_format($insignia->insignia_damage);
		$output .= "<div class='style-line'></div>";
		$output .= "<div class='stat-message'>Base: {$formatted_damage} - {$formatted_damage}</div>";
		$output .= "<div class='stat-message'>- - -</div>";
		$output .= "<div class='style-line'></div>";
		$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>Luck Bonus: +{$insignia->luck_bonus}</div>";
		$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>HP Bonus: +{$formatted_hp}</div>";
		$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>Final Damage: +{$formatted_fd}%</div>";
		$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>Attack Speed: +{$formatted_as}%</div>";
		if ($insignia->num_elements == 9) {
			$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>Omni Penetration: +{$insignia->pen}%</div>";
		} else if ($insignia->num_elements > 3) {
			$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>Element Penetration: +{$insignia->pen}%</div>";
		}else {
			foreach ($insignia->element_list as $index => $value) {
				if ($value == 1) {
					$element_name = $element_names[$index];
					$output .= "<div class='skill-slot tier-" . $insignia->insignia_stars . "'>{$element_name} Penetration: +{$insignia->pen}%</div>";
				}
			}
		}
		$output .= "</div>";
		$output .= "<div class='item-slot-void' id='void-insignia'></div>";
		return $output;
	}
	
	function assign_insignia_values($player_obj) {
		if (empty($player_obj->player_insignia)) {
			return;
		}
		$insignia_obj = new Insignia($player_obj);
		// Apply bonus stats
		$player_obj->player_damage_min += $insignia_obj->insignia_damage;
		$player_obj->player_damage_max += $insignia_obj->insignia_damage;
		$player_obj->final_damage += $insignia_obj->final_damage * 0.01;
		$player_obj->attack_speed += $insignia_obj->attack_speed * 0.01;
		$player_obj->luck_bonus += $insignia_obj->luck_bonus;
		$player_obj->hp_bonus += $insignia_obj->hp_bonus;
		// Apply Elemental Penetration
		$adjusted_penetration = $insignia_obj->pen * 0.01;
		if ($insignia_obj->num_elements != 9) {
			foreach ($insignia_obj->element_list as $y => $has_element) {
				if ($has_element) {
					$player_obj->elemental_pen[$y] += $adjusted_penetration;
				}
			}
		} else {
			$player_obj->all_elemental_pen += $adjusted_penetration;
		}
	}
?>