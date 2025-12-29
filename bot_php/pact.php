<?php
	class Pact {
		public $player_id, $pact_tier, $pact_variant, $pact_link;
		public $demon_name, $pact_stars, $bonus1, $bonus2, $penalty1, $penalty2;
		
		public function __construct($player_obj) {
			global $pact_data;
			$this->player_id = $player_obj->player_id;
			$pact_code = explode(';', $player_obj->player_pact);
			$this->pact_tier = (int)$pact_code[0];
			$this->pact_variant = $pact_code[1];
			$base_url = './botimages/Gear_Icon/Pact/Frame_Pact_';
			$this->pact_link = $base_url . $this->pact_tier . '_' . $this->pact_variant . '.png';
			$this->demon_name = $pact_data['demon_variants'][$this->pact_tier];
			$this->pact_stars = generate_stars($this->pact_tier);
			$this->bonus1 = $pact_data['pact_variants'][$this->pact_variant][0][0];
			$this->bonus2 = $pact_data['pact_variants'][$this->pact_variant][0][1];
			$this->penalty1 = $pact_data['pact_variants'][$this->pact_variant][1][0];
			$this->penalty2 = $pact_data['pact_variants'][$this->pact_variant][1][1];
		}
	}

	function display_pact($player_profile) {
		$pact_output = "<div class='item-slot' id='item-Pact'>";
		if (empty($player_profile->player_pact)) {
			return $pact_output . "Empty Slot: Pact</div>";
		}
		global $skill_data;
		$pact = new Pact($player_profile);
		$pact_output .= "<img class='item-thumbnail' src='" . $pact->pact_link . "'/>";
		$pact_output .= "<h1 class='item-name highlight-text'>" . $pact->demon_name . " Pact</h1>";
		$pact_output .= "<div class='style-line'></div>";
		$pact_output .= "<div>" . $pact->pact_stars . "</div>";
		$pact_output .= '<div class="element-icons"></div>';
		$pact_output .= "<div class='style-line'></div>";
		$pact_output .= "<div class='badge-container'>";
		$pact_output .= "<div class='item-id-badge'>[" . $pact->pact_variant . "]</div>";
		$pact_output .= "<div class='item-tier-badge'>Tier: " . $pact->pact_tier . "</div>";
		$pact_output .= "</div>";
		$pact_output .= "<div class='style-line'></div>";
		$pact_output .= "<div class='stat-message'>- - -</div><div class='stat-message'>- - -</div>";
		$pact_output .= "<div class='style-line'></div>";
		$pact_output .= "<div class='bonus skill-slot tier-" . $pact->pact_tier . "'>Double: " . $pact->bonus1 . "</div>";
		$pact_output .= "<div class='bonus skill-slot tier-" . $pact->pact_tier . "'>Double: " . $pact->bonus2 . "</div>";
		$pact_output .= "<div class='penalty skill-slot tier-" . $pact->pact_tier . "'>Half: " . $pact->penalty1 . "</div>";
		$pact_output .= "<div class='penalty skill-slot tier-" . $pact->pact_tier . "'>Half: " . $pact->penalty2 . "</div>";
		$skills =  ["damage-13", "unique-4-s"];
		foreach ($skills as $skill) {
			$skill_name = $skill_data[$skill][0];
			$skill_value = $pact->pact_tier * $skill_data[$skill][1];
			$pact_output .= "<div class='skill-slot tier-" . $pact->pact_tier . "'>" . "$skill_name +$skill_value%" . "</div>";
		}
		$pact_output .= "</div>";
		$pact_output .= "<div class='item-slot-void' id='void-pact'></div>";
		return $pact_output;
	}
	
	function assign_pact_values($player_obj) {
		if (empty($player_obj->player_pact)) {
			return;
		}
		$pact_object = new Pact($player_obj);
		$player_obj->all_elemental_mult += $pact_object->pact_tier * 0.15;
		$player_obj->class_multiplier += $pact_object->pact_tier * 0.03;
		switch ($pact_object->pact_variant) {
			case "Wrath":
				$player_obj->attack_speed *= 2;
				$player_obj->elemental_capacity *= 2;
				$player_obj->luck_bonus = ($player_obj->luck_bonus != 0) ? round($player_obj->luck_bonus / 2) : 0;
				$player_obj->mitigation_bonus = ($player_obj->mitigation_bonus != 0) ? round($player_obj->mitigation_bonus / 2) : 0;
				break;
			case "Sloth":
				$player_obj->player_mHP *= 2;
				$player_obj->recovery *= 2;
				$player_obj->attack_speed = ($player_obj->attack_speed != 0) ? round($player_obj->attack_speed / 2) : 0;
				break;
			case "Greed":
				foreach (["Omega", "Hyperbleed", "Fractal", "Temporal", "Bloom"] as $key) {
					$player_obj->trigger_rate[$key] *= 2;
				}
				$player_obj->charge_generation = round($player_obj->charge_generation / 2);
				break;
			case "Gluttony":
				$player_obj->charge_generation *= 2;
				$player_obj->elemental_capacity = round($player_obj->elemental_capacity / 2);
				break;
			case "Envy":
				$player_obj->final_damage *= 2;
				$player_obj->luck_bonus *= 2;
				$player_obj->player_mHP = round($player_obj->player_mHP / 2);
				$player_obj->recovery = ($player_obj->recovery != 0) ? round($player_obj->recovery / 2) : 0;
				break;
			case "Pride":
				$player_obj->singularity_mult *= 2;
				$player_obj->banes[5] *= 2;
				$player_obj->dodge = ($player_obj->dodge != 0) ? round($player_obj->dodge / 2) : 0;
				$player_obj->block = ($player_obj->block != 0) ? round($player_obj->block / 2) : 0;
				break;
			case "Lust":
				$player_obj->bleed_mult *= 2;
				$player_obj->class_multiplier *= 2;
				$player_obj->singularity_mult = round($player_obj->singularity_mult / 2);
				$player_obj->mana_mult = round($player_obj->mana_mult / 2);
				break;
			default:
				break;
		}
	}

?>