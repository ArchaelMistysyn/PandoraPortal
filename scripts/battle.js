const battleLabels = {
    "Any": "Random", "Fortress": "Fortress", "Dragon": "Dragon", "Demon": "Demon",
    "Paragon": "Paragon (T1-4)", "Summon1": "Paragon (T5)", "Summon2": "Paragon (T6)", "Arbiter": "Arbiter (T1-6)",
    "Summon3": "Arbiter (T7)", "Arena": "Arena (PvP)", "Palace1": "Palace (Challenger)", "Palace2": "Palace (Usurper)",
    "Palace3": "Palace (Samsara)", "Gauntlet": "Gauntlet (Boss Rush)", "Ruler": "Ruler (Raid Boss)"
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
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "player"})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // TODO
        } else {
            alert(data.message || "Failed to load player data.");
        }
    })
    .catch(error => console.error('Error:', error));
}

function triggerBattle(callType) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "runBoss", boss_calltype: callType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // TODO
        } else {
            alert(data.message || "Failed to start battle.");
        }
    })
    .catch(error => console.error('Error:', error));
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

