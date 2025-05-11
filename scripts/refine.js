const refine_options = {
    "Basic": [
        { name: "Dragon Wing", success: 75, item_id: "Unrefined1", tier: 4 },
        { name: "Demon Greaves", success: 75, item_id: "Unrefined2", tier: 4 },
        { name: "Paragon Crest", success: 75, item_id: "Unrefined3", tier: 4 }
    ],
    "Void": [
        { name: "Void Weapon", success: 100, item_id: "Void1", tier: 5 },
        { name: "Void Armour", success: 80, item_id: "Void2", tier: 5 },
        { name: "Void Greaves", success: 80, item_id: "Void3", tier: 5 },
        { name: "Void Amulet", success: 80, item_id: "Void4", tier: 5 },
        { name: "Void Wings", success: 80, item_id: "Void5", tier: 5 },
        { name: "Void Crest", success: 80, item_id: "Void6", tier: 5 }
    ],
    "Gem": [
        { name: "Dragon Gem", success: 75, item_id: "Gem1", tier: 4 },
        { name: "Demon Gem", success: 75, item_id: "Gem2", tier: 4 },
        { name: "Paragon Gem", success: 75, item_id: "Gem3", tier: 4 }
    ],
    "Jewel": [
        { name: "Dragon Jewel", success: 50, item_id: "Jewel1", tier: 5 },
        { name: "Demon Jewel", success: 50, item_id: "Jewel2", tier: 5 },
        { name: "Paragon Jewel", success: 50, item_id: "Jewel3", tier: 5 },
        { name: "Arbiter Jewel", success: 50, item_id: "Jewel4", tier: 6 },
        { name: "Incarnate Jewel", success: 50, item_id: "Jewel5", tier: 7 }
    ]
};


function onRefine(selectedMenu = 'Basic') {
    clearScreens();
    refineItemScreen.innerHTML = '';
    displayRefineMenu(selectedMenu);
    setActiveButton(".sort-button", selectedMenu);
}

function displayRefineMenu(slotType) {
    const slotOptions = refine_options[slotType] || [];
    let menu_html = slotOptions.map((option, i) =>
        `<button class="forge-button" onclick="setRefineAction('${slotType}', ${i})">${option.name}</button>`
    ).join('');
    menu_html += "<div id='sub-refine-menu'></div>";
    refineMenu.innerHTML = menu_html;
    refineryContainer.style.display = 'flex';
}

function setRefineAction(slotType, index) {
    let subRefineMenu = document.getElementById('sub-refine-menu');
    const option = refine_options[slotType][index];
    const itemId = option.item_id;
    const itemIcon = itemData[itemId]?.image_link || "";
    const itemName = itemData[itemId]?.name || "Unknown Item";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "inventory" })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stockEntry = data.items.find(item => item.item_id === itemId);
                const userStock = stockEntry ? stockEntry.item_qty : 0;
                let menuHtml = `<h3 id='refine-sub-header' class='highlight-text'>~ ${option.name} ~</h3>`;
                menuHtml += `
                    <div class="cost-row">
                        <img src="${itemIcon}" alt="${itemName}" class="cost-icon">
                        <span class="cost-name">${itemName}</span>
                        <span class="cost-quantity">${userStock} / 1</span>
                    </div>
                `;
                let buttonClass = "final-forge-button";
                let buttonOnClick = `onclick="executeRefineAction('${option.item_id}', this, '${slotType}', ${index})"`;
                let buttonText = "Refine &lpar;" + option.success + "%&rpar;";
                if (userStock < 1) {
                    buttonOnClick = "";
                    buttonClass = "disabled-button";
                    buttonText = "Out of Stock";
                }
                menuHtml += `<button id="confirmRefineButton" class="${buttonClass}" ${buttonOnClick}>${buttonText}</button>`;
                subRefineMenu.innerHTML = menuHtml;
                subRefineMenu.classList.add('forge-submenu-border');
            } else {
                alert(data.message || "Failed to load inventory.");
            }
        })
        .catch(error => console.error('Error:', error));
}

function executeRefineAction(itemId, buttonElement, slotType, index) {
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: itemId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.inventory_check === false) {
            alert("Your inventory is full for this item.");
        } else if (data.stock_check === false) {
            alert("You do not have the required material.");
            if (buttonElement) {
                buttonElement.className = "disabled-button";
                buttonElement.innerText = "Out of Stock";
                buttonElement.removeAttribute("onclick");
                blockingScreen.style.display = "none";
            }
        } else {
            animateRefineOutcome(data.item_html, slotType, index);
        }
    })
    .catch(error => {
        console.error("Error executing refine action:", error);
        alert("An error occurred while refining:");
        blockingScreen.style.display = "none";
    });
}

function animateRefineOutcome(item_html, slotType, index) {
    setRefineAction(slotType, index);
    refineItemScreen.innerHTML = item_html;
    success = item_html !== '';
    setTimeout(() => {
        refineItemScreen.classList.add(success ? "forge-success" : "forge-failure");
        setTimeout(() => {
            refineItemScreen.classList.remove("forge-success", "forge-failure");
            blockingScreen.style.display = "none";
        }, 1200);
    }, 80);
}
