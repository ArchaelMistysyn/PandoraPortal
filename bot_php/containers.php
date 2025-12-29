<?php
    // Interfaces - Inventory Container
    $inventoryContainerHTML = '<div id="inventory-container">';
        $inventoryContainerHTML .= '<div id="inventory-menu">';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Crafting" onclick="onInventory(\'Crafting\')">Crafting</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Fae Cores" onclick="onInventory(\'Fae Cores\')">Fae Cores</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Materials" onclick="onInventory(\'Materials\')">Materials</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Unprocessed" onclick="onInventory(\'Unprocessed\')">Unprocessed</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Essences" onclick="onInventory(\'Essences\')">Essences</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Summoning" onclick="onInventory(\'Summoning\')">Summoning</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Gemstone" onclick="onInventory(\'Gemstone\')">Gemstone</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Fish" onclick="onInventory(\'Fish\')">Fish</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Misc" onclick="onInventory(\'Misc\')">Misc</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="Ultra Rare" onclick="onInventory(\'Ultra Rare\')">Ultra Rare</button>';
            $inventoryContainerHTML .= '<button class="sort-button" data-value="" onclick="onInventory()">Show All</button>';
        $inventoryContainerHTML .= '</div>';
        $inventoryContainerHTML .= '<div id="inventory-screen"></div>';
    $inventoryContainerHTML .= '</div>';

    // Interfaces - Gear Container
    $gearContainerHTML = '<div id="gear-container">';
        $gearContainerHTML .= '<div id="gear-menu">';
            $gearContainerHTML .= '<button class="sort-button" data-value="Weapon" onclick="onGear(\'Weapon\')">Weapon</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Armour" onclick="onGear(\'Armour\')">Armour</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Greaves" onclick="onGear(\'Greaves\')">Greaves</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Amulet" onclick="onGear(\'Amulet\')">Amulet</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Wings" onclick="onGear(\'Wings\')">Wings</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Crest" onclick="onGear(\'Crest\')">Crest</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Ring" onclick="onGear(\'Ring\')">Ring</button>';
            $gearContainerHTML .= '<button class="sort-button" data-value="Gem" onclick="onGear(\'Gem\')">Gem</button>';
        $gearContainerHTML .= '</div>';
        $gearContainerHTML .= '<div id="gear-screen-container">';
            $gearContainerHTML .= '<div id="gear-screen"></div>';
            $gearContainerHTML .= '<div id="equipped-gear"></div>';
        $gearContainerHTML .= '</div>';
    $gearContainerHTML .= '</div>';

    // Interfaces - Travel
    $travelContainerHTML = '<div id="travel-container">';
        $travelContainerHTML .= '<div id="travel-menu">';
            $travelContainerHTML .= '<button class="travel-button lightbox-button-gray" data-plane="exploration">Locked</button>';
            $travelContainerHTML .= '<button class="travel-button lightbox-button-gray" data-plane="mortal">Locked</button>';
            $travelContainerHTML .= '<button class="travel-button lightbox-button-gray" data-plane="celestial">Locked</button>';
            $travelContainerHTML .= '<button class="travel-button lightbox-button-gray" data-plane="divine">Locked</button>';
            $travelContainerHTML .= '<button class="travel-button lightbox-button-gray" data-plane="abyss">Locked</button>';
        $travelContainerHTML .= '</div>';
        $travelContainerHTML .= '<div id="travel-submenu"></div>';
        $travelContainerHTML .= '<div id="travel-screen-a">';
            $travelContainerHTML .= '<div id="room-image"></div>';
            $travelContainerHTML .= '<div id="room-menu"></div>';
        $travelContainerHTML .= '</div>';
        $travelContainerHTML .= '<div id="travel-screen-b"></div>';
    $travelContainerHTML .= '</div>';

    // Interfaces - Forge Container
    $forgeContainerHTML = '<div id="forge-container">';
        $forgeContainerHTML .= '<div id="gear-menu">';
            $forgeContainerHTML .= '<button class="sort-button" data-value="W" onclick="onForge(\'W\')">Weapon</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="A" onclick="onForge(\'A\')">Armour</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="V" onclick="onForge(\'V\')">Greaves</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="Y" onclick="onForge(\'Y\')">Amulet</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="G" onclick="onForge(\'G\')">Wings</button>';
            $forgeContainerHTML .= '<button class="sort-button" data-value="C" onclick="onForge(\'C\')">Crest</button>';
        $forgeContainerHTML .= '</div>';
        $forgeContainerHTML .= '<div id="forge-screen-container">';
            $forgeContainerHTML .= '<div id="forge-item-screen"></div>';
            $forgeContainerHTML .= '<div id="forge-menu"></div>';
        $forgeContainerHTML .= '</div>';
    $forgeContainerHTML .= '</div>';

    // Interfaces - Refinery Container
    $refineryContainerHTML = '<div id="refine-container">';
        $refineryContainerHTML .= '<div id="gear-menu">';
            $refineryContainerHTML .= '<button class="sort-button" data-value="Basic" onclick="onRefine(\'Basic\')">Basic Gear</button>';
            $refineryContainerHTML .= '<button class="sort-button" data-value="Void" onclick="onRefine(\'Void\')">Void Gear</button>';
            $refineryContainerHTML .= '<button class="sort-button" data-value="Gem" onclick="onRefine(\'Gem\')">Gem</button>';
            $refineryContainerHTML .= '<button class="sort-button" data-value="Jewel" onclick="onRefine(\'Jewel\')">Jewel</button>';
        $refineryContainerHTML .= '</div>';
        $refineryContainerHTML .= '<div id="refine-screen-container">';
            $refineryContainerHTML .= '<div id="refine-item-screen" class="item-slot"></div>';
            $refineryContainerHTML .= '<div id="refine-menu"></div>';
        $refineryContainerHTML .= '</div>';
    $refineryContainerHTML .= '</div>';

    // Interfaces - Quest Container
    $questContainerHTML = '<div id="quest-container" style="display: none;">';
        $questContainerHTML .= '<div id="quest-left-panel">';
            $questContainerHTML .= '<div id="quest-box"></div>';
            $questContainerHTML .= '<div id="quest-button-group">';
                $questContainerHTML .= '<button id="quest-action-btn-1" class="lightbox-button-gray"></button>';
                $questContainerHTML .= '<button id="quest-action-btn-2" class="lightbox-button-gray"></button>';
                $questContainerHTML .= '<button id="quest-action-btn-3" class="lightbox-button-gray"></button>';
            $questContainerHTML .= '</div>';
        $questContainerHTML .= '</div>';
        $questContainerHTML .= '<div id="quest-right-panel">';
            $questContainerHTML .= '<img id="quest-character-image" src="" alt="Character" />';
        $questContainerHTML .= '</div>';
    $questContainerHTML .= '</div>';

    // Interfaces - Login
    $login_form = '<div id="login-container">';
        $login_form .= '<div id="login-header">Login Required</div>';
        $login_form .= '<ol id="login-instructions">';
            $login_form .= '<li>Join the discord.</li>';
            $login_form .= '<li>/Register with the bot.</li>';
            $login_form .= '<li>Direct Message the bot "login" to get your id and key.</li>';
            $login_form .= '<li>Directly Messaging "reset" to the bot will give you a new key.</li>';
        $login_form .= '</ol>';
        $login_form .= '<form id="login-form" method="POST">';
            $login_form .= '<label for="discord_id">Discord ID:</label>';
            $login_form .= '<input type="text" name="discord_id" required>';
            $login_form .= '<label for="login_key">Login Key:</label>';
            $login_form .= '<input type="password" name="login_key" required>';
            $login_form .= '<label><input type="checkbox" name="remember_me"> Remember Me</label>';
            $login_form .= '<button type="submit">Login</button>';
        $login_form .= '</form>';
    $login_form .= '</div>';

    // Interfaces - Battle Container
    $battleContainerHTML = '<div id="battle-container">';
        $battleContainerHTML .= '<div id="battle-menu">';
            $battleContainerHTML .= '<div id="battle-menu-toggle" class="battle-button-red" onclick="battleToggle()">Solo Encounter</div>';
            $battleContainerHTML .= '<div id="battle-slider-container">';
                $battleContainerHTML .= '<label for="magnitude-slider" class="slider-label">Magnitude: <span id="magnitude-value">0</span></label>';
                $battleContainerHTML .= '<input type="range" id="magnitude-slider" min="0" max="10" value="0" step="1" oninput="document.getElementById(\'magnitude-value\').innerText = this.value">';
            $battleContainerHTML .= '</div>';
            // Solo Menu Buttons
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="0" data-type="Any">Random</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="0" data-type="Fortress">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="1" data-type="Dragon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="3" data-type="Demon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="5" data-type="Paragon">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="7" data-type="Summon1">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="8" data-type="Summon2">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-echelon="9" data-type="Arbiter">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-solo" data-quest="48" data-type="Summon3">Locked</div>';
            // Special Modes Buttons
            // $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-echelon="1" data-type="Arena">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="49" data-type="Palace1">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="50" data-type="Palace2">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-level="200" data-type="Palace3">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-quest="36" data-type="Gauntlet">Locked</div>';
            // $battleContainerHTML .= '<div class="battle-button-locked battle-button-special" data-echelon="5" data-type="Ruler">Locked</div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
            $battleContainerHTML .= '<div class="battle-button-placeholder battle-button-special"></div>';
        $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '<div id="battle-screen">';
            $battleContainerHTML .= '<div id="battle-screen-bg"></div>';
            $battleContainerHTML .= '<div id="action-box">';
                $battleContainerHTML .= '<div id="action-box-name"></div><div id="action-box-value"></div><div id="action-box-image"></div>';
                $battleContainerHTML .= '<div id="action-box-menu"></div>';
            $battleContainerHTML .= '</div>';
            $battleContainerHTML .= '<div id="battle-detail-box">';
                $battleContainerHTML .= '<div id="log-boss-header"><span id="log-boss-name" class="highlight-text"></span><span id="log-boss-lvl"></span></div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-top">';
                    $battleContainerHTML .= '<div id="log-boss-hp"></div>';
                    $battleContainerHTML .= '<div id="log-boss-details"></div>';
                    $battleContainerHTML .= '<div id="log-boss-status"></div>';
                    $battleContainerHTML .= '<div id="weakness-tag"><u>Weakness Types</u></div>';
                    $battleContainerHTML .= '<div id="log-boss-weakness"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-mid">';
                    $battleContainerHTML .= '<div id="log-cycles"></div>';
                    $battleContainerHTML .= '<div id="log-dps"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-player-section">';
                    $battleContainerHTML .= '<div id="log-player-hp"></div>';
                    $battleContainerHTML .= '<div id="log-player-recovery"></div>';
                    $battleContainerHTML .= '<div id="log-player-status"></div>';
                $battleContainerHTML .= '</div>';
                $battleContainerHTML .= '<div class="style-line"></div>';
                $battleContainerHTML .= '<div id="log-actions-section"></div>';
            $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '</div>';
        $battleContainerHTML .= '<div id="battle-cover"></div>';
    $battleContainerHTML .= '</div>';

    // Interfaces - Lore Container
    $loreContainerHTML = '<div id="lore-container">';
    $loreContainerHTML .= '<div id="lore-menu">';
        $loreContainerHTML .= '<button id="lore-menu-toggle" class="lore-button-red" onclick="loreToggle()">Hide</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story1" onclick="onLore(\'story1\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story2" onclick="onLore(\'story2\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story3" onclick="onLore(\'story3\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="story4" onclick="onLore(\'story4\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel1" onclick="onLore(\'prequel1\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel2" onclick="onLore(\'prequel2\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel3" onclick="onLore(\'prequel3\')">Locked</button>';
        $loreContainerHTML .= '<button class="lore-button-locked" data-value="prequel4" onclick="onLore(\'prequel4\')">Locked</button>';
    $loreContainerHTML .= '</div>';
    $loreContainerHTML .= '<div id="lore-screen"></div>';
    $loreContainerHTML .= '</div>';

    // Shop Container
    $shopContainerHTML  = '<div id="shop-container">';
        $shopContainerHTML .= '<div id="shop-menu"></div>';
        $shopContainerHTML .= '<div id="tarot-screen"></div>';
        $shopContainerHTML .= '<div id="cathedral-screen"></div>';
        $shopContainerHTML .= '<div id="shop-screen"></div>';
        $shopContainerHTML .= '<div id="infuse-screen"></div>';
    $shopContainerHTML .= '</div>';

    // Default Containers
    $containersHTML = $inventoryContainerHTML . $gearContainerHTML . $forgeContainerHTML . $loreContainerHTML . $battleContainerHTML . $questContainerHTML;
    $containersHTML .= $travelContainerHTML .  $refineryContainerHTML . $shopContainerHTML;
?>