function onForge(selectedItem = 'W') {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "displayForge", slot_type: selectedItem })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                forgeItemScreen.innerHTML = data.item_html;
                displayForgeMenu(data.item_data);
            } else {
                alert(data.message || "Failed to load forge.");                
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayForgeMenu(itemData) {
    const options = [
        { name: "Enhancement", action: null },
        { name: "Upgrade Quality", action: "Reinforce Quality" },
        { name: "Open Socket", action: "Create Socket" },
        { name: "Reforging", action: null },
        { name: "Cosmic Attunement", action: "Attune Rolls" },
        { name: "Astral Augment", action: null },
        { name: "Implant Element", action: null }
    ];
    let menu_html = options.map(option =>
        option.action
            ? `<button class="forge-button" onclick="setAction('${option.action}', '', '${itemData.item_type}')">${option.name}</button>`
            : `<button class="forge-button" onclick="handleForgeOption('${option.name}', '${itemData.item_type}')">${option.name}</button>`
    ).join('') + "<div id='sub-forge-menu'></div>";
    forgeMenu.innerHTML = menu_html;
    forgeContainer.style.display = "flex";
}

function handleForgeOption(option, itemType) {
    const subForgeMenu = document.getElementById('sub-forge-menu');
    let subMenuHtml = `<h3 id='forge-sub-header'>~ ${option} ~</h3>`;
    let elementMenu = `<div class="action-row">`;
        elementMenu += `<select id="elementSelect" onchange="updateButtonAction(null, 'elementSelect', ['faeButton', 'gemstoneButton', 'elementButton'])">`;
            elementMenu += ` ${["Fire", "Water", "Lightning", "Earth", "Wind", "Ice", "Light", "Shadow", "Celestial"]
                .map(e => `<option value="${e}">${e}</option>`).join('')}`;
        elementMenu += `</select>`;
    elementMenu += `</div>`;

    switch (option) {
        case "Enhancement":
            subMenuHtml += elementMenu;
            subMenuHtml += `<button class="sub-forge-button" id="faeButton" onclick="setAction('Fae Enchant', 'Fire', '${itemType}')">Fae Enchant</button>`;
            subMenuHtml += `<button class="sub-forge-button" id="gemstoneButton" onclick="setAction('Gemstone Enchant', 'Fire', '${itemType}')">Gemstone Enchant</button>`;
            break;
        case "Reforging":
            ["Hellfire", "Abyssfire", "Mutate"].forEach(type =>
                subMenuHtml += `<button class="sub-forge-button" onclick="setAction('${type} Reforge', '', '${itemType}')">${type} Reforge</button>`
            );
            break;
        case "Astral Augment":
            subMenuHtml += `<div class="action-row">`;
                subMenuHtml += `    <select id="augmentSelect" onchange="updateButtonAction('augmentSelect', null, ['augmentButton'])">`;
                    subMenuHtml += `        ${["Star Fusion (Add/Reroll)", "Radiant Fusion (Defensive)", "Chaos Fusion (All)", "Void Fusion (Damage)", "Wish Fusion (Penetration)", "Abyss Fusion (Curse)", "Divine Fusion (Unique)"]
                        .map(fusion => `<option value="${fusion}">${fusion}</option>`).join('')}`;
                subMenuHtml += `    </select>`;
            subMenuHtml += `</div>`;
            subMenuHtml += `<button class="sub-forge-button" id="augmentButton" onclick="setAction('Star Fusion (Add/Reroll)', '', '${itemType}')">Augment Rolls</button>`;
            break;
        case "Implant Element":
            subMenuHtml += elementMenu;
            subMenuHtml += `<button class="sub-forge-button" id="elementButton" onclick="setAction('Implant', 'Fire', '${itemType}')">Implant Element</button>`;
            break;
        default:
            subMenuHtml += `<p>No action available.</p>`;
    }
    subForgeMenu.innerHTML = subMenuHtml;
}

function updateButtonAction(selectId1, selectId2, buttonIds, itemType) {
    const value1 = selectId1 ? document.getElementById(selectId1).value : '';
    const value2 = selectId2 ? document.getElementById(selectId2).value : '';
    buttonIds.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        button.setAttribute('onclick', `setAction('${value1}', '${value2 || ''}', '${itemType}')`);
    });
}


function setAction(action, method, itemType) {
    let subForgeMenu = document.getElementById('sub-forge-menu');
    method = method === '' ? null : method;
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: action, slot_type: itemType, element: method })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || "Action failed.");
            return;
        }
        let menuHtml = `<h3 id='forge-sub-header'>~ ${action} ~</h3>`;
        forgeItemScreen.innerHTML = data.item_html;
        if (data.cost && data.cost.length > 0) {
            data.cost.forEach(costItem => {
                const itemId = costItem.item_id;
                const requiredQty = costItem.quantity;
                const userStock = data.stock[itemId] || 0;
                const itemIcon = getBasicItemSrc(itemId);
                const itemName = itemData[itemId]?.name || "Unknown Item";
                menuHtml += `
                    <div class="cost-row">
                        <img src="${itemIcon}" alt="${itemName}" class="cost-icon">
                        <span class="cost-name">${itemName}</span>
                        <span class="cost-quantity">${userStock} / ${requiredQty}</span>
                    </div>
                `;
            });
        }
        menuHtml += `<button class="final-forge-button" id="confirmForgeButton" 
                        onclick="executeForgeAction('${action}', '${method}')" 
                        ${data.qualified ? "" : "disabled"}>
                        Confirm ${action}
                    </button>`;
        subForgeMenu.innerHTML = menuHtml;
    })
    .catch(error => console.error('Error:', error));
}

function executeForgeAction(action, method) {
    alert("YES");
    return;
}
