<?php	
	class TarotItem {
		public $player_id, $card_numeral, $tarot_key, $card_qty, $num_stars, $card_enhancement, $resonance;
		public $card_name, $card_tier;
		public $card_damage, $card_hp, $card_fd, $card_type, $card_path, $path_points, $card_image_link, $essence_link;

		public function __construct($player_id, $resonance, $card_numeral, $card_qty, $num_stars, $card_enhancement) {
			global $tarot_data, $tarot_hp, $tarot_fd, $tarot_damage, $tarot_point_values;
			$this->player_id = $player_id;
			$this->card_numeral = $card_numeral;
			$this->tarot_key = roman_to_number($card_numeral);
			$this->resonance = ($resonance == "all" || $resonance == $card_numeral) ? 2 : 1;
			$this->card_name = $tarot_data[$this->tarot_key]['Name'];
			$this->card_tier = $tarot_data[$this->tarot_key]['tier'];
			$this->card_type = $tarot_data[$this->tarot_key]["type"];
			$this->card_path = $tarot_data[$this->tarot_key]["path"];
			$this->card_qty = $card_qty;
			$this->num_stars = $num_stars;
			$this->card_enhancement = $card_enhancement;
			$this->card_damage = $tarot_damage[$num_stars];
			$this->card_hp = $tarot_hp[$num_stars];
			$this->card_fd = $tarot_fd[$num_stars];
			$this->path_points = $tarot_point_values[$num_stars];
			if ($card_qty == 0) {
				$this->card_image_link = "./gallery/Tarot/Paragon/Cardback.webp";
			} else {
				$this->card_image_link = "./botimages/tarot/" . $card_numeral . "/" . $card_numeral . "_" . $num_stars. ".png";
			}
			$this->essence_link = "./botimages/NonGear_Icon/Essence/Frame_Essence_" . $this->num_stars . ".png";
		}
		
		public function assign_tarot_values($player_obj) {
			global $tarot_data;
			// Apply Path bonuses
			 if ($this->card_path != "All") {
				$player_obj->gear_points[$this->card_path] += $this->path_points * $this->resonance;
			} else {
				foreach ($player_obj->gear_points as &$path) {
					$path += $this->path_points * $this->resonance;
				}
			}
			// Apply modifier bonuses
			$player_obj->player_damage_min += $this->card_damage * $this->resonance;
			$player_obj->player_damage_max += $this->card_damage * $this->resonance;
			$player_obj->hp_bonus += $this->card_hp * $this->resonance;
			$player_obj->final_damage += $this->card_fd * 0.01 * $this->resonance;
			$card_data = $tarot_data[$this->tarot_key];
			foreach ($card_data['skills'] as $skill) {
				$attribute_value = $skill['value'];
				$attribute_name = $skill['attribute'];
				$attribute_index = $skill['index'];
				if (strpos($attribute_name, 'appli') === false) {
					$attribute_value *= 0.01;
				}
				$attribute_value *= $this->num_stars * $this->resonance;
				if ($attribute_index === null) {
					$player_obj->$attribute_name += $attribute_value;
				} else {
					$player_obj->{$attribute_name}[$attribute_index] += $attribute_value;
				}
			}
		}

		public function display_tarot_skills() {
			global $tarot_data;
			if (!isset($tarot_data[$this->tarot_key])) {
				return "No tarot skills found.";
			}
			$hp_bonus = number_format($this->card_hp * $this->resonance);
			$final_damage = number_format($this->card_fd * $this->resonance);
			$skill_display = "<div class=\"skill-slot tier-{$this->num_stars}\">HP Bonus: +{$hp_bonus}</div>";
			$skill_display .= "<div class=\"skill-slot tier-{$this->num_stars}\">Final Damage: +{$final_damage}%</div>";
			$skills = $tarot_data[$this->tarot_key]['skills'];
			foreach ($skills as $skill) {
				$skill_value = $skill['value'] * $this->num_stars * $this->resonance;
				if (strpos($skill['bonus'], 'X') !== false) {
					$formatted_skill = str_replace('X', $skill_value, $skill['bonus']);
				} else {
					$formatted_skill = $skill['bonus'];
					if (strpos($skill['bonus'], 'Application') !== false) {
						$formatted_skill .= " +{$skill_value}";
					} else {
						$formatted_skill .= " {$skill_value}%";
					}
				}
				$skill_display .= "<div class=\"skill-slot tier-{$this->num_stars}\">{$formatted_skill}</div>";
			}
			return $skill_display;
		}
	}
	
	function get_tarot_by_id($player_profile, $resonance) {
		$player_id = $player_profile->player_id;
		$card_numeral = $player_profile->equipped_tarot;
		$query_tarot = "SELECT * FROM TarotInventory WHERE player_id = " . intval($player_id) . " AND card_numeral = '" . addslashes($card_numeral) . "'";
		$result_tarot = run_query($query_tarot);
		if ($result_tarot) {
			$row = $result_tarot[0];
			return new TarotItem($player_id, $resonance, $card_numeral, $row['card_qty'], $row['num_stars'], $row['card_enhancement']);
		} else {
			return null;
		}
	}
	
	function display_tarot($tarot_card) {
		global $card_variant, $path_names;
		$html = "<div class='item-slot' id='item-Tarot'>";
		$html = "<div class='item-slot' id='item-Tarot'>";
		if (!$tarot_card) {
			return $html . "</div>";
			return $html . "</div>";
		}
		$html .= "<img class='item-thumbnail' src='" . $tarot_card->essence_link . "'>";
		$html .= "<div class='style-line'></div>";
		$html .= generate_stars($tarot_card->num_stars);
		$html .= '<h1 class="item-name highlight-text">' . $tarot_card->card_numeral . ' - ' . $tarot_card->card_name . '</h1>';
		$html .= "<div class='style-line'></div>";
		$html .= "<div class='badge-container'>";
		$html .= '<div class="item-id-badge">' . '[' . $card_variant[$tarot_card->num_stars] . ']' . '</div>';
		$html .= $tarot_card->resonance == 1 ? "<div class='inactive-badge'>Dormant</div>" : "<div class='active-badge'>Resonating</div>";
		$html .= '<div class="item-tier-badge">Tier: ' . $tarot_card->num_stars . '</div>';
		$html .= "</div>";
		$base_damage_min = number_format($tarot_card->card_damage * $tarot_card->resonance);
		$base_damage_max = number_format($tarot_card->card_damage * $tarot_card->resonance);
		$html .= "<div class='style-line'></div>";
		$html .= "<div class='stat-message'>Base: {$base_damage_min} - {$base_damage_max}</div>";
		$html .= "<div class='stat-message'>Path of " . $path_names[$tarot_card->card_path] . " +" . $tarot_card->path_points . "</div>";
		$html .= "<div class='style-line'></div>";
		$html .= "<div class='stat-message'>Base: {$base_damage_min} - {$base_damage_max}</div>";
		$html .= "<div class='stat-message'>Path of " . $path_names[$tarot_card->card_path] . " +" . $tarot_card->path_points . "</div>";
		$html .= "<div class='style-line'></div>";
		$tarot_skills = $tarot_card->display_tarot_skills();
		$html .= '<div id="tarot-skills">' . $tarot_skills . '</div>';
		$html .= '</div>';
		$html .= "<div id='image-tarot' class='item-slot'>";
		$html .= "<div id='image-tarot-bg' style=\"background-image: url('" . $tarot_card->card_image_link . "');\"></div>";
		$artist_name = get_artist_by_numeral($tarot_card->card_numeral);
		$html .= "<div class='artist-name highlight-text'>Tarot Artist</div><div class='artist-name'>" . $artist_name . "</div>";
		$html .= "</div>";
		$html .= '</div>';
		$html .= "<div id='image-tarot' class='item-slot'>";
		$html .= "<div id='image-tarot-bg' style=\"background-image: url('" . $tarot_card->card_image_link . "');\"></div>";
		$artist_name = get_artist_by_numeral($tarot_card->card_numeral);
		$html .= "<div class='artist-name highlight-text'>Tarot Artist</div><div class='artist-name'>" . $artist_name . "</div>";
		$html .= "</div>";
		return $html;
	}

	function get_artist_by_numeral($numeral) {
		global $artist_numerals;
		foreach ($artist_numerals as $artist => $numerals) {
			if (in_array($numeral, $numerals)) {
				return $artist;
			}
		}
		return "No Artist";
	}

	function roman_to_number($roman) {
		$map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
		$result = 0;
		foreach ($map as $roman_char => $value) {
			while (strpos($roman, $roman_char) === 0) {
				$result += $value;
				$roman = substr($roman, strlen($roman_char));
			}
		}
		return $result;
	}
	
	function check_resonance($weapon, $ring) {
		if ($weapon && $weapon->item_base_type == "Pandora's Universe Hammer"){
			return "all";
		}
		if (!$ring) {
			return null;
		}
		if ($ring->item_tier <= 4) {
			return null;
		}
		$index = ($ring->item_base_type != "Crown of Skulls") ? $ring->item_roll_values[0] : $ring->item_roll_values[2];
		return $index;
	}

?>
	