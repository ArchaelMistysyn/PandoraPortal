function onInventory(filterCategory = null) {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "inventory" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let filteredItems = filterCategory ? filterInventory(data.items, filterCategory) : data.items;
                displayInventory(filteredItems);
                setActiveButton(".sort-button", filterCategory);
            } else {
                alert(data.message || "Failed to load inventory.");
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayInventory(items) {
    let inventoryContainer = document.getElementById("inventory-container");
    let inventoryScreen = document.getElementById("inventory-screen");
    inventoryScreen.innerHTML = "";
    items.forEach(item => {
        let itemElement = document.createElement("div");
        let itemIcon = document.createElement("img");
        let itemName = document.createElement("span");
        let itemTag = document.createElement("div");
        itemElement.classList.add("inventory-item");
        itemElement.dataset.itemId = item.item_id;
        itemElement.dataset.itemQty = item.item_qty;
        itemName.textContent = item.name;
        itemName.classList.add("inventory-hovername");
        itemIcon.src = item.icon;
        itemIcon.alt = item.name;
        itemIcon.classList.add("inventory-icon");
        itemTag.classList.add("qty-tag");
        itemTag.textContent = item.item_qty;
        itemElement.appendChild(itemIcon);
        itemElement.appendChild(itemName);
        itemElement.appendChild(itemTag);
        inventoryScreen.appendChild(itemElement);
        itemElement.onclick = () => openInventoryLightbox(item);
    });
    inventoryContainer.style.display = "flex";
}

function filterInventory(items, category) {
    const regex_dict = {
        "Crafting": /^(Matrix|Hammer|Pearl|Fragment|Crystal)/,
        "Fae Cores": /^(Fae)/,
        "Materials": /^(Scrap|Ore|Shard|Heart)/,
        "Unprocessed": /^(Unrefined|Gem|Jewel|Void)/,
        "Essences": /^(Essence)/,
        "Summoning": /^(Compass|Summon)/,
        "Gemstone": /^(Catalyst|Gemstone([0-9]|1[0]))$/,
        "Fish": /^(Fish)/,
        "Misc": /^(Potion|Trove|Chest|Stone|Token|Skull[0-3])/,
        "Ultra Rare": /^(Lotus|LightStar|DarkStar|Gemstone12|Skull4|Nephilim|Nadir|Salvation|RoyalCoin)/
    };
    return items.filter(item => {
        if (category === "Unprocessed" && /^Gemstone([0-9]|1[0])$/.test(item.item_id)) {
            return false;
        }
        return regex_dict[category].test(String(item.item_id));
    });
}

function openInventoryLightbox(item) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "showInventoryItem", item_id: item.item_id })
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