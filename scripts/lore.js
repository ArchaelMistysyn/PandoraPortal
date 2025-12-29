const storyUnlocks = {
    story1: { threshold: 26, label: "Story Act 1", header: "Story Act 1: Mortal Journey" },
    story2: { threshold: 46, label: "Story Act 2", header: "Story Act 2: Through the Stars" },
    story3: { threshold: 53, label: "Story Act 3", header: "Story Act 3: Overthrow the Divine" },
    story4: { threshold: 55, label: "Story Act 4", header: "" },
    prequel1: { threshold: 1, label: "Prequel Act 1", header: "Prequel Act 1: Tiamat's Treachery" },
    prequel2: { threshold: 1, label: "Prequel Act 2", header: "Prequel Act 2: Shattered Souls" },
    prequel3: { threshold: 46, label: "Prequel Act 3", header: "Prequel Act 3: Alaric's Regret" },
    prequel4: { threshold: 47, label: "Prequel Act 4", header: "Prequel Act 4: The Paragon War" }
};
const endingContent = {0: "Pandora's Ending: You Are You", 1: "Thana's Ending: An Eternal Love", 2: "Eleuia's Ending: Cry No More"};


function onLore(storyKey = null) {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "player" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateLoreButtons(data.player);
            if (storyKey !== null) {
                document.querySelectorAll(".lore-button").forEach(button => {
                    button.classList.remove("active");
                });
                document.querySelector(`[data-value="${storyKey}"]`).classList.add("active");
                let readHTML = "<button id='lore-toggle' onclick='ReadToggle()'>Read Mode</button>";
                let storyHeader = storyUnlocks[storyKey]['header'];
                if (storyKey === "story4" && data.player['player_oath_num'] >= 0) {
                    storyHeader = endingContent[data.player['player_oath_num']];
                }
                readHTML += "<div id='lore-header' class='highlight-text'>" + storyHeader + "</div>";
                readHTML += storyData[storyKey];
                loreScreen.innerHTML = readHTML;
                loreToggle();
            }
            loreContainer.style.display = 'flex';
        } else {
            alert(data.message || "Failed to load player data.");
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateLoreButtons(playerData) {
    const loreButtons = document.querySelectorAll("#lore-menu .lore-button-locked");  
    loreButtons.forEach(button => {
        let loreKey = button.getAttribute("data-value");
        let unlockButton = false;
        if (playerData.player_quest >= storyUnlocks[loreKey]['threshold']) {
            switch (loreKey) {
                case "story4":
                    if (playerData.player_oath_num >= 0) { unlockButton = true; }
                    break;
                case "prequel1":
                    if (playerData.misc_data['thana_visits'] > 0) { unlockButton = true; }
                    break;
                case "prequel4":
                    if (playerData.misc_data['eleuia_visits'] > 0) { unlockButton = true; }
                    break;
                default: 
                    unlockButton = true;
            }
        }
        if (unlockButton) {
            button.classList.remove("lore-button-locked");
            button.classList.add("lore-button-blue");
            button.innerText = storyUnlocks[loreKey]['label'];
            button.onclick = () => onLore(loreKey);
        }
    });
}

function ReadToggle() {
    let loreHeader = document.getElementById('lore-header');
    if (loreScreen.style.background == 'white') {
        loreScreen.style.background = '#111';
        loreScreen.style.color = 'white';
        loreScreen.style.fontFamily = 'inherit';
        loreHeader.style.color = 'var(--brand-color-primary)';
        loreHeader.style.textShadow = '2px 2px var(--brand-color-secondary)';
    } else {
        loreScreen.style.background = 'white';
        loreScreen.style.color = 'black';
        loreScreen.style.fontFamily = 'Times New Roman, Times, serif';
        loreHeader.style.color = 'black';
        loreHeader.style.textShadow = 'None';
    }
}

function loreToggle() {
    let loreToggle = document.getElementById('lore-menu-toggle');
    const loreButtons = document.querySelectorAll("#lore-menu .lore-button-locked, #lore-menu .lore-button-blue");
    if (loreToggle.innerHTML === "Hide") {
        loreToggle.innerHTML = "▸";
        loreToggle.style.fontSize = '2rem';
        loreButtons.forEach(button => button.style.display = 'none');
        loreMenu.style.width = '50px';
        loreToggle.style.padding = '0px';
    } else {
        loreToggle.innerHTML = "Hide";
        loreButtons.forEach(button => button.style.display = 'block');
        loreMenu.style.width = '250px';
        loreToggle.style.fontSize = '1.2rem';
        loreToggle.style.padding = '10px';
    }
}
