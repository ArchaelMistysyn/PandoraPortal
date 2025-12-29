const travelMenu = document.getElementById("travel-menu");
const travelSubmenu = document.getElementById("travel-submenu");
const travelScreenA = document.getElementById("travel-screen-a");
const travelScreenB = document.getElementById("travel-screen-b");
const roomImage = document.getElementById("room-image");
const roomMenu = document.getElementById("room-menu");
let galleryURL = "https://PandoraPortal.ca/gallery/";

const travelUnlocks = {
    exploration: { threshold: 1, label: "Exploration", image: galleryURL + "Displays/Locations/Map.webp"},
    mortal: { threshold: 1, label: "Mortal Plane", image: "" },
    celestial: { threshold: 1, label: "Celestial Plane", image: "" },
    divine: { threshold: 10, label: "Divine Plane", image: "" },
    abyss: { threshold: 38, label: "Abyssal Plane", image: galleryURL + "Displays/Banners/Abyss.webp" }
};

let zone_msg = {};
zone_msg['abyss'] = "Within the abyssal plane resides the Deep Void. The taint of the void can only be purified through a more powerful darkness.";
zone_msg['abyss'] += "\n  Take great caution, dive too deep and nothing can pull you back out.";
zone_msg['monument1'] = "The immaculate monument resonates with life and hope.\n  The unfamiliar markings grant you new perspective and understanding.";
zone_msg['monument2'] = "The abstract monument resonates with passion and freedom.\n The ambiguous markings grant you new perspective and understanding.";
zone_msg['monument3'] = "The pristine monument resonates with power and control.\n  The elegant markings grant you new perspective and understanding.";
zone_msg['monument4'] = "The ominous monument resonates with death and despair.\n  The sinister markings grant you the last of their dark wisdom.";
zone_msg['monument5'] = "The exalted monument emanates with an overpowering and devouring presence.\n  The resplendent markings recognize your strength and grant you sacred authority.";
zone_msg['fleur'] = "Have you come to desecrate my holy gardens once more? Well, I suppose it no longer matters, ";
zone_msg['fleur'] += "I know you will inevitably find what you desire even without the guidance of a lowly echo.";
zone_msg['fleur'] += "\n  If you intend to sever the divine lotus, then I suppose the rest are nothing but pretty flowers.";
zone_msg['yubelle'] = "You would still follow Pandora's path in her place? Very well, I am no longer in a position to object.";
zone_msg['yubelle'] += "I suppose, even as an echo, such things do indeed fall within my purview.";

const travelSubmenus = {
    // visitation checks required
    exploration: [
        { label: "Map Select", threshold: 5, trigger: () => onMap("Default") },
        { label: "Unreleased", threshold: 99/*threshold: 21, trigger: () => onMap("Auto")*/ }, // Automap option - not for beta release
        { label: "Unreleased", threshold: 99 } // Manifest option - not for beta release
    ],
    mortal: [
        { label: "Refinery", image: galleryURL + "Displays/Locations/Refinery.webp", trigger: () => onRefine() },
        { label: "Alchemist", image:  galleryURL + "Displays/Locations/Alchemist Shop.webp", trigger: () => onShop("Infuse") },
        { label: "Market", image: "", trigger: () => onShop("Market") },
        { label: "Bazaar", image: "" },
        { label: "Monument", level: 15, monument_id: 1, trigger: (e) => checkMonument(1, galleryURL + "Displays/Locations/Monument of Beginnings.webp", e.target) },
        { label: "Fishing" }
    ],
    celestial: [
        { label: "Forge", threshold: 1, image: galleryURL + "Displays/Locations/Celstial Forge.webp", trigger: () => onForge()},
        { label: "Planetarium", threshold: 1, image: galleryURL + "Displays/Locations/Planetarium.webp", trigger: () => onShop("Tarot") },
        { label: "Thana", threshold: 1, message: "", image: galleryURL + "Tarot/Paragon/XIII - Thana, The Death.webp" }, // varies by visit count
        { label: "Monument", threshold: 30, monument_id: 2, trigger: (e) => checkMonument(2, galleryURL + "Displays/Locations/Monument of Journeys.webp", e.target) }
    ],
    divine: [
        { label: "Mysmir", threshold: 10, message: "Bring me enough tokens and even you can be rewritten.", 
            image: galleryURL + "Tarot/Arbiter/XII - Mysmir, The Changeling.webp" },
        { label: "Avalon", threshold: 1, message: "The farther you walk your path, the harder it is to change what you've become", image: "" },
        { label: "Isolde", threshold: 20, message: "You've come a long way from home child. Tell me, what kind of power do you seek?", image: galleryURL + "Tarot/Arbiter/XXIV - Isolde, The Soulweaver.webp" },
        { label: "Kazyth", threshold: 42, message: "", image: galleryURL + "Tarot/Arbiter/XXVI - Kazyth, The Lifeblood.webp" },
        { label: "Vexia", threshold: 46, message: "We need not turn you away, mortal. \nThe oracle has already foretold your failure. Now it need only be written into truth.", 
            image: "" },
        { label: "Fleur", threshold: 48, message: zone_msg['fleur'], image: galleryURL + "Displays/Locations/Sanctuary.webp" },
        { label: "Yubelle", threshold: 51, message: zone_msg['yubelle'], image: galleryURL + "Displays/Locations/Cathedral.webp", trigger: () => onShop("Cathedral") },
        { label: "Monument", level: 45, monument_id: 3, trigger: (e) => checkMonument(3, galleryURL + "Displays/Locations/Monument of Providence.webp", e.target) }
    ],
    abyss: [
        { label: "Deep Void", threshold: 38, image: galleryURL + "Displays/Locations/Abyssal Plane.webp",message: zone_msg['abyss'], trigger: () => onForge('W', true)},
        { label: "Eleuia", threshold: 47, message: "", image: "" }, // Varies by visit count
        { label: "Monument", level: 60, monument_id: 4, trigger: (e) => checkMonument(4, galleryURL + "Displays/Locations/Monument of Endings.webp", e.target) },
        { label: "☆ Monument", level: 60, monument_id: 5, trigger: (e) => checkMonument(5, galleryURL + "Displays/Locations/Monument of Apotheosis.webp", e.target) }
    ]
};

function onTravel(plane = null) {
    travelSubmenu.innerHTML = ""
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "playerExtra" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            roomImage.style.aspectRatio = '';
            travelContainer.style.display = "flex";
            travelScreenA.style.display = "none";
            if (plane) {
                updateTravelSubmenu(plane, data.player, data.player_gear_score);
            } else {
                updateTravelMenu(data.player);
            }
        } else {
            alert(data.message || "Failed to load.");
        }
    })
    .catch(error => console.error('Error loading:', error));
}

function updateTravelMenu(playerData) {
    travelMenu.style.display = "flex";
    travelSubmenu.style.display = "none";
    travelScreenB.style.display = "flex";
    travelScreenB.style.backgroundImage = `url("https://PandoraPortal.ca/gallery/Displays/Locations/Map.webp")`;
    const travelButtons = document.querySelectorAll("#travel-menu .travel-button");
    travelButtons.forEach(button => {
        const plane = button.getAttribute("data-plane");
        const unlockData = travelUnlocks[plane];
        if (playerData.player_quest >= (unlockData.threshold)) {
            button.classList.remove("lightbox-button-gray");
            button.classList.add("lightbox-button-blue");
            button.innerText = unlockData.label;
            button.onclick = () => onTravel(plane);
        }
    });
}

function updateTravelSubmenu(plane, playerData, player_gear_score) {
    const planeImage = travelSubmenus[plane]?.image || "";
    const planeMessage = travelSubmenus[plane]?.message || "";
    travelSubmenu.innerHTML = "";
    travelMenu.style.display = "none";
    travelScreenB.style.display = "none";
    travelSubmenu.style.display = "flex";            
    if (planeMessage != "") {
        travelScreenA.style.display = "flex";
        roomMenu.innerText = planeMessage;
        roomImage.style.backgroundImage = `url('${planeImage}')`;
    } else {
        travelScreenB.style.display = "flex";
        travelScreenB.style.backgroundImage = `url('${planeImage}')`;
    }
    let monumentData = playerData.misc_data['monument_data'].split(';');
    // Updating Buttons
    travelSubmenus[plane]?.forEach(option => {
        const btn = document.createElement("button");
        let labelText = option.label;
        let lockText = "Locked";
        let isUnlocked = true;
        if (option.monument_id) {
            isUnlocked = monumentData[option.monument_id - 1] !== "1";
            if (option.monument_id === 5) {
                if (player_gear_score < 99999){
                    isUnlocked = false; 
                }
                labelText = lockText = "☆ " + player_gear_score.toLocaleString();
            }
        }
        isUnlocked = isUnlocked && (playerData.player_quest >= (option.threshold ?? 0)) && (playerData.player_level >= (option.level ?? 0));
        if (isUnlocked) {
            btn.className = "lightbox-button-blue";
            btn.innerText = labelText;
            if(option.trigger){
                btn.onclick = option.trigger;
            }
        } else {
            btn.className = "lightbox-button-gray";
            btn.innerText = lockText;
        }
        travelSubmenu.appendChild(btn);
    });
    const backBtn = document.createElement("button");
    backBtn.className = "lightbox-button-red";
    backBtn.innerText = "← Back";
    backBtn.onclick = () => onTravel();
    travelSubmenu.appendChild(backBtn);
}

function checkMonument(id, imageLink, btn) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "checkMonument", numeric_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.onclick = null;
            btn.className = "lightbox-button-gray";
            btn.innerText = "Claimed";
            travelScreenA.style.display = "flex";
            travelScreenB.style.display = "none";
            roomMenu.innerHTML = "<div class='highlight-text quest-title'>" + data.title + "</div><div class='style-line'></div><div>" + zone_msg["monument" + id] + "</div>";
            roomImage.style.backgroundImage = `url('${imageLink}')`;
            roomImage.style.aspectRatio = '1 / 1';
            lightboxDisplay.innerHTML = '<div class="item-displaybox">' + data.reward_html + '</div>';
            lightboxMenu.innerHTML = `<button class="lightbox-button-gray" onclick="closeLightbox()"><span class="symbol-height">✖</span> Close</button>`;
            lightboxScreen.style.display = "flex";
            showAchievements(data.achievement_data);  
        } else {
            alert(data.message || "Unable to access monument.");
        }
    })
    .catch(error => console.error("Monument check error:", error))
    .finally(() => {
        blockingScreen.style.display = "none";
    });
}



