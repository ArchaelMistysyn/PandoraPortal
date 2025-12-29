<?php
$map_tier_dict = [
    "Ancient Ruins" => 1, "Spatial Dungeon" => 2, "Celestial Labyrinth" => 3,
    "Starlit Grotto" => 4, "Void Temple" => 5, "Citadel of Miracles" => 6,
    "Abyssal Sanctum" => 7, "Divine Ziggurat" => 8, "Cradle of Samsara" => 9,
    "Rift of the Chaos God" => 10
];
$valid_maps = array_keys($map_tier_dict);

$random_room_list = [
    ["trap_room", 1],
    ["statue_room", 1],
    ["healing_room", 1],
    ["basic_treasure", 1],
    ["basic_monster", 1],
    ["elite_monster", 2],
    ["legend_monster", 3],
    ["epitaph_room", 2],
    ["penetralia_room", 3],
    ["selection_room", 4],
    ["crystal_room", 4],
    ["pact_room", 5],
    ["heart_room", 5],
    ["trial_room", 6],
    ["boss_shrine", 6],
    ["sanctuary_room", 7]
];

$room_button_dict = [
    "trap_room" => [
        ["label" => "Salvage", "style" => "blue", "action" => "trapSalvage"],
        ["label" => "Bypass",  "style" => "gray", "action" => "trapBypass"]
    ],
    "statue_room" => [
        ["label" => "Pray",     "style" => "blue", "action" => "prayStatue"],
        ["label" => "Destroy",  "style" => "red",  "action" => "destroyStatue"]
    ],
    "healing_room" => [
        ["label" => "Short Rest", "style" => "blue",  "action" => "shortRest"],
        ["label" => "Long Rest",  "style" => "green", "action" => "longRest"]
    ],
    "basic_monster" => [
        ["label" => "Fight",   "style" => "red",  "action" => "basicFight"],
        ["label" => "Stealth", "style" => "blue", "action" => "basicStealth"]
    ],
    "elite_monster" => [
        ["label" => "Fight",   "style" => "red",  "action" => "eliteFight"],
        ["label" => "Stealth", "style" => "blue", "action" => "eliteStealth"]
    ],
    "legend_monster" => [
        ["label" => "Fight",   "style" => "red",  "action" => "legendFight"],
        ["label" => "Stealth", "style" => "blue", "action" => "legendStealth"]
    ],
    "basic_treasure" => [
        ["label" => "Open Chest", "style" => "green", "action" => "basic_treasure"],
        ["label" => "Bypass",     "style" => "gray",  "action" => "skipTreasure"]
    ],
    "greater_treasure" => [
        ["label" => "Open Chest", "style" => "green", "action" => "greater_treasure"],
        ["label" => "Bypass",     "style" => "gray",  "action" => "skipTreasure"]
    ],
    "epitaph_room" => [
        ["label" => "Search",   "style" => "blue", "action" => "searchEpitaph"],
        ["label" => "Decipher", "style" => "blue", "action" => "decipherEpitaph"]
    ],
    "selection_room" => [
        ["label" => "Option 1", "style" => "blue", "action" => "selectOption1"],
        ["label" => "Option 2", "style" => "blue", "action" => "selectOption2"],
        ["label" => "Both",     "style" => "red",  "action" => "selectBoth"]
    ],
    "pact_room" => [
        ["label" => "Refuse Pact", "style" => "red",   "action" => "refusePact"],
        ["label" => "Forge Pact",  "style" => "green", "action" => "forgePact"]
    ],
    "heart_room" => [
        ["label" => "Purify", "style" => "green", "action" => "purifyHeart"],
        ["label" => "Taint",  "style" => "red",   "action" => "taintHeart"]
    ],
    "boss_shrine" => [
        ["label" => "Option 1", "style" => "blue", "action" => "shrineOption1"],
        ["label" => "Option 2", "style" => "blue", "action" => "shrineOption2"],
        ["label" => "Option 3", "style" => "red",  "action" => "shrineOption3"]
    ],
    "trial_room" => [],
    "sanctuary_room" => [
        ["label" => "Option 1", "style" => "blue", "action" => "sanctuaryOption1"],
        ["label" => "Option 2", "style" => "blue", "action" => "sanctuaryOption2"],
        ["label" => "Option 3", "style" => "blue", "action" => "sanctuaryOption3"]
    ],
    "crystal_room" => [
        ["label" => "Resonate", "style" => "blue", "action" => "crystalResonate"],
        ["label" => "Search",   "style" => "blue", "action" => "crystalSearch"]
    ],
    "penetralia_room" => [
        ["label" => "Search",  "style" => "blue",  "action" => "searchPenetralia"],
        ["label" => "Collect", "style" => "green", "action" => "collectPenetralia"]
    ],
    "jackpot_room" => [
        ["label" => "Search",  "style" => "blue",  "action" => "searchJackpot"],
        ["label" => "Collect", "style" => "green", "action" => "collectJackpot"]
    ]
];

$trial_variants = [
    "Offering" => [
        "description" => "Pay with your life.",
        "options" => [
            ["label" => "Pain (10%)", "style" => "blue", "action" => "trialOption1"],
            ["label" => "Blood (30%)", "style" => "blue", "action" => "trialOption2"],
            ["label" => "Death (50%)", "style" => "blue", "action" => "trialOption3"]
        ]
    ],
    "Greed" => [
        "description" => "Pay with your wealth.",
        "options" => [
            ["label" => "Poor (1,000)", "style" => "blue", "action" => "trialOption1"],
            ["label" => "Affluent (5,000)", "style" => "blue", "action" => "trialOption2"],
            ["label" => "Rich (10,000)", "style" => "blue", "action" => "trialOption3"]
        ]
    ],
    "Soul" => [
        "description" => "Pay with your stamina.",
        "options" => [
            ["label" => "Vitality (100)", "style" => "blue", "action" => "trialOption1"],
            ["label" => "Spirit (200)", "style" => "blue", "action" => "trialOption2"],
            ["label" => "Essence (300)", "style" => "blue", "action" => "trialOption3"]
        ]
    ]
];
$greed_cost_list = [1000, 5000, 10000];

$shrine_dict = [
    1 => ["Land", 3, "Sky", 4],
    2 => ["Fear", 6, "Suffering", 0],
    3 => ["Illumination", 7, "Tranquility", 1],
    4 => ["Retribution", 2, "Imprisonment", 5],
];

$trap_room_name_list = ["Warm", "Damp", "Charged", "Unstable", "Breezy", "Cold", "Dim", "Bright", "Disorienting"];

$trap_trigger_list_default = [
    "Magma streams out from jets hidden in the wall.",
    "A pit opens, plunging you into a whirling vortex. Despite your injuries you pull yourself back up.",
    "Lightning bolts and surges through you at full force before the room becomes silent once more.",
    "Boulders and debris roll through the passageway smashing you around as they pass by.",
    "The sharp winds cut and tear at your skin as you push forward.",
    "Icicles fall from the ceiling in great number. Injury is unavoidable.",
    "Blinded by the darkness, you lose your way and arrive at another location.",
    "Blinded by a flash of light, you lose your way and arrive at another location.",
    "Feeling space itself shift around you, you find yourself in an entirely new location."
];

$trap_trigger_list_death = [
    "The ground opens beneath you and the last thing you feel is lava melting your skin and bones.",
    "The doors of the room slam closed and water floods the room. Unable to find an escape, your lungs fill with water.",
    "Paralyzed by high voltage energy, you collapse. Never to stand again.",
    "Your movement slows and an uneasy feeling creeps up your leg. Within mere moments you are fully petrified.",
    "Winds howl from all directions and the strong gusts swiftly tear you apart.",
    "The cold stops you in your tracks. Slowly you find yourself unable to move at all, fully entombed in ice.",
    "Dark smoke fills the room. As you breath it in you feel your mind melting until nothing remains.",
    "Light particles gather and condense. Within moments, multiple lasers slash through your feeble armour and body.",
    "A celestial miasma permeates the room. Unable to prevent exposure your body disintegrates."
];

$selection_pools = [
    ["Hammer", "Pearl", "Ore1", "Trove1", "Potion1", "Scrap", "ESS"],
    ["Hammer", "Pearl", "Ore2", "Token1", "Trove2", "Flame1", "Matrix", "Trove2", "Summon1", "Potion2", "ESS"],
    ["Hammer", "Pearl", "Ore3", "Token2", "Trove3", "Stone4", "Gem1", "Gem2", "Gem3", "Potion3", "ESS"],
    ["Hammer", "Pearl", "Ore4", "Token3", "Token4", "Trove4", "Summon1", "Crystal1", "Jewel1", "Jewel2", "Potion4", "ESS"],
    ["Skull1", "Hammer", "Pearl", "Ore5", "Token5", "Trove5", "Summon2", "Crystal2", "Flame2", "Jewel3", "ESS"],
    ["Skull2", "Hammer", "Pearl", "Ore5", "Token7", "Token6", "Trove7", "Trove6", "Summon3", "Crystal3", "Jewel4", "Compass", "ESS"],
    ["Skull3", "Lotus2", "Lotus3", "Lotus4", "Lotus5", "Lotus6", "Lotus7", "Lotus8", "Lotus9", "Lotus1",
     "Lotus10", "DarkStar", "LightStar", "Trove8", "Crystal4", "Jewel5", "ESS", "Trove9"]
];

$blessing_rewards = [
    "Incarnate" => [
        8 => ["Divine",    "Crystal4", 15],
        0 => ["Divine",    "Crystal4", 15]
    ],
    "Arbiter" => [
        7 => ["Prismatic", "Summon3", 10],
        0 => ["ARBITER",   "Stone5",  7]
    ],
    "Paragon" => [
        6 => ["Miraculous", "Summon2", 5],
        5 => ["Deity's",    "Summon1", 3],
        0 => ["PARAGON",    "Stone4",  2]
    ]
];

$wrath_msg_list = [
    "0"   => ["To reverse your actions onto yourself is a basic preservation of the balance.", 1],
    "I"   => ["May your unworthy eyes be blessed by the sight of my magic as it tears you apart.", 1],
    "II"  => ["Why would you do that? What is wrong with you? How can you be so cruel...", 0],
    "III" => ["I see somebody wants to help me test my new obliteration magic. How kind of you.", 1],
    "IV"  => ["I praise you for being so bold as to draw my ire. However, your transgression will not go unpunished.", 999999999],
    "V"   => ["There is balance to all things. Can you bear the weight of your sins?", 5000],
    "VI"  => ["Heartless.\nHow empty are you, that you can be so cold?", 0],
    "VII" => ["The celestial dragon manifests atop the statue's rubble. The last thing you feel is the feeling of it's dimensional claws tearing you apart.", 1],
    "VIII"=> ["A powerful roar shakes the room. You flee from the great beast.", 2],
    "IX"  => ["You will forget what you have done, but I will remember. Begone.", 2],
    "X"   => ["I will undo your mistake. Choose more wisely this time if you value the time you have remaining.", 2],
    "XI"  => ["Unfathomable, to enact such sacrilege in my presence. I will judge your actions before the eyes of god.", 1],
    "XII" => ["Gaze too deep into the abyss, and I will gaze back.", 1],
    "XIII"=> ["Mortality is as frail as this statue, but to openly invite death is the height of foolishness.", 1],
    "XIV" => ["Your actions muddy the waters and cloud the sky. To restore purity you must be purged.", 1],
    "XV"  => ["You think I am beneath you, human!? You will burn where you stand.", 1],
    "XVI" => ["If pandora wants to reclaim her authority she should've sent someone stronger.", 5000],
    "XVII"=> ["A shooting star sails across the silent sky to strike the sinner where they stand.", 1],
    "XVIII"=> ["You're lucky Luma isn't here. Leave before I change my mind.", 0],
    "XIX" => ["Feel the fury of 10,000 suns!", 10000],
    "XX"  => ["Listen closely, and you can hear the sounds of your beating heart fade away.", 1],
    "XXI" => ["Just because I am The Creation does not mean I am incapable of destruction.", 1],
    "XXII"=> ["You've crossed the point of no return. Be undone by the very laws you sought to defy.", 1],
    "XXIII"=> ["I will set you back onto the right path.", 2],
    "XXIV"=> ["These are the threads of your soul. Any last words before I snap them?", 1],
    "XXV" => ["I see now that I was wrong. My only wish in this moment is that you never existed.", 1],
    "XXVI"=> ["You will replay this debt in blood!", 1],
    "XXVII"=> ["I will now read the verdict. Death.", 1],
    "XXVIII"=> ["Just as this was destined, so is your resulting demise.", 1],
    "XXIX"=> ["It is your right to take action, however it is divine right to make you bear the consequences.", 1],
    "XXX" => ["???", 1],
];

# Adjust button colours later?

$element_descriptor_list = ["Pyro", "Aqua", "Voltaic", "Stone", "Sky", "Frost", "Shadow", "Luminous", "Celestial"];
$basic_monsters = ["Skeleton Knight", "Skeleton Archer", "Skeleton Mage", "Ooze", "Slime", "Sprite", "Faerie", "Spider", "Goblin", "Imp", "Fiend", "Orc", "Ogre", "Lamia"];
$elite_monsters = ["Salamander", "Undine", "Raiju", "Construct", "Sylph", "Ursa", "Wraith", "Seraph", "Void Drifter"];
$legendary_monsters = [
    "Inferno-Imperator Hydra", "Ocean-Overlord Kraken", "Electric-Emperor Phoenix",
    "Time-Transgressor Wurm", "Sky-Sovereign Manta", "Winter-Warden Fenrir",
    "Underworld-Usurper Cerberus", "Crystal Conqueror Simurgh", "Eon-Eater Jormungarr"
];
$monster_dict = ["basic_monster" => $basic_monsters, "elite_monster" => $elite_monsters, "legend_monster" => $legendary_monsters];

$death_lines = [
            "Back so soon? I think I'll play with you a little longer.",
            "Death is not the end.",
            "Can't have you dying before the prelude now can we?",
            "I will overlook this, just this once.",
            "I'll have you repay this favour when the time is right.",
            "I wouldn't mind helping you for a price, but does your life truly hold any value?"
        ];

$safe_msg_list = ["You happen across a rare carbuncle. It's gentle aura heals your wounds.",
                 "You drink from the nearby magical fountain and it restores your vitality",
                 "Taking advantage of the protective barriers surrounding the room, you rest your weary body."];
?>