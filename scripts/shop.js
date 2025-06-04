const shopScreen = document.getElementById("shop-screen");
const tarotScreen = document.getElementById("tarot-screen");
const cathedralScreen = document.getElementById("cathedral-screen");

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
    ]
};

function onShop(shopType, reload=true) {
    if (reload) {
        clearScreens();
    }
    swapShopScreen(shopType);
    const options = shopOptions[shopType] || [];
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
    const screens = {Market: '#shop-screen', Tarot: '#tarot-screen', Cathedral: '#cathedral-screen'};
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
