const map_tier_dict = {
                "Ancient Ruins": 1, "Spatial Dungeon": 2, "Celestial Labyrinth": 3,
                "Starlit Grotto": 4, "Void Temple": 5, "Citadel of Miracles": 6,
                "Abyssal Sanctum": 7, "Divine Ziggurat": 8, "Cradle of Samsara": 9,
                "Rift of the Chaos God": 10
            };

function onMap(mapType = "Default") {
    travelSubmenu.innerHTML = "";
    clearScreens();

    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "player" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const unlockedTier = parseInt(data.player.player_echelon) + 1;
 
            travelMenu.style.display = "none";
            travelScreenA.style.display = "none";
            travelScreenB.style.display = "flex";
            travelScreenB.style.backgroundImage = `url('${galleryURL}Displays/Locations/Map.webp')`;
            travelSubmenu.style.display = "flex";
            travelContainer.style.display = "flex"

            Object.entries(map_tier_dict).forEach(([name, tier]) => {
                const btn = document.createElement("button");
                const isUnlocked = tier <= unlockedTier;
                btn.className = "lightbox-button-gray";
                btn.innerText = "Locked";
                if (isUnlocked) {
                    stamina_cost = tier * 50 + 200;
                    if (data.player.player_stamina < stamina_cost) {
                        btn.className = "lightbox-button-red";
                        btn.innerText = "Low Stamina";
                    } else {
                        btn.className = "lightbox-button-blue";
                        btn.innerText =  name
                        btn.onclick = () => onMapSelect(name, mapType);
                    }
                }
                travelSubmenu.appendChild(btn);
            });

            const backBtn = document.createElement("button");
            backBtn.className = "lightbox-button-red";
            backBtn.innerText = "← Back";
            backBtn.onclick = () => onTravel("exploration");
            travelSubmenu.appendChild(backBtn);
        } else {
            alert(data.message || "Failed to load player data.");
        }
    })
    .catch(error => console.error("Map fetch error:", error));
}


function onMapSelect(mapName, mode) {
    let actionName = (mode === "Default") ? "startExpedition" : "autoExpedition";
    fetch('./fetch_handler.php', { 
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: actionName, map_name: mapName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showRoom(data);
        } else {
            alert(data.message || "Failed to start map.");
        }
    });
}

function handleRoomAction(action, roomType) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "roomAction", room_action: action, room_type: roomType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showRoom(data);
        } else {
            alert(data.message || "Action failed.");
        }
    });
}

function showRoom(roomData){
    travelSubmenu.style.display = "None";
    travelScreenA.style.display = "Flex";
    travelScreenB.style.display = "None";
    roomImage.innerHTML = `<img src="${roomData.room_image}" alt="Room Image" class="expedition-image">`;
    roomMenu.innerHTML = roomData.room_display || "<div>No room data</div>";
}


