function onGear(filterCategory = null) {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "displaygear" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let allInlaidGemIds = new Set(data.items.map(item => item.inlaid_id).filter(id => id !== 0));
                let filteredGear = filterCategory ? filterGear(data.items, filterCategory) : data.items;
                let playerEquipped = data.items
                    .filter(item => item.equipped)
                    .map(item => item.item_id);
                displayGear(filteredGear, allInlaidGemIds);
                displayEquippedGear(data.items, playerEquipped);
                setActiveButton(".sort-button", filterCategory);
            } else {
                alert(data.message || "Failed to load gear.");
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayGear(items, inlaidGemIds) {
    let gearScreen = document.getElementById("gear-screen");
    gearScreen.innerHTML = "";
    items.forEach(item => {
        let itemElement = document.createElement("div");
        let itemIcon = document.createElement("img");
        let itemName = document.createElement("span");
        itemElement.classList.add("gear-item");
        itemElement.dataset.itemId = item.item_id;
        itemName.textContent = item.name;
        itemName.classList.add("gear-hovername");
        itemIcon.src = item.icon;
        itemIcon.alt = item.name;
        itemIcon.classList.add("gear-icon");
        itemElement.appendChild(itemIcon);
        itemElement.appendChild(itemName);
        let itemTag = null;
        let equippedStatus = null;
        if (item.equipped) {
            equippedStatus = "Equipped";
            itemTag = document.createElement("div");
            itemTag.classList.add("gear-tag");
            itemTag.textContent = "E";
        } else if (item.item_type.includes("D") && inlaidGemIds.has(item.item_id)) {
            equippedStatus = "Inlaid";
            itemTag = document.createElement("div");
            itemTag.classList.add("gear-tag");
            itemTag.textContent = "I";
        }
        if (itemTag) itemElement.appendChild(itemTag);
        itemElement.onclick = () => openGearLightbox(item, equippedStatus);
        gearScreen.appendChild(itemElement);
    });
    gearContainer.style.display = "flex";
}

function displayEquippedGear(items, playerEquipped) {
    let equippedGearContainer = document.getElementById("equipped-gear");
    equippedGearContainer.innerHTML = "";
    let slotRows = {
        "row-1": ["W"],
        "row-2": ["A", "V"],
        "row-3": ["Y", "G", "C"],
        "row-4": ["R"]
    };
    Object.keys(slotRows).forEach(rowClass => {
        let row = document.createElement("div");
        row.classList.add("gear-row", rowClass);
        slotRows[rowClass].forEach(slotType => {
            let slotWrapper = document.createElement("div");
            slotWrapper.classList.add("gear-slot-wrapper");
            let slot = document.createElement("div");
            slot.classList.add("gear-slot");
            slot.dataset.slot = slotType;
            slotWrapper.appendChild(slot);
            let gemSlot = document.createElement("div");
            gemSlot.classList.add("gear-slot", "gem-slot", "empty-slot");
            slotWrapper.appendChild(gemSlot);
            row.appendChild(slotWrapper);
        });
        equippedGearContainer.appendChild(row);
    });
    let equippedSet = new Set(playerEquipped);
    let equippedItems = items.filter(item => equippedSet.has(item.item_id));
    equippedItems.forEach(item => {
        let slot = document.querySelector(`[data-slot="${item.item_type}"]`);
        if (!slot) return;
        let itemElement = document.createElement("div");
        let itemIcon = document.createElement("img");
        let itemName = document.createElement("span");
        itemName.classList.add("gear-hovername");
        itemElement.classList.add("gear-item");
        itemName.textContent = item.name;
        itemIcon.src = item.icon;
        itemIcon.alt = item.name;
        itemIcon.classList.add("gear-icon");
        itemElement.appendChild(itemIcon);
        itemElement.appendChild(itemName);
        slot.innerHTML = "";
        slot.appendChild(itemElement);
        itemElement.onclick = () => openGearLightbox(item, "Equipped");
        let gemSlot = slot.nextElementSibling;
        if (item.num_sockets > 0 && item.inlaid_id) {
            let gemItem = items.find(gem => gem.item_id === item.inlaid_id);
            if (gemItem) {
                let gemIcon = document.createElement("img");
                gemIcon.src = gemItem.icon;
                gemIcon.alt = gemItem.name;
                gemIcon.classList.add("gem-icon");
                gemSlot.innerHTML = "";
                gemSlot.appendChild(gemIcon);
                gemSlot.classList.remove("empty-slot");
                gemIcon.onclick = () => openGearLightbox(gemItem, "Inlaid");
            }
        } else {
            gemSlot.classList.remove("empty-slot");
            gemSlot.classList.add("no-slot");
        }
    });
    document.querySelectorAll(".gear-slot").forEach(slot => {
        if (slot.children.length === 0) slot.classList.add("empty-slot");
    });
}

function filterGear(items, category) {
    return items.filter(item => category === "Gem" ? item.item_type.includes("D") : slotTypes[item.item_type] === category);
}

function openGearLightbox(item) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "showGearItem", item_id: item.item_id })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                lightboxDisplay.innerHTML = data.html;
                lightboxMenu.innerHTML = data.menu;
                lightboxScreen.style.display = "flex"; 
            } else {
                alert(data.message || "Failed to fetch gear details.");
            }
        })
        .catch(error => console.error("Error fetching gear details:", error));
}

function EquipItem(itemId) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "equipItem", item_id: itemId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                onGear();
                closeLightbox();
            } else {
                alert(data.message || "Failed to equip item.");
            }
        })
        .catch(error => console.error("Error equipping item:", error))
        .finally(() => {
            blockingScreen.style.display = "none";
        });
}

function handleGearAction(itemId, method) {
    blockingScreen.style.display = "block";
    let action = "removeItem" + method;
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: action, item_id: itemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            onGear();
            closeLightbox();
        } else if (data.reload) {
            onGear();
            closeLightbox();
            alert(`Error: ${data.message}`);
        } else {
            alert(data.message || `Failed to ${method} item. ${data.message}`);
        }
    })
    .catch(error => console.error(`Error ${method}ing item:`, error))
    .finally(() => {
        blockingScreen.style.display = "none";
    });
}

function toggleInlayMenu() {
    let menu = document.getElementById("inlay-gear-select");
    if (menu) {
        menu.classList.toggle("hideItem");
    }
}

function InlayItem(gemId, slotType) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "inlayItem", item_id: gemId, slot_type: slotType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            onGear();
            closeLightbox();
        } else {
            alert(data.message || "Failed to inlay gem.");
        }
    })
    .catch(error => console.error("Error inlaying gem:", error))
    .finally(() => {
        blockingScreen.style.display = "none";
    });
}

