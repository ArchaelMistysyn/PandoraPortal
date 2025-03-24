const battleScreen = document.getElementById("battle-screen");
const battleMenu = document.getElementById("battle-menu");
const actionBox = document.getElementById("action-box");
const actionBoxName = document.getElementById("action-box-name");
const actionBoxValue = document.getElementById("action-box-value");
const battleDetailBox = document.getElementById("battle-detail-box");
const battleBossName = document.getElementById("battle-boss-name");
const battleBossHp = document.getElementById("battle-boss-hp");
const battleBossLog = document.getElementById("battle-boss-log");
const magnitudeSlider = document.getElementById("magnitude-slider");

const soloTypes = ["Any", "Fortress", "Dragon", "Demon", "Paragon", "Arbiter"];
const battleLabels = {
    "Any": "Random", "Fortress": "Fortress", "Dragon": "Dragon", "Demon": "Demon",
    "Paragon": "Paragon (T1-4)", "Summon1": "Paragon (T5)", "Summon2": "Paragon (T6)", "Arbiter": "Arbiter (T1-6)",
    "Summon3": "Arbiter (T7)", "Arena": "Arena (PvP)", "Palace1": "Palace (Challenger)", "Palace2": "Palace (Usurper)",
    "Palace3": "Palace (Samsara)", "Gauntlet": "Gauntlet (Boss Rush)", "Ruler": "Ruler (Raid Boss)"
};
const battleItemCost = {
    "Fortress": "Stone1", "Dragon": "Stone2", "Demon": "Stone3", "Paragon": "Stone4", 
    "Summon1": "Summon1", "Summon2": "Summon2", "Arbiter": "Stone6", "Summon3": "Summon3",
    "Palace1": "Lotus10", "Palace2": "Lotus10", "Palace3": "Lotus10", 
    "Gauntlet": "Compass"
};

function onBattle() {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "player" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            unlockButtons(data.player);
            battleContainer.style.display = "flex";
        } else {
            alert(data.message || "Failed to load player data.");
        }
    })
    .catch(error => console.error('Error:', error));
}

function confirmBattle(callType) {
    let canConfirm = true;
    let costDisplay = null;
    let weaponEquipped = false;
    let menuHTML = '';
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "inventory"})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            weaponEquipped = data.player['player_equipped'][0];
            costDisplay = '';
            if (soloTypes.includes(callType)) {
                const currentStamina = parseInt(data.player['player_stamina']);
                if (currentStamina < 200) canConfirm = false;
                costDisplay += `
                <div class="cost-row">
                    <img src="https://pandoraportal.ca/gallery/Icons/Misc/Stamina.webp" alt="Stamina" class="cost-icon">
                    <span class="cost-name">Stamina</span>
                    <span class="cost-quantity">${currentStamina} / 200</span>
                </div>`;
            }    
            if (callType in battleItemCost) {
                const itemID = battleItemCost[callType];
                const itemName = itemData[itemID]['name'];
                const itemObj = data.items.find(item => item.item_id === itemID);
                const userStock = itemObj.item_qty;
                const itemIcon = itemData[itemID]['image_link'];
                if (userStock < 1) canConfirm = false;
                costDisplay += `
                <div class="cost-row">
                    <img src="${itemIcon}" alt="${itemName}" class="cost-icon">
                    <span class="cost-name">${itemName}</span>
                    <span class="cost-quantity">${userStock} / 1</span>
                </div>`;
            }    
        } else {
            alert(data.message || "Failed to load player data.");
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        if (costDisplay == null) {
            lightboxDisplay.innerHTML = `<div class="lightbox-red lightbox-extra-small">Something went wrong.</div>`;
        } else if (weaponEquipped == 0) {
            lightboxDisplay.innerHTML = `<div class="lightbox-red lightbox-extra-small">Weapon must be equipped.</div>`;
        } else {
            let lightboxColour = "red";
            if (canConfirm) {
                menuHTML = `<button class="lightbox-button-green" onclick="triggerBattle('${callType}')">Confirm</button>`;
                lightboxColour = "green";
            }
            let headerMessage = '<div class="lightbox-header highlight-text">Encounter Cost<div class="style-line"></div></div>';
            lightboxDisplay.innerHTML = `<div class="lightbox-${lightboxColour} lightbox-small">${headerMessage}${costDisplay}</div>`;;
        }
        lightboxMenu.innerHTML = menuHTML + `<button class="lightbox-button-gray" onclick="closeLightbox()"><span class="symbol-height">✖</span> Close</button>`;
        lightboxScreen.style.display = "flex";
    });
}

function triggerBattle(callType) {
    const magnitude = parseInt(magnitudeSlider.value);
    blockingScreen.style.display = "flex";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "runBoss", boss_calltype: callType, magnitude: magnitude })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            battleScreen.style.backgroundImage = `url("${data.boss_image}")`; // Improve loading, and display cropped vers where needed.
            battleBossName.innerText = data.boss['boss_name'];
            let currentHP = numberConversion(data.boss['boss_cHP']);
            let maxHP = numberConversion(data.boss['boss_mHP']);
            battleBossHp.innerText = currentHP + ' / ' + maxHP;
            battleScreen.style.display = "flex";
            battleMenu.style.display = "none";
            setTimeout(() => {
                runBoss(data);
            }, 20000);
        } else {
            alert(data.message || "Failed to start battle.");
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        lightboxScreen.style.display = "none";
        blockingScreen.style.display = "none";
    });
}

// Button Menus
let currentMenu = "Solo Encounter";

function battleToggle() {
    let soloButtons = document.querySelectorAll(".battle-button-solo");
    let specialButtons = document.querySelectorAll(".battle-button-special");
    let toggleButton = document.getElementById('battle-menu-toggle');
    if (currentMenu === "Solo Encounter") {
        soloButtons.forEach(btn => btn.style.display = "none");
        specialButtons.forEach(btn => btn.style.display = "inline-block");
        currentMenu = "Special Modes"; 
    } else {
        specialButtons.forEach(btn => btn.style.display = "none");
        soloButtons.forEach(btn => btn.style.display = "inline-block");
        currentMenu = "Solo Encounter";
    }
    toggleButton.innerHTML = currentMenu;
}

function unlockButtons(playerData) {
    document.querySelectorAll(".battle-button-locked").forEach(button => {
        if (playerData['player_equipped'][0] == 0) {
            return;
        }
        let requiredEchelon = button.dataset.echelon ? parseInt(button.dataset.echelon) : null;
        let requiredQuest = button.dataset.quest ? parseInt(button.dataset.quest) : null;
        let requiredLevel = button.dataset.level ? parseInt(button.dataset.level) : null;
        if ((requiredEchelon !== null && playerData['player_echelon'] >= requiredEchelon) || 
        (requiredQuest !== null && playerData['player_quest'] >= requiredQuest)  || 
        (requiredLevel !== null && playerData['player_level'] >= requiredLevel)) {
            button.classList.remove("battle-button-locked");
            button.classList.add("battle-button-blue");
            button.setAttribute("onclick", `confirmBattle('${button.dataset.type}')`);
            button.innerHTML = battleLabels[button.dataset.type];
        }
    });
}

let battleInterval = null;
let battleHP = { current: 0n, max: 0n };
function runBoss(bossData) {
    battleHP.max = numberConversion(bossData.boss['boss_mHP']);
    battleHP.current = bossData.boss['boss_mHP'];
    battleBossHp.innerText = numberConversion(battleHP.current) + ' / ' + battleHP.max;
    triggerCycle(bossData);
    battleInterval = setInterval(() => {
        triggerCycle(bossData);
    }, 60000);
}

function triggerCycle(bossData) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "runCycle", encounter_id: bossData["encounter_id"] })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            animateCycleActions(data);
        } else {
            alert("Cycle failed: " + data.message);
        }
    });
}

function animateCycleActions(bossData) {
    const bossActions = bossData.cycle_data.filter(row => row.action_type.includes("boss"));
    const playerActions = bossData.cycle_data.filter(row => !row.action_type.includes("boss"));

    const bossInterval = 2000;
    const playerInterval = 2000;
    // Run Boss Actions
    bossActions.forEach((row, index) => {
        setTimeout(() => {
            actionBoxName.className = `action-entry ${row.action_type}`;
            actionBoxValue.className = `action-entry ${row.action_type}`;
            actionBoxName.innerText = row.action_name; 
            actionBoxValue.innerText = numberConversion(row.damage_value);
            // battleHP.current = (BigInt(battleHP.current) - BigInt(row.damage_value)); handle regen and player damage here later.
            battleBossHp.innerText = numberConversion(battleHP.current) + ' / ' + battleHP.max;
        }, 2000 + index * bossInterval);
    });
    // Run Player Actions after delay
    playerActions.forEach((row, index) => {
        setTimeout(() => {
            actionBoxName.className = `action-entry ${row.action_type}`;
            actionBoxValue.className = `action-entry ${row.action_type}`;
            actionBoxName.innerText = row.action_name; 
            actionBoxValue.innerText = numberConversion(row.damage_value);
            battleHP.current = (BigInt(battleHP.current) - BigInt(row.damage_value));
            battleBossHp.innerText = numberConversion(battleHP.current) + ' / ' + battleHP.max;
        }, 12000 + index * playerInterval);
    });

    // Handle Death fix later.
    if (bossData.battleStatus != 'continue' && (battleHP.current <= 0 || battleHP.current <= 0)) { // fix player condition later
        if (bossData.battle_status === "player_dead") {
            clearInterval(battleInterval);
            alert("Slain!");
            // display death screen or popup?
            // reset screen
        } else if (bossData.battle_status === "boss_dead") {
            clearInterval(battleInterval);
            alert("Boss defeated!");
            // display cycle animation until boss dead
            // reward popup
            // reset screen
        } else{
            // display cycle animation
        }
    }
}

