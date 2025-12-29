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

	function get_tarot_collection_count($player_id) {
		$query = "SELECT COUNT(*) as count FROM TarotInventory WHERE player_id = " . intval($player_id);
		$result = run_query($query);
		return $result ? (int)$result[0]['count'] : 0;
	}
	
	function display_tarot($tarot_card, $menu_type = null) {
		global $card_variant, $path_names, $tarot_rate_map;
		$html = "<div class='item-slot hidden-tag tarot1 active' id='item-Tarot'>";
		if (!$tarot_card) {
			return $html . "</div>";
		}
		if ($menu_type === null) {
			$html .= '<button type="button" class="slot-toggle input-button" onclick="toggleSlotDisplay(\'Tarot\')">Toggle</button>';
		}
		$artist_name = "Nong Dit";
		if ($tarot_card->num_stars !== 0) {
			$artist_name = get_artist_by_numeral($tarot_card->card_numeral);
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
			$tarot_skills = $tarot_card->display_tarot_skills();
			$html .= '<div id="tarot-skills">' . $tarot_skills . '</div>';
			if ($menu_type === 'Synthesize') {			
				$rate = $tarot_rate_map[$tarot_card->num_stars] ?? 0;
				$bind_rate = max(0, 90 - ($tarot_card->card_tier * 5));
				$html .= "<div class='style-line'></div>";
				$html .= "<div class='cost-row'>Duplicate Cards: " . max(0, $tarot_card->card_qty - 1) . "</div>";
				$html .= "<div class='cost-row'>Binding Rate: {$bind_rate}%</div>";
				if ($tarot_card->num_stars >= 9) {
					$html .= "<div class='cost-row'>Synthesis Rate: [MAXED]</div>";
				} else {
					$html .= "<div class='cost-row'>Synthesis Rate: {$rate}%</div>";
				}
				$lotus_item = null;
				$lotus_qty = 1;
				if ($tarot_card->num_stars == 7) {
					$lotus_item = new BasicItem("Lotus9");
				} elseif ($tarot_card->num_stars == 8) {
					$lotus_item = new BasicItem("Lotus11");
					$lotus_qty = $tarot_card->card_tier;
				}
				if ($lotus_item !== null) {
					$stock = checkUserStock($tarot_card->player_id, [$lotus_item->item_id])[$lotus_item->item_id] ?? 0;
					$html .= "<div class='cost-row'><img src='{$lotus_item->image_link}' class='cost-icon'> {$lotus_item->item_name}: {$stock} / {$lotus_qty}</div>";
				}
			}
		}
		$html .= '</div>';
		$html .= "<div id='image-tarot' class='item-slot tarot2'>";
		if ($menu_type === null) {
			$html .= '<button type="button" class="slot-toggle input-button" onclick="toggleSlotDisplay(\'Tarot\')">Toggle</button>';
		}
		$html .= "<div id='image-tarot-bg' style=\"background-image: url('" . $tarot_card->card_image_link . "');\"></div>";
		$html .= "<div class='artist-name highlight-text'>Tarot Artist</div><div class='artist-name'>" . $artist_name . "</div>";
		$html .= '</div>';
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
	function number_to_roman($num) {
		$map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
		$result = '';
		foreach ($map as $roman => $value) {
			while ($num >= $value) {
				$result .= $roman;
				$num -= $value;
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

	function get_tarot($player_id, $numeric_id){
		global $tarot_data;
		$player_profile = get_player_by_id($player_id);
		$roman = $tarot_data[$numeric_id]['Numeral'];
		$equipped_weapon_id = $player_profile->player_equipped[0] ?? 0;
		$equipped_ring_id = $player_profile->player_equipped[4] ?? 0;
		$weapon = $equipped_weapon_id ? read_custom_item($equipped_weapon_id) : null;
		$ring = $equipped_ring_id ? read_custom_item($equipped_ring_id) : null;
		$resonance = check_resonance($weapon, $ring);
		$query = "SELECT * FROM TarotInventory WHERE player_id = $player_id AND card_numeral = '$roman'";
		$result = run_query($query);
		if (!$result || count($result) === 0) {
			$card_qty = 0;
			$num_stars = 0;
			$enhancement = 0;
		} else {
			$card_qty = $result[0]['card_qty'];
			$num_stars = $result[0]['num_stars'];
			$enhancement = $result[0]['card_enhancement'];
		}
		return new TarotItem($player_id, $resonance, $roman, $card_qty, $num_stars, $enhancement);
	}

	function getDeityByTier($tier) {
		global $tarot_data;
		$pool_keys = [];
		foreach ($tarot_data as $k => $data) {
			if (intval($data['tier']) <= $tier) { $pool_keys[] = $k; }
		}
		return $tarot_data[$pool_keys[array_rand($pool_keys)]]['Numeral'];
	}

	function getTarotByNumeral($num) {
		global $tarot_data;
		foreach ($tarot_data as $entry) {
			if ($entry['Numeral'] == $num) {
				return $entry;
			}
		}
		return null;
	}


?>
	