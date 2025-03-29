const battleScreen = document.getElementById("battle-screen");
const battleMenu = document.getElementById("battle-menu");
const actionBox = document.getElementById("action-box");
const actionBoxName = document.getElementById("action-box-name");
const actionBoxValue = document.getElementById("action-box-value");
const actionBoxImage = document.getElementById("action-box-image");
const magnitudeSlider = document.getElementById("magnitude-slider");

// Log elements
const battleDetailBox = document.getElementById("battle-detail-box");
const logBossHeader = document.getElementById("log-boss-header");
const logBossName = document.getElementById("log-boss-name");
const logBossHp = document.getElementById("log-boss-hp");
const logBossLvl = document.getElementById("log-boss-lvl");
const logBossWeaknesses = document.getElementById("log-boss-weakness");
const logCycles = document.getElementById("log-cycles");
const logDps = document.getElementById("log-dps");
const logBossStatus = document.getElementById("log-boss-status");
const logBossActions = document.getElementById("log-boss-actions");
const logPlayerHp = document.getElementById("log-player-hp");
const logPlayerRecovery = document.getElementById("log-player-recovery");
const logPlayerStatus = document.getElementById("log-player-status");
const logPlayerActions = document.getElementById("log-player-actions");

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

let battleHP = { current: 0n, max: 0n, player_cHP: 0n, player_mHP: 0n };
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
            let currentHP = numberConversion(data.boss['boss_cHP']);
            let maxHP = numberConversion(data.boss['boss_mHP']);
            logBossHp.innerText = currentHP + ' / ' + maxHP;
            battleScreen.style.display = "flex";
            battleMenu.style.display = "none";
            battleHP.max = numberConversion(data.boss['boss_mHP']);
            battleHP.current = data.boss['boss_mHP'];
            battleHP.player_cHP = data.player['player_mHP'];
            battleHP.player_mHP = data.player['player_mHP'];
            logBossHp.innerText = numberConversion(battleHP.current) + ' / ' + battleHP.max;
            assignWeakness(data);
            updateBattleLog(data);
            setTimeout(() => {
                runBoss(data);
            }, 5000);
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
function runBoss(bossData) {
    triggerCycle(bossData);
    battleInterval = setInterval(() => {
        let continue_status = triggerCycle(bossData);
        if (!continue_status) {
            clearInterval(battleInterval);
            battleInterval = null;
        }
    }, 61000);
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
           return animateCycleActions(data);
        } else {
            alert("Cycle failed: " + data.message);
            return false;
        }
    });
}

function animateCycleActions(bossData) {
    const bossActions = bossData.cycle_data.filter(row => row.action_type.includes("boss"));
    const playerActions = bossData.cycle_data.filter(row => !row.action_type.includes("boss"));
    const bossInterval = 3000;
    const playerInterval = 3000;
    updateBattleLog(bossData);
    // Run Boss Actions
    bossActions.forEach((row, index) => {
        setTimeout(() => {
            actionBox.style.display = "none";
            actionBox.style.left = "50%";
            actionBox.style.top = "50%";
            actionBox.style.transform = "translate(-50%, -50%)";
            actionBoxName.className = `action-entry ${row.action_type}`;
            actionBoxValue.className = `action-entry ${row.action_type}`;
            actionBoxName.innerText = row.action_name; 
            actionBoxValue.innerText = numberConversion(row.damage_value);
            actionBox.style.display = "flex";
            if (row.action_type.includes("regen")){
                battleHP.current += row.damage_value;
                if(battleHP.current >= battleHP.max) {
                    battleHP.current = battleHP.max;
                }
            } else {
                battleHP.player_cHP -= row.damage_value;
                if (battleHP.player_cHP <= 0) {
                    // add proper recovery and immortality checks here.
                    battleHP.player_cHP = 0;
                }
            }
            updateBattleLog(bossData, row, true);
        }, 2000 + index * bossInterval);
    });
    // Run Player Actions after delay
    playerActions.forEach((row, index) => {
        setTimeout(() => {
            actionBox.style.display = "none";
            //const randX = Math.floor(Math.random() * 41) + 30;
            //const randY = Math.floor(Math.random() * 41) + 30;
            //actionBox.style.left = `${randX}%`;
            //actionBox.style.top = `${randY}%`;
            actionBox.style.left = "50%";
            actionBox.style.top = "50%";
            actionBox.style.transform = "translate(-50%, -50%)";
            actionBoxName.className = `action-entry ${row.action_type}`;
            actionBoxValue.className = `action-entry ${row.action_type}`;
            actionBoxName.innerText = row.action_name; 
            actionBoxValue.innerText = numberConversion(row.damage_value);
            actionBox.style.display = "flex";
            battleHP.current = (BigInt(battleHP.current) - BigInt(row.damage_value));
            updateBattleLog(bossData, row, false);
        }, 12000 + index * playerInterval);
    });

    // Handle Death fix later.
    if (bossData.battle_status !== 'continue' && (battleHP.current <= 0 || bossData.combat_tracker['player_cHP'] <= 0)) {
        if (bossData.battle_status === "player_dead") {
            alert("Slain!");
            // display death screen or popup?
            // reset screen
            return false;
        } else if (bossData.battle_status === "boss_dead") {
            alert("Boss defeated!");
            // display cycle animation until boss dead
            // reward popup
            // reset screen
            return false;
        } else{
            // display cycle animation
            return true;
        }
    }
}

function updateBattleLog(data, row = null, bossAction = false) {
    if (row == null) {
        logBossActions.innerText = "";
        logPlayerActions.innerText = "";
    }
    // add magnitude?
    logBossName.innerText = data.boss['boss_name'];
    logBossLvl.innerText = " Lv" + data.boss['boss_level'];
    logBossHp.innerText = "HP: " + numberConversion(battleHP.current) + ' / ' + battleHP.max;
    logCycles.innerText = `Cycle Count: ${data.combat_tracker?.total_cycles ?? "0"}`;
    let total_dps = 0;
    if (data.combat_tracker?.total_dps) {
        total_dps = numberConversion(data.combat_tracker['total_dps'] / data.combat_tracker['total_cycles']) + " / min";
    }
    logDps.innerText = `Cyclic DPS: ${total_dps}`;
    let newStatus = `Boss Status: ${data.combat_tracker?.boss_stun_status ?? "---"}`;
    if (data.boss['boss_type_num'] >= 2 && battleHP.current <= battleHP.max / 2) {
        newStatus = "Boss Status: Enraged";
    }
    logBossStatus.innerText = newStatus;
    if (row !== null && bossAction) {
        logBossActions.innerText += row.action_name + ' ' + row.damage_value;
    }
    logPlayerHp.innerText = `Player HP: ${numberConversion(battleHP.player_cHP)} / ${numberConversion(battleHP.player_mHP)}`;
    logPlayerRecovery.innerText = `Recovery: ${data.combat_tracker?.total_cycles ?? "0"}`;
    logPlayerStatus.innerText = `Player Status: ${data.combat_tracker?.stun_status ?? "---"}`;
    if (row !== null && !bossAction) {
        logPlayerActions.innerHTML += "<div>" + row.action_name + ' ' + numberConversion(row.damage_value) + "</div>";
    }
}

function assignWeakness(data){
    logBossWeaknesses.innerHTML = "Weakness: ";
    // Type (class-based) weaknesses
    const typeNames = ["Knight", "Ranger", "Assassin", "Mage", "Weaver", "Rider", "Summoner"];
    data.boss?.boss_typeweak?.forEach((val, i) => {
        if (val === 1) {
            logBossWeaknesses.innerHTML += `<img src="./gallery/Icons/Classes/${typeNames[i]}.webp" class="icon-small" alt="${typeNames[i]}"> `;
        }
    });
    // Elemental weaknesses
    const elementNames = ["Fire", "Water", "Lightning", "Earth", "Wind", "Ice", "Shadow", "Light", "Celestial"];
    data.boss?.boss_eleweak?.forEach((val, i) => {
        if (val === 1) {
            logBossWeaknesses.innerHTML += `<img src="./gallery/Icons/Elements/${elementNames[i]}.webp" class="icon-small" alt="${elementNames[i]}"> `;
        }
    });    
}

