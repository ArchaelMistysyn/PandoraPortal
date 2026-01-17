<?php
	$web_url_base = 'https://www.PandoraPortal.ca/';
	$itemData = json_decode(file_get_contents('./bot_php/itemData.json'), true);

	$slot_types = [
		'W' => 'Weapon', 
		'A' => 'Armor', 
		'V' => 'Greaves', 
		'Y' => 'Amulet', 
		'R' => 'Ring', 
		'G' => 'Wings', 
		'C' => 'Crest', 
		'Pact' => 'Pact', 
		'Insignia' => 'Insignia', 
		'Tarot' => 'Tarot'
	];
	$item_loc_dict = ['W' => 0, 'A' => 1, 'V' => 2, 'Y' => 3, 'R' => 4, 'G' => 5, 'C' => 6];

	$element_dict = [
		'Fire' => [0], 
		'Water' => [1], 
		'Lightning' => [2], 
		'Earth' => [3], 
		'Wind' => [4],
		'Ice' => [5], 
		'Shadow' => [6], 
		'Light' => [7], 
		'Celestial' => [8],
		'Storms' => [1, 2], 
		'Frostfire' => [0, 5], 
		'Eclipse' => [7, 6], 
		'Horizon' => [3, 4], 
		'Stars' => [8],
		'Solar' => [0, 7, 4], 
		'Lunar' => [1, 5, 6], 
		'Terrestria' => [2, 3, 8], 
		'Chaos' => [0, 2, 3, 6], 
		'Holy' => [1, 4, 5, 7],
		'Confluence' => [0, 1, 2, 3, 4, 5, 6, 7, 8]
	];

	$augment_icons = [];
	for ($i = 1; $i <= 9; $i++) {
		$augment_icons[] = $web_url_base . "gallery/Icons/Pearls/Pearl{$i}.webp";
	}

	// Weapon Type Dictionary
	$weapon_type_dict = [
		"Knight" => [["Sword"], [], ["Saber", "Scythe"]],
		"Ranger" => [[], ["Bow"], ["Blaster"]],
		"Assassin" => [["Dagger"], [], ["Mirrorblades", "Claws"]],
		"Mage" => [["Rod"], [], ["Codex", "Caduceus Rod"]],
		"Weaver" => [[], ["Threads"], []],
		"Rider" => [["Hatchling", "Mare"], [], ["Dragon", "Pegasus"]],
		"Summoner" => [["Serpent"], [], ["Basilisk", "Cerberus"]]
	];

	// Sovereign Item List
	$sovereign_item_list = [
		"Crown of Skulls", "Twin Rings of Divergent Stars", "Hadal's Raindrop", "Heavenly Calamity",
		"Stygian Calamity", "Pandora's Universe Hammer", "Solar Flare Blaster", "Ruler's Crest",
		"Bathyal, Enigmatic Chasm Bauble", "Fallen Lotus of Nephilim", "Chromatic Tears"
	];

	// Tier Keywords
	$tier_keywords = [
		1 => "Lesser",
		2 => "Greater",
		3 => "Superior",
		4 => "Exalted",
		5 => "Sovereign",
		6 => "Mythical",
		7 => "Divine",
		8 => "Transcendent",
		9 => "MAX"
	];

	// Enhancement Maximum
	$max_enhancement = [10, 20, 30, 40, 50, 100, 150, 200, 200];

	// Quality Damage Map
	$quality_damage_map = [
		4 => [1 => "Prelude", 2 => "Fabled", 3 => "Heroic", 4 => "Mythic", 5 => "Legendary"],
		5 => [1 => "Prelude", 2 => "Abject", 3 => "Hollow", 4 => "Abyssal", 5 => "Emptiness"],
		6 => [1 => "Prelude", 2 => "Opalescent", 3 => "Prismatic", 4 => "Resplendent", 5 => "Iridescent"],
		7 => [1 => "Prelude", 2 => "Tainted", 3 => "Cursed", 4 => "Corrupt", 5 => "Fallen"],
		8 => [1 => "Prelude", 2 => "Majestic", 3 => "Sanctified", 4 => "Radiant", 5 => "Transcendent"],
		9 => [1 => "MAX", 2 => "MAX", 3 => "MAX", 4 => "MAX", 5 => "MAX"]
	];
	
	// Gear Data lists
	$tier_keywords = array(
		0 => "Error",
		1 => "Lesser",
		2 => "Greater",
		3 => "Superior",
		4 => "Crystal",
		5 => "Void",
		6 => "Destiny",
		7 => "Stygian",
		8 => "Divine",
		9 => "Sacred"
	);

	$summon_tier_keywords = array(
		0 => "Error",
		1 => "Illusion",
		2 => "Spirit",
		3 => "Phantasmal",
		4 => "Crystalline",
		5 => "Phantasmal",
		6 => "Fantastical",
		7 => "Abyssal",
		8 => "Divine",
		9 => "Sacred"
	);

	$gem_tier_keywords = array(
		0 => "Error",
		1 => "Emerald",
		2 => "Sapphire",
		3 => "Amethyst",
		4 => "Diamond",
		5 => "Emptiness",
		6 => "Destiny",
		7 => "Abyss",
		8 => "Transcendence",
		9 => "MAX"
	);

	$armour_base_dict = array(
		1 => "Armour",
		2 => "Shell",
		3 => "Mail",
		4 => "Plate",
		5 => "Lorica"
	);

	$wing_base_dict = array(
		1 => "Webbed Wings",
		2 => "Feathered Wings",
		3 => "Mystical Wings",
		4 => "Dimensional Wings",
		5 => "Rift Wings",
		6 => "Wonderous Wings",
		7 => "Bone Wings",
		8 => "Ethereal Wings",
		9 => "Sanctified Wings"
	);
	$crest_base_list = array("Halo", "Horns", "Crown", "Tiara");
	
	// Attack Speed Ranges by Tier
	$speed_range_list = [
		[1.00, 1.10], [1.10, 1.20], [1.20, 1.30], [1.30, 1.50],
		[1.50, 2.00], [2.00, 2.50], [2.50, 3.00], [3.00, 3.50], [4.00, 4.00]
	];

	// Damage tier list
	$damage_tier_list = [
		[500, 5000], [5000, 7500], [7500, 10000], [10000, 15000],
		[15000, 25000], [25000, 50000], [50000, 100000], [100000, 200000], [250000, 250000]
	];

	
	// Data lists
	$skill_data = json_decode(file_get_contents('./bot_php/skills.json'), true);
	$ring_skill_data = json_decode(file_get_contents('./bot_php/ring_skill_data.json'), true);
	$tarot_data = json_decode(file_get_contents('./bot_php/tarot.json'), true);
	$pact_data = json_decode(file_get_contents('./bot_php/pact_data.json'), true);
	$path_data = json_decode(file_get_contents('./bot_php/path_data.json'), true);
	$keyword_data = json_decode(file_get_contents('./bot_php/keyword_data.json'), true);
	$quest_data = json_decode(file_get_contents('./bot_php/questdata.json'), true);
	$quest_exceptions = json_decode(file_get_contents('./bot_php/questexceptions.json'), true);

	$tarot_map = [];
	foreach ($tarot_data as $entry) { $tarot_map[$entry['Name']] = ['type' => $entry['type'], 'numeral' => $entry['Numeral']]; }

	$glyph_data = $path_data['glyph_data'];
	$path_perks = $path_data['path_perks'];
	
	$damage_rolls = [];
	$penetration_rolls = [];
	$curse_rolls = [];
	$defensive_rolls = [];
	$shared_unique_rolls = [];
	$weapon_unique_rolls = [];
	$armour_unique_rolls = [];
	$accessory_unique_rolls = [];
	$unique_skill_rolls = [];
	
	foreach ($skill_data as $key => $value) {
		$prefix = explode('-', $key)[0];
		switch ($prefix) {
			case 'damage':
				$damage_rolls[$key] = $value;
				break;
			case 'penetration':
				$penetration_rolls[$key] = $value;
				break;
			case 'curse':
				$curse_rolls[$key] = $value;
				break;
			case 'defensive':
				$defensive_rolls[$key] = $value;
				break;
			case 'unique':
				$subtype = explode('-', $key)[2];
				if ($subtype == 's') {
					$shared_unique_rolls[$key] = $value;
				} elseif ($subtype == 'w') {
					$weapon_unique_rolls[$key] = $value;
				} elseif ($subtype == 'a') {
					$armour_unique_rolls[$key] = $value;
				} elseif ($subtype == 'y') {
					$accessory_unique_rolls[$key] = $value;
				} elseif (in_array($subtype, ['Knight', 'Ranger', 'Assassin', 'Mage', 'Weaver', 'Rider', 'Summoner'])) {
					$unique_skill_rolls[$key] = $value;
				}
				break;
			default:
				break;
		}
	}
	
	$total_damage_weighting = array_sum(array_column($damage_rolls, 2));
	$total_penetration_weighting = array_sum(array_column($penetration_rolls, 2));
	$total_curse_weighting = array_sum(array_column($curse_rolls, 2));
	$total_defensive_weighting = array_sum(array_column($defensive_rolls, 2));
	$total_shared_unique_weighting = array_sum(array_column($shared_unique_rolls, 2));
	$total_weapon_unique_weighting = array_sum(array_column($weapon_unique_rolls, 2));
	$total_armour_unique_weighting = array_sum(array_column($armour_unique_rolls, 2));
	$total_accessory_unique_weighting = array_sum(array_column($accessory_unique_rolls, 2));
	$total_unique_skill_weighting = array_sum(array_column($unique_skill_rolls, 2));
	
	$unique_rolls = [
			's' => [$shared_unique_rolls, $total_shared_unique_weighting],
			'w' => [$weapon_unique_rolls, $total_weapon_unique_weighting],
			'a' => [$armour_unique_rolls, $total_armour_unique_weighting],
			'y' => [$accessory_unique_rolls, $total_accessory_unique_weighting],
			'SKILL' => [$unique_skill_rolls, $total_unique_skill_weighting]
		];
	
	$item_roll_master_dict = [
		'damage' => [$damage_rolls, $total_damage_weighting],
		'penetration' => [$penetration_rolls, $total_penetration_weighting],
		'curse' => [$curse_rolls, $total_curse_weighting],
		'defensive' => [$defensive_rolls, $total_defensive_weighting],
		'unique' => $unique_rolls
	];
	
	$valid_rolls = array_keys($damage_rolls) +
				   array_keys($penetration_rolls) +
				   array_keys($curse_rolls) +
				   array_keys($defensive_rolls) +
				   array_keys($shared_unique_rolls) +
				   array_keys($weapon_unique_rolls) +
				   array_keys($armour_unique_rolls) +
				   array_keys($accessory_unique_rolls) +
				   array_keys($unique_skill_rolls);
				   
	$roll_structure_dict = array(
		"W" => array("damage", "damage", "penetration", "penetration", "curse", "unique"),
		"A" => array("damage", "penetration", "curse", "defensive", "defensive", "unique"),
		"V" => array("damage", "penetration", "curse", "defensive", "defensive", "unique"),
		"Y" => array("damage", "damage", "damage", "penetration", "curse", "unique"),
		"G" => array("damage", "damage", "damage", "penetration", "curse", "unique"),
		"C" => array("damage", "damage", "damage", "penetration", "curse", "unique"),
		"D1" => array("damage", "damage", "damage", "penetration", "defensive", "defensive"),
		"D2" => array("damage", "damage", "penetration", "penetration", "defensive", "defensive"),
		"D3" => array("damage", "damage", "damage", "penetration", "curse", "defensive"),
		"D4" => array("damage", "damage", "penetration", "penetration", "curse", "defensive"),
		"D5" => array("damage", "damage", "penetration", "penetration", "curse", "curse")
	);	

	$rare_ability_dict = [
        "Overflow" => ["Elemental", 2],
        "Mastery" => ["class_multiplier", 0.1],
        "Immortality" => ["immortal", true],
        "Omega" => ["Critical", 1],
        "Combo" => ["Combo", 1],
        "Reaper" => ["Bleed", 1],
        "Overdrive" => ["Ultimate", 1],
        "Unravel" => ["Temporal", 1],
        "Vitality" => ["Life", 1],
        "Manatide" => ["Mana", 1]
    ];	
		
	// Raw data lists
	$boss_list = array("Fortress", "Dragon", "Demon", "Paragon", "Arbiter", "Incarnate", "Ruler");
	$raid_bosstype_list = array("Ruler");
	$ring_item_type = [null, null, null, "Signet", "Element_Ring", "Path_Ring", "Fabled_Ring", "Sovereign_Ring", "Sacred_Ring"];
	$ring_category = [null, null, null, "Elemental", "Primordial", "Pathwalker", "Fabled", "Sovereign", "Sacred"];
	$sovereign_item_list = ["Crown of Skulls", "Twin Rings of Divergent Stars", "Hadal's Raindrop", "Heavenly Calamity", "Stygian Calamity", "Pandora's Universe Hammer", "Solar Flare Blaster", "Ruler's Crest", "Bathyal, Enigmatic Chasm Bauble", "Fallen Lotus of Nephilim", "Chromatic Tears"];
	$tag_dict = ["W"=> "Weapon", "A" => "Armour", "V" => "Greaves", "Y" => "Amulet", "R" => "Ring", "G" => "Wings", "C" => "Crest"];
	$element_names = ["Fire", "Water", "Lightning", "Earth", "Wind", "Ice", "Shadow", "Light", "Celestial"];
	$element_special_names = ["Volcanic", "Aquatic", "Voltaic", "Seismic", "Cyclonic", "Arctic", "Lunar", "Solar", "Cosmic"];
	$card_variant = ["Empty", "Prelude", "Emergence", "Chromatic", "Prismatic", "Resplendent", "Iridescent", "Transcendent", "Masterpiece", "Inverted"];
	$gem_point_dict = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 6, 6 => 8, 7 => 10, 8 => 15, 9 => 20];
	$tarot_damage = [0, 5000, 25000, 50000, 100000, 250000, 500000, 750000, 1000000, 10000000];
	$tarot_hp = [0, 5000, 1000, 1500, 2000, 2500, 5000, 10000, 20000, 50000];
	$tarot_fd = [0, 10, 20, 30, 40, 50, 70, 100, 200, 500];
	$tarot_point_values = [0, 1, 2, 3, 4, 5, 7, 10, 20, 50];
	$tarot_rate_map = [1 => 75, 2 => 50, 3 => 40, 4 => 30, 5 => 20, 6 => 10, 7 => 99, 8=>100];
	$insignia_name_list = [null, "Monolith", "Dyadic", "Trinity", "Tetradic", "Pentagram", null, null, null, "Refraction"];
	$insignia_description_list = [null, "One element: ", "Two elements: ", "Three elements: ", "Four elements: ", "Five elements: ", null, null, null, "All elements: "];
	$insignia_multipliers = [[0, 0], [150, 25], [75, 25], [50, 25], [75, 10], [50, 10], [0, 0], [0, 0], [0, 0], [25, 10]];
	$insignia_hp_list = [0, 750, 1500, 1500, 2000, 2500, 5000, 10000, 20000, 50000];
	$insignia_damage = [0, 5000, 10000, 25000, 50000, 100000, 250000, 500000, 750000, 1000000];
	$insignia_prefix = ["Dormant", "Awakened", "Evolved", "Infused", "Symbiotic", "Resonating", "Mutation: Wish", "Mutation: Abyss", "Mutation: Divine", "Mutation: Sacred"];
	$path_names = ["Storms", "Frostfire", "Horizon", "Eclipse", "Stars", "Solar Flux", "Lunar Tides", "Terrestria", "Confluence"];
	$class_names = ["Knight", "Ranger", "Mage", "Assassin", "Weaver", "Rider", "Summoner"];
	$scaling_rings = ["Crown of Skulls", "Chromatic Tears"];
	$ultra_id_list = array_merge(["Skull3", "EssenceXXX"], array_map(function($x) {
		return "Lotus" . $x;
	}, range(1, 9)));
	$uber_id_list = ["Gemstone11", "DarkStar", "LightStar", "Nadir", "Lotus10"];
	$ultimate_id_list = ["Skull4", "Nephilim", "Sacred", "Ruler", "Salvation", "Pandora", "Lotus11"];
	$u_rarity_id_list = array_merge($ultra_id_list, $uber_id_list, $ultimate_id_list);
	
	
	// Sovereign Data List
	$sov_item = [
		"Pandora's Universe Hammer" => [
			[9999999, 9999999], [2, 3],
			["Genesis Dream (TYPE)", "Universal Advent"],
			["Divine Genesis (TYPE)", "Entwined Universe"]
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
			["Divine Glare", "Ruler's Aegis"]
		]
	];
	$random_values_dict = [
		"Solar Flare Blaster" => [100, 500]
	];
	$sov_type_dict = [
		"Bathyal, Enigmatic Chasm Bauble" => ["Critical", "Fractal", "Temporal", "Hyperbleed", "Combo", "Bloom"],
		"Pandora's Universe Hammer" => array_merge($element_names, array_slice($path_names, 0, -4), ["Omni"])
	];

	// Quest
	$ring_check = ["Twin Rings of Divergent Stars", "Crown of Skulls", "Chromatic Tears"];

	// Artist Data List
	$artist_numerals = [
		"Daerun" => ["III", "VI", "VII", "XIV", "XIII", "XXIV"],
		"Heng Ming Chiun" => ["IV", "VIII", "XV", "XVI", "XXII", "XXVI"],
		"Quan Bui" => ["X", "XI", "XII"],
		"Denny Rasyid Salam" => ["IX", "V"],
		"Martin Steffen" => ["I", "XXVI"],
		"Aiming Chen" => ["XXIII", "XVIII", "XIX"],
		"Alina Arkhipova" => ["XXVIII"],
		"Elvany Destiani" => ["0", "II", "XXV", "XVII", "XXIX"],
		"Maybelle Gormate" => ["XVII"],
		"Leonardo Guinard"=> ["XXXC", "XXXD"],
		"Tan kwokYeow" => ["XXXA", "XXXB"]
	];	
?>
