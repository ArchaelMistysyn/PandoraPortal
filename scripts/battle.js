const battleScreen = document.getElementById("battle-screen");
const battleScreenBg = document.getElementById("battle-screen-bg");
const battleCover = document.getElementById("battle-cover");
const battleMenu = document.getElementById("battle-menu");
const actionBox = document.getElementById("action-box");
const actionBoxName = document.getElementById("action-box-name");
const actionBoxValue = document.getElementById("action-box-value");
const actionBoxImage = document.getElementById("action-box-image");
const actionBoxMenu = document.getElementById("action-box-menu");
const magnitudeSlider = document.getElementById("magnitude-slider");

// Log elements
const battleDetailBox = document.getElementById("battle-detail-box");
const logBossHeader = document.getElementById("log-boss-header");
const logBossName = document.getElementById("log-boss-name");
const logBossHp = document.getElementById("log-boss-hp");
const logBossLvl = document.getElementById("log-boss-lvl");
const logBossDetails = document.getElementById("log-boss-details");
const logBossWeaknesses = document.getElementById("log-boss-weakness");
const logCycles = document.getElementById("log-cycles");
const logDps = document.getElementById("log-dps");
const logBossStatus = document.getElementById("log-boss-status");
const logPlayerHp = document.getElementById("log-player-hp");
const logPlayerRecovery = document.getElementById("log-player-recovery");
const logPlayerStatus = document.getElementById("log-player-status");
const logActions = document.getElementById("log-actions-section");

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

let battleTracker = { current: 0n, max: 0n, player_cHP: 0n, player_mHP: 0n , recovery: 0n, boss_status: ''};
function initializeBattleTracker(player, boss) {
    battleTracker = {
        max: boss.boss_mHP,
        current: boss.boss_mHP,
        player_cHP: player.player_mHP,
        player_mHP: player.player_mHP,
        recovery: player.recovery,
        boss_status: ''
    };
}
function initializeBattleDetails(boss) {
    battleScreen.style.display = "flex";
    battleDetailBox.classList.add("detail-box-tier-" + boss['boss_tier']);
}
function loadBossImage(boss){
    const img = new Image();
    img.onload = () => {
        battleScreenBg.style.backgroundImage = `url("${boss.boss_image}")`;
        battleScreenBg.classList.remove("bg-hidden");
    };
    img.src = boss.boss_image;
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
            loadBossImage(data.boss);
            logBossHp.innerText = numberConversion(data.boss['boss_cHP']) + ' / ' + numberConversion(data.boss['boss_mHP']);
            initializeBattleDetails(data.boss);
            battleDetailBox.style.display = "flex";
            battleMenu.style.display = "none";
            initializeBattleTracker(data.player, data.boss);
            logBossHp.innerText = numberConversion(battleTracker.current) + ' / ' + numberConversion(battleTracker.max);
            assignWeakness(data);
            updateBattleLog(data);
            runBoss(data);
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
let continue_status = true;
function runBoss(bossData) {
    setTimeout(() => {
        triggerCycle(bossData);
        battleInterval = setInterval(() => {
            if (check_abort()) { return; }
            triggerCycle(bossData);
        }, 60100);
        setTimeout(() => {
            if (check_abort()) { return; }
        }, 5000); 
    }, 5000);
}

function check_abort(){
    if (!continue_status) {
        continue_status = true;
        clearInterval(battleInterval);
        battleInterval = null;
        return true;
    }
    return false;
}

function triggerCycle(bossData) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "runCycle", numeric_id: bossData["encounter_id"] })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (!battleTracker) {
                loadBossImage(data.boss);
                initializeBattleDetails(data.boss);
                initializeBattleTracker(data.player, data.boss);
            }
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
    if (battleTracker.boss_status != '' && battleTracker.boss_status !== "Enraged") {
        // Stunned
        setTimeout(() => {
            animation_box('', '', `action-entry ${battleTracker.boss_status}`, battleTracker.boss_status.toUpperCase());
        }, bossInterval);
        battleTracker.boss_status = bossData.combat_tracker['boss_stun_status']?.trim();
    } else {
        // Boss Actions
        bossActions.forEach((row, index) => {
            setTimeout(() => {
                if (row.action_type =="boss_regen"){
                    battleTracker.current = BigInt(battleTracker.current) + BigInt(row.damage_value);
                    if(battleTracker.current >= battleTracker.max) {
                        battleTracker.current = battleTracker.max;
                    } else {
                        animation_box(`action-entry ${row.action_type}`, row.action_name, `action-entry ${row.action_type}`, numberConversion(row.damage_value));
                        updateBattleLog(bossData, row);
                    }
                } else {
                    animation_box(`action-entry ${row.action_type}`, row.action_name, `action-entry ${row.action_type}`, numberConversion(row.damage_value));
                    battleTracker.player_cHP = BigInt(battleTracker.player_cHP) - BigInt(row.damage_value);
                    if (battleTracker.player_cHP <= 0) {
                        battleTracker.player_cHP = row.new_hp;
                        battleScreenBg.classList.add("screen-grayscale");
                    }
                    updateBattleLog(bossData, row);
                }
            }, (1 + index) * bossInterval);
        });
    }
    // Run Player Actions after delay
    playerActions.forEach((row, index) => {
        setTimeout(() => {
            animation_box(`action-entry ${row.action_type}`, row.action_name, `action-entry ${row.action_type}`, numberConversion(row.damage_value));
            // Player regen
            if (row.action_type != "player_regen"){
                battleTracker.current = BigInt(battleTracker.current) - BigInt(row.damage_value);
                if (battleTracker.current <= 0) {
                    battleTracker.current = 0;
                    battleScreenBg.classList.add("screen-grayscale");
                }
            } else {
                battleTracker.player_cHP = BigInt(battleTracker.player_cHP) + BigInt(row.damage_value);
                if (battleTracker.player_cHP > battleTracker.player_mHP) {
                    battleTracker.player_cHP = battleTracker.player_mHP;
                }
            }
            updateBattleLog(bossData, row);
        }, 10000 + index * playerInterval);
    });
    setTimeout(() => {
        // update status for the next cycle.
        battleTracker.boss_status = bossData.combat_tracker['boss_stun_status'];
        if (bossData.battle_status !== 'continue' && (battleTracker.current <= 0 || bossData.combat_tracker['player_cHP'] <= 0)) {
            if (bossData.battle_status === "player_dead") {
                continue_status = false;
                battleDetailBox.style.display = "none";
                battle_menu_box('final-entry-title', 'red', bossData.boss['boss_name'], "Defeat!");
                return;
            } else if (bossData.battle_status === "boss_dead") {
                if (bossData.boss['mode'] === "Gauntlet" && bossData.boss['boss_tier'] < 6) {
                    battleTracker = null;
                    battleScreenBg.style.backgroundImage = "";
                    battleScreenBg.classList.add("bg-hidden");
                    battleScreenBg.classList.remove("screen-grayscale");
                } else {
                    continue_status = false;
                    battleDetailBox.style.display = "none";
                    battle_menu_box('final-entry-title', 'green', bossData.boss['boss_name'], "Victory!", bossData.reward_data);
                    showAchievements(bossData.achievement_data);
                    return;
                }
                
            }
        }
        continue_status = true;
        return;
    }, 55000);
}

function reset_boss(){
    battleScreenBg.style.backgroundImage = "";
    battleScreenBg.classList.remove("screen-grayscale");
    battleScreenBg.classList.add("bg-hidden");
    battleMenu.style.display = "flex";
    actionBox.style.display = "none";
    actionBox.style.backgroundColor = "";
    actionBox.style.border = "";
    actionBoxMenu.style.display = "none";
}

function battle_menu_box(title_class, button_class, menu_title, menu_text, reward = null){
    actionBox.style.backgroundColor = "rgba(0, 0, 0, 0.9)";
    actionBox.style.left = "50%";
    actionBox.style.top = "50%";
    actionBox.style.transform = "translate(-50%, -50%)";
    actionBoxName.className = title_class;
    actionBoxValue.className = 'battle-rewards';
    actionBoxName.innerText = menu_title;
    if (reward) {
        actionBox.style.border = "2px solid green";
        actionBoxValue.innerHTML = reward;
        actionBoxMenu.innerHTML = `<button class="lightbox-button-${button_class} lightbox-btn" onclick="reset_boss()">Claim</button>`;
    } else {
        actionBox.style.border = "2px solid red";
        actionBoxValue.innerHTML = "";
        actionBoxMenu.innerHTML = `<button class="lightbox-button-${button_class} lightbox-btn" onclick="reset_boss()">Retreat</button>`;
    }
    actionBoxMenu.style.display = "flex";
    actionBox.style.display = "flex";
}

function animation_box(name_class, name_text, value_class, value_text, position = "center") {
    let left_pos = "50%";
    let right_pos = "50%";
    let translate_pos = "translate(-50%, -50%)";
    if (position == "random") {
        const randX = Math.floor(Math.random() * 41) + 30;
        const randY = Math.floor(Math.random() * 41) + 30;
        left_pos = `${randX}%`;
        right_pos = `${randY}%`;
        translate_pos = "";
    }
    actionBox.style.left = left_pos;
    actionBox.style.top = right_pos;
    actionBox.style.transform = translate_pos;
    actionBoxName.className = name_class;
    actionBoxValue.className = value_class;
    actionBoxName.innerText = name_text; 
    actionBoxValue.innerText = value_text;
    actionBox.style.display = "flex";
    actionBox.classList.add("attack-hit");
    setTimeout(() => {
        actionBox.classList.add("fade-out");
    }, 1500);
    setTimeout(() => {
        actionBox.style.display = "none";
        actionBox.classList.remove("fade-out");
        actionBox.classList.remove("attack-hit");
    }, 1800);
}

function updateBattleLog(data, row = null) {
    if (row == null) {
        logActions.innerText = "";
    }
    // ADD HP BAR LATER
    // add magnitude & tier
    logBossName.innerText = data.boss['boss_name'];
    logBossLvl.innerText = " Lv" + data.boss['boss_level'];
    logBossDetails.innerText = "Danger Class: T" + data.boss['boss_tier'] + '-M' + data.boss['magnitude'];
    logBossHp.innerText = "HP: " + numberConversion(battleTracker.current) + ' / ' + numberConversion(battleTracker.max);
    logCycles.innerText = `Cycle Count: ${data.combat_tracker?.total_cycles ?? "0"}`;
    let total_dps = "0 / min";
    if (data.combat_tracker?.total_dps) {
        let roundedDps = BigInt(data.combat_tracker['total_dps']) / BigInt(data.combat_tracker['total_cycles']);
        total_dps = numberConversion(roundedDps.toString()) + " / min";
    }
    logDps.innerText = `Cyclic DPS: ${total_dps}`;
    let newStatus = `Boss Status: ${battleTracker.boss_status?.trim() || "Stable"}`;
    if (data.boss['boss_type_num'] >= 2 && battleTracker.current <= battleTracker.max / 2) {
        newStatus = "Boss Status: Enraged";
    }
    logBossStatus.innerText = newStatus;
    logPlayerHp.innerText = `Player HP: ${numberConversion(battleTracker.player_cHP)} / ${numberConversion(battleTracker.player_mHP)}`;
    logPlayerRecovery.innerText = `Recovery: ${battleTracker.recovery}`;
    logPlayerStatus.innerText = `Player Status: ${data.combat_tracker?.stun_status?.trim() || "Stable"}`;
    if (row !== null) {
        logActions.innerHTML += "<div class='log-row'>" + row.action_name + ' ' + numberConversion(row.damage_value) + " " + row.triggers + "</div>";
    }
}

function assignWeakness(data){
    logBossWeaknesses.innerHTML = "";
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

