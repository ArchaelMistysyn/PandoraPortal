let showGearInfuse = false;

const shopOptions = {
    Market: [
        { label: "Fae Core Shop", value: "faem", quest: 0 },
        { label: "Tier 1 Shop", value: "t1m", quest: 0 },
        { label: "Tier 2 Shop", value: "t2m", quest: 6 },
        { label: "Tier 3 Shop", value: "t3m", quest: 11 },
        { label: "Tier 4 Shop", value: "t4m", quest: 16 },
        { label: "Tier 5 Shop", value: "t5m", quest: 21 },
        { label: "Tier 6+ Shop", value: "t6m", quest: 26 },
        { label: "Daily Exchange", value: "dailym", quest: 0 }
    ],  
    Cathedral: [
        { label: "Tier 1 Essence", value: "t1e", quest: 51 },
        { label: "Tier 2 Essence", value: "t2e", quest: 51 },
        { label: "Tier 3 Essence", value: "t3e", quest: 51 },
        { label: "Tier 4 Essence", value: "t4e", quest: 51 },
        { label: "Tier 5 Essence", value: "t5e", quest: 51 },
        { label: "Tier 6 Essence", value: "t6e", quest: 51 },
        { label: "Tier 7 Essence", value: "t7e", quest: 51 }
    ],
    Tarot: [
        { label: "Tier 1 Tarot", value: "t1t", quest: 0 },
        { label: "Tier 2 Tarot", value: "t2t", quest: 0 },
        { label: "Tier 3 Tarot", value: "t3t", quest: 0 },
        { label: "Tier 4 Tarot", value: "t4t", quest: 0 },
        { label: "Tier 5 Tarot", value: "t5t", quest: 0 },
        { label: "Tier 6 Tarot", value: "t6t", quest: 0 },
        { label: "Tier 7 Tarot", value: "t7t", quest: 0 }
    ],
    Infuse: [
        { label: "Heavenly Infusion", value: "heavenly_infusion", quest: 0 },
        { label: "Elemental Infusion", value: "elemental_infusion", quest: 0 },
        { label: "Crystal Infusion", value: "crystal_infusion", quest: 0 },
        { label: "Void Infusion", value: "void_infusion", quest: 0 },
        { label: "Jewel Infusion", value: "jewel_infusion", quest: 0 },
        { label: "Skull Infusion", value: "skull_infusion", quest: 0 }
    ],
    InfuseGear: [
        { label: "Special Infusion", value: "special_infusion", quest: 0 },
        { label: "Elemental Signet", value: "elemental_signet_infusion", quest: 0 },
        { label: "Primordial Ring", value: "primordial_ring_infusion", quest: 0 },
        { label: "Path Ring", value: "path_ring_infusion", quest: 0 },
        { label: "Fabled Ring", value: "fabled_ring_infusion", quest: 0 },
        { label: "Sovereign Ring", value: "sovereign_ring_infusion", quest: 0 },
        { label: "Sovereign Gear", value: "sovereign_gear_infusion", quest: 0 }
    ]
};

const infuseRecipes = {
    "heavenly_infusion": [
        "Heavenly Ore (Crude)",
        "Heavenly Ore (Cosmite)",
        "Heavenly Ore (Celestite)",
        "Heavenly Ore (Crystallite)"
    ],
    "elemental_infusion": [
        "Ruby", "Sapphire", "Topaz", "Agate", "Emerald", "Zircon", "Obsidian", "Opal", "Amethyst"
    ],
    "crystal_infusion": [
        "Crystallized Void (Fragment)",
        "Crystallized Wish (Fragment)",
        "Crystallized Abyss (Fragment)",
        "Crystallized Divinity (Fragment)",
        "Crystallized Wish (Upgrade)",
        "Crystallized Abyss (Upgrade)",
        "Crystallized Divinity (Upgrade)"
    ],
    "void_infusion": [
        "Unrefined Void Item (Weapon)", "Unrefined Void Item (Armour)", "Unrefined Void Item (Greaves)",
        "Unrefined Void Item (Amulet)", "Unrefined Void Item (Wing)", "Unrefined Void Item (Crest)"
    ],
    "jewel_infusion": [
        "Unrefined Dragon Jewel", "Unrefined Demon Jewel", "Unrefined Paragon Jewel"
    ],
    "skull_infusion": [
        "Haunted Golden Skull", "Radiant Golden Skull", "Prismatic Golden Skull"
    ],
    "special_infusion": [
        "Radiant Heart", "Chaos Heart", "Abyss Flame", "Lotus of Serenity"
    ],
    "elemental_signet_infusion": [
        "Elemental Signet of Fire", "Elemental Signet of Water", "Elemental Signet of Lightning",
        "Elemental Signet of Earth", "Elemental Signet of Wind", "Elemental Signet of Ice",
        "Elemental Signet of Shadow", "Elemental Signet of Light", "Elemental Signet of Celestial"
    ],
    "primordial_ring_infusion": [
        "Ruby Ring of Incineration", "Sapphire Ring of Atlantis", "Topaz Ring of Dancing Thunder",
        "Agate Ring of Seismic Tremors", "Emerald Ring of Wailing Winds", "Zircon Ring of the Frozen Castle",
        "Obsidian Ring of Tormented Souls", "Opal Ring of Scintillation", "Amethyst Ring of Shifting Stars"
    ],
    "path_ring_infusion": [
        "Invoking Ring of Storms", "Primordial Ring of Frostfire", "Boundary Ring of Horizon",
        "Hidden Ring of Eclipse", "Cosmic Ring of Stars", "Orbital Ring of Solar Flux",
        "Orbital Ring of Lunar Tides", "Orbital Ring of Terrestria", "Rainbow Ring of Confluence"
    ],
    "fabled_ring_infusion": [
        "Dragon's Eye Diamond", "Bleeding Hearts", "Gambler's Masterpiece",
        "Lonely Ring of the Dark Star", "Lonely Ring of the Light Star"
    ],
    "sovereign_ring_infusion": [
        "Stygian Calamity", "Heavenly Calamity", "Hadal's Raindrop", "Twin Rings of Divergent Stars",
        "Crown of Skulls", "Chromatic Tears"
    ],
    "sovereign_gear_infusion": [
        "Pandora's Universe Hammer", "Fallen Lotus of Nephilim", "Solar Flare Blaster", "Bathyal, Enigmatic Chasm Bauble", "Ruler's Crest"
    ]
};


function onShop(shopType, reload=true) {
    if (reload) {
        clearScreens();
    }
    swapShopScreen(shopType);
    shopMenu.className = (shopType === "Infuse") ? 'infuse-shop-menu' : 'basic-shop-menu';
    const options = shopType === "Infuse" ? (showGearInfuse ? shopOptions.InfuseGear : shopOptions.Infuse) : (shopOptions[shopType] || []);
    shopMenu.innerHTML = ""
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "player" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const buttons = [];
            if (shopType === "Infuse") {
                const toggleText = showGearInfuse ? "Show Non-Gear" : "Show Gear";
                buttons.push(`<button class="shop-button-red" onclick="toggleInfuseCategory()">${toggleText}</button>`);
            }
            options.forEach(opt => {
                const locked = data.player.quest < opt.quest;
                const className = locked ? 'shop-button-locked' : 'shop-button-blue';
                const onclick = locked ? '' : `onclick="selectOption('${opt.value}', '${shopType}')"`;
                buttons.push(`<button class="${className}" data-value="${opt.value}" ${onclick}>${opt.label}</button>`);
            });
            shopMenu.innerHTML = buttons.join('');
            shopContainer.style.display = 'flex';
        } else {
            alert(data.message || "Failed to load.");
        }
    })
    .catch(error => console.error('Error loading:', error));
}

function swapShopScreen(shopType) {
    const screens = {Infuse: '#infuse-screen', Market: '#shop-screen', Tarot: '#tarot-screen', Cathedral: '#cathedral-screen'};
    Object.values(screens).forEach(id => { document.querySelector(id).style.display = 'none'; });
    const target = screens[shopType];
    if (target) { document.querySelector(target).style.display = 'flex'; }
}


function selectOption(value, shopType = 'Market') {
    if (shopType === 'Tarot') {
        const tier = parseInt(value[1]);
        const filtered = Object.entries(tarotData)
            .filter(([_, card]) => card.tier === tier)
            .map(([id, card]) => `<button class="shop-button-blue" onclick="selectTarot('${id}')">${card.Name}</button>`);
        filtered.push(`<button class="shop-button-red" onclick="onShop('Tarot', false)">Back</button>`);
        shopMenu.innerHTML = filtered.join('');
        return;
    }
    if (shopType === 'Infuse') {
        if (!value && shopType === 'Infuse') {
            onShop('Infuse', false);
            return;
        }
        const recipes = infuseRecipes[value] || [];
        const buttons = recipes.map(recipe => {
            const safeRecipe = recipe.replace(/'/g, "\\'");
            return `<button class="shop-button-blue" onclick="selectInfusion('${value}', '${safeRecipe}')">${recipe}</button>`;
        });
        buttons.push(`<button class="shop-button-red" onclick="selectOption('', 'Infuse')">Back</button>`);
        shopMenu.innerHTML = buttons.join('');
        return;
    }

    const shopFilters = {
        t1m: (_, id, item) => item.cost > 0 && item.tier === 1 && !id.startsWith("Fae"), t2m: item => item.cost > 0 && item.tier === 2, 
        t3m: item => item.cost > 0 && item.tier === 3, t4m: (_, id, item) => item.cost > 0 && item.tier === 4 && !id.startsWith("Skull"),
        t5m: (_, id, item) => item.cost > 0 && item.tier === 5 && !id.startsWith("Skull"), t6m: (_, id, item) => item.cost > 0 && item.tier >= 6 && !id.startsWith("Skull"),
        faem: (_, id) => id.startsWith("Fae"), dailym: (_, id) => id.startsWith("Fae"), // REPLACE FISH LOGIC LATER NEED TO UPDATE DAILY ITEMS IN SQL OR SMTHN
        t1e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 1, t2e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 2,
        t3e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 3, t4e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 4,
        t5e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 5, t6e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 6,
        t7e: (_, id) => id.startsWith("Essence") && itemData[id].tier === 7
    };
    const filterFn = shopFilters[value];
    const items = Object.entries(itemData)
        .filter(([id, item]) => filterFn(item, id, item))
        .map(([id, item]) => `<button class="shop-button-blue" onclick="selectItem('${id}', '${shopType}', '${value}')">${item.name}</button>`);
    items.push(`<button class="shop-button-red" onclick="onShop('${shopType}', false)">Back</button>`);
    shopMenu.innerHTML = items.join('');
}

function selectItem(itemId, shopType, value) {
    fetch('./fetch_handler.php', {
        method: "POST", headers: { "Content-Type": "application/json" }, 
        body: JSON.stringify({ action: "showItemPurchase", item_id: itemId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                lightboxDisplay.innerHTML = data.html;
                lightboxMenu.innerHTML = data.menu;
                lightboxScreen.style.display = "flex"; 
            } else {
                alert(data.message || "Failed to fetch inventory item details.");
            }
        })
        .catch(error => console.error("Error fetching inventory details:", error));
}

function selectTarot(tarotId, shopType, value) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "showTarotPurchase", numeric_id: tarotId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            lightboxScreen.style.display = "flex";
        } else {
            alert(data.message || "Failed to fetch tarot details.");
        }
    })
    .catch(error => console.error("Tarot fetch error:", error));
}

function toggleInfuseCategory() {
    showGearInfuse = !showGearInfuse;
    onShop('Infuse', false);
}

function selectInfusion(categoryKey, recipeName) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "showInfusion", recipe: recipeName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            lightboxScreen.style.display = "flex";
        } else {
            alert(data.message || "Failed to load infusion.");
        }
    })
    .catch(error => console.error("Infusion fetch error:", error));
}

function executeInfusion(recipeName) {
    runInfusion("executeInfusion", recipeName);
}

function executeSacredInfusion(recipeName) {
    runInfusion("executeSacredInfusion", recipeName);
}

function runInfusion(action, recipeName) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action, recipe: recipeName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            animatePurchase(data.attempt_success);
        } else {
            alert(data.message || "Infusion failed.");
        }
    })
    .catch(error => console.error("Infusion error:", error))
    .finally(() => { blockingScreen.style.display = "none"; });
}


function handleTarotAction(action, tarotId) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action, numeric_id: tarotId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            if ('attempt_success' in data) {
                animatePurchase(data.attempt_success);
            }
        } else {
            alert(data.message || `${action} failed.`);
        }
    })
    .catch(error => console.error(`${action} error:`, error))
    .finally(() => { blockingScreen.style.display = "none"; });
}

// Refactored Usage:
function synthesizeTarot(tarotId) { handleTarotAction("synthesizeTarot", tarotId); }
function bindTarot(tarotId) { handleTarotAction("bindTarot", tarotId); }
function equipTarot(tarotId) { handleTarotAction("equipTarot", tarotId); }

function exchangeEssence(itemId) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "exchangeEssence", item_id: itemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            animatePurchase();
        } else {
            alert(data.message || "Exchange failed.");
        }
    })
    .catch(error => console.error("Exchange error:", error))
    .finally(() => { blockingScreen.style.display = "none";});
}

function purchaseShopItem(itemId, quantity = 1) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "purchaseShopItem", item_id: itemId, numeric_id: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lightboxDisplay.innerHTML = data.html;
            lightboxMenu.innerHTML = data.menu;
            animatePurchase();
        } else {
            alert(data.message || "Exchange failed.");
        }
    })
    .catch(error => console.error("Exchange error:", error))
    .finally(() => { blockingScreen.style.display = "none"; });
}

function animatePurchase(success=true) {
    animateType = "forge-failure";
    if (success) {
        animateType = "animate-success";
    }
    lightboxDisplay.classList.add(animateType);
    setTimeout(() => {
        lightboxDisplay.classList.remove(animateType);
    }, 1200);
}
