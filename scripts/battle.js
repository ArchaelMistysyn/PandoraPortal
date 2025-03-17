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

function triggerBattle(callType,) {
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
            battleScreen.style.backgroundImage = `url("${data.boss['boss_image']}")`; // Improve loading, and display cropped vers where needed.
            battleScreen.style.display = "flex";
            battleMenu.style.display = "none";
            runBoss(data.boss);
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
function runBoss(bossData) {/*
    battleInterval = setInterval(() => {
        fetch('./battle_handler.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "runCycle", boss_id: bossData.bossID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.boss_response);
                if (data.boss_dead) {
                    clearInterval(battleInterval);
                    alert("Boss defeated!");
                }
            } else {
                alert("Cycle failed: " + data.message);
            }
        });
    }, 61000);*/
}
