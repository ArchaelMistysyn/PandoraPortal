const purify_labels = { 5: "Wish Purification", 6: "Abyss Purification", 7: "Divine Purification", 8: "Blood Purification", 9: "Blood Extraction" };
let meldSlots = { A: null, B: null };
const meldSlotA = document.getElementById("meld-slot-a");
const meldSlotB = document.getElementById("meld-slot-b");
const btnA = document.getElementById("meld-select-a");
const btnB = document.getElementById("meld-select-b");
const swapBtn = document.getElementById("swap-button");
const meldBtn = document.getElementById("meld-button");
const costDisplay=document.getElementById("meld-cost-display");
const targetDisplay=document.getElementById("meld-target-display");
const affinityDisplay=document.getElementById("meld-affinity-display");

function onForge(selectedItem = 'W', abyss = false) {
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
                displayForgeMenu(data.item_data, abyss);
                setActiveButton(".sort-button", selectedItem);
                document.querySelectorAll(".sort-button").forEach(button => {
                    button.setAttribute("onclick", `onForge('${button.getAttribute("data-value")}', ${abyss})`);
                });
            } else {
                alert(data.message || "Failed to load forge.");                
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayForgeMenu(itemData, abyss=false) {
    const options = [
        { name: "Enhancement", action: null },
        { name: "Astral Augment", action: itemData.item_num_rolls < 6 ? "Star Fusion (Add/Reroll)" : null },
        { name: "Cosmic Attunement", action: "Attune Rolls" },
        { name: "Upgrade Quality", action: "Reinforce Quality" },
        { name: "Open Socket", action: "Create Socket" },
        { name: "Reforging", action: null },
        { name: "Implant Element", action: null }
    ];
    let menu_html = options.map(option => {
        return option.action
            ? `<button class="forge-button" onclick="setAction('${option.action}', '', '${itemData.item_type}')">${option.name}</button>`
            : `<button class="forge-button" onclick="handleForgeOption('${option.name}', '${itemData.item_type}')">${option.name}</button>`;
    }).join('');
    if (abyss) {
        if (itemData.tier >= 5) {
            menu_html += `<button class="abyss-button" onclick="setAction('${purify_labels[itemData.tier]}', '', '${itemData.item_type}')">${purify_labels[itemData.tier]}</button>`;
        } else {
            menu_html += `<button class="abyss-button abyss-button-disabled">Unknown</button>`;
        }
    }    
    forgeMenu.innerHTML = menu_html + "<div id='sub-forge-menu'></div>";
    forgeContainer.style.display = "flex";
}

function handleForgeOption(option, itemType) {
    const subForgeMenu = document.getElementById('sub-forge-menu');
    let subMenuHtml = `<h3 id='forge-sub-header' class='highlight-text'>~ ${option} ~</h3>`;
    let elementMenu = `<div class="action-row">
        <select id="elementSelect" onchange="updateButtonAction('elementSelect', ['faeButton', 'gemstoneButton', 'elementButton'])">
            ${["Fire", "Water", "Lightning", "Earth", "Wind", "Ice", "Shadow", "Light", "Celestial"]
                .map((e, i) => `<option value=${i}>${e}</option>`).join('')}
        </select>
    </div>`;

    switch (option) {
        case "Enhancement":
            subMenuHtml += elementMenu;
            subMenuHtml += `<button class="sub-forge-button" id="faeButton" data-action="Fae Enchant" data-method="0" data-item="${itemType}" onclick="setActionFromButton(this)">Fae Enchant</button>`;
            subMenuHtml += `<button class="sub-forge-button" id="gemstoneButton" data-action="Gemstone Enchant" data-method="0" data-item="${itemType}" onclick="setActionFromButton(this)">Gemstone Enchant</button>`;
            break;
        case "Reforging":
            ["Hellfire", "Abyssfire", "Mutate"].forEach(type => {
                if (type === "Abyssfire" && itemType === "W") return;
                subMenuHtml += `<button class="sub-forge-button" data-action="${type} Reforge" data-method="" data-item="${itemType}" onclick="setActionFromButton(this)">${type} Reforge</button>`;
            });
            break; 
        case "Astral Augment":
            let augmentOptions = [
                "Star Fusion (Add/Reroll)", "Radiant Fusion (Defensive)", "Chaos Fusion (All)",
                "Void Fusion (Damage)", "Wish Fusion (Penetration)", "Abyss Fusion (Curse)", "Divine Fusion (Unique)"
            ];
            if (itemType === "Y") { 
                augmentOptions.push("Salvation (Class Skill)");
            }
            subMenuHtml += `<div class="action-row">
                <select id="augmentSelect" onchange="updateButtonAction('augmentSelect', ['augmentButton'])">
                    ${augmentOptions.map(fusion => `<option value="${fusion}">${fusion}</option>`).join('')}
                </select>
            </div>`;
            subMenuHtml += `<button class="sub-forge-button" id="augmentButton" data-action="${augmentOptions[0]}" data-method="" data-item="${itemType}" onclick="setActionFromButton(this)">Augment Rolls</button>`;
            break;   
        case "Implant Element":
            subMenuHtml += elementMenu;
            subMenuHtml += `<button class="sub-forge-button" id="elementButton" data-action="Implant" data-method="0" data-item="${itemType}" onclick="setActionFromButton(this)">Implant Element</button>`;
            break;
        default:
            subMenuHtml += `<p>No action available.</p>`;
    }
    subForgeMenu.innerHTML = subMenuHtml;
}

function updateButtonAction(selectId, buttonIds) {
    const newValue = document.getElementById(selectId).value;
    buttonIds.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.setAttribute(selectId === "augmentSelect" ? 'data-action' : 'data-method', newValue);
        }
    });
}

function setActionFromButton(button) {
    const action = button.getAttribute('data-action');
    const method = button.getAttribute('data-method');
    const itemType = button.getAttribute('data-item');
    setAction(action, method, itemType);
}

const actionLabels = {
    "Fae Enchant": "Enhance",
    "Gemstone Enchant": "Enhance",
    "Implant": "Implant",
    "Star Fusion (Add/Reroll)": "Fusion",
    "Radiant Fusion (Defensive)": "Fusion",
    "Chaos Fusion (All)": "Fusion",
    "Void Fusion (Damage)": "Fusion",
    "Wish Fusion (Penetration)": "Fusion",
    "Abyss Fusion (Curse)": "Fusion",
    "Divine Fusion (Unique)": "Fusion",
    "Attune Rolls": "Attune",
    "Hellfire Reforge": "Reforge",
    "Abyssfire Reforge": "Reforge",
    "Mutate Reforge": "Reforge",
    "Reinforce Quality": "Augment",
    "Create Socket": "Augment",
    "Wish Purification": "Purify",
    "Abyss Purification": "Purify",
    "Divine Purification": "Purify",
    "Blood Purification": "Purify",
    "Blood Extraction": "Extract"
};


function setAction(action, method, itemType) {
    method = method === '' ? null : method;
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: action, slot_type: itemType, element: method })
    })
    .then(response => response.json())
    .then(data => updateForgeUI(data, action, method, itemType))
    .catch(error => console.error("Error setting forge action:", error));
}

function executeForgeAction(action, method, itemType, abyss = false) {
    method = method === '' ? null : method;
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: action, slot_type: itemType, element: method, execute: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.action_triggered && (action.includes("Purification") || action === "Blood Extraction")) {
            displayForgeMenu(data.item_data, abyss);
        } else {
            updateForgeUI(data, action, method, itemType, abyss);
        }
        forgeItemScreen.innerHTML = data.item_html;
        animateForgeOutcome(data.action_triggered);
    })
    .catch(error => {
        console.error("Error executing forge action:", error);
        alert("An error occurred while processing the action.");
        blockingScreen.style.display = "none";
    })
}

function updateForgeUI(data, action, method, itemType, abyss = false) {
    let subForgeMenu = document.getElementById('sub-forge-menu');
    let label = actionLabels[action] || "Forge";
    if (!data.success) {
        alert(data.message || "Action Error.");
        return;
    }
    let menuHtml = `<h3 id='forge-sub-header' class='highlight-text'>~ ${action} ~</h3>`;
    let hasStock = true;
    if (data.cost && data.cost.length > 0) {
        data.cost.forEach(costItem => {
            const itemId = costItem.item_id;
            const requiredQty = costItem.quantity;
            const userStock = data.stock[itemId] || 0;
            const itemIcon = itemData[itemId]?.image_link || "";
            const itemName = itemData[itemId]?.name || "Unknown Item";
            if (userStock < requiredQty) hasStock = false;
            menuHtml += `
                <div class="cost-row">
                    <img src="${itemIcon}" alt="${itemName}" class="cost-icon">
                    <span class="cost-name">${itemName}</span>
                    <span class="cost-quantity">${userStock} / ${requiredQty}</span>
                </div>
            `;
        });
    }
    if (label.includes('Extract')) {
        menuHtml += '<div class="cost-row">Returns:</div>';
        menuHtml += `<div class="cost-row">
                        <img src="${itemData['Sacred'].image_link}" alt="${itemData['Sacred'].name}" class="cost-icon">
                        <span class="cost-name">${itemData['Sacred'].name}</span>
                        <span class="cost-quantity">1x</span>
                    </div>`;
    } else if (data.cost.length < 2) {
        menuHtml += '<div class="cost-row"></div>';
    }
    let buttonText = label;
    let buttonClass = "disabled-button";
    let buttonOnClick = "";
    if (!hasStock) {
        buttonText = "Out of Stock";
    } else if (!data.qualified) {
        if (!["Reforge", "Purify", "Extract"].some(word => label.includes(word))) {
            buttonText += " [MAX]";
        }        
    } else {
        buttonText = `${label} (${data.success_rate}%)`;
        buttonClass = "final-forge-button";
        buttonOnClick = `onclick="executeForgeAction('${action}', '${method}', '${itemType}', '${abyss}')"`; 
    }
    menuHtml += `<button id="confirmForgeButton" class="${buttonClass}" ${buttonOnClick}>${buttonText}</button>`;
    subForgeMenu.classList.add('forge-submenu-border');
    subForgeMenu.innerHTML = menuHtml;
}

function onMeld() {
    clearScreens();
    fetch('./fetch_handler.php', {
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body:JSON.stringify({action:"showTwoItems", numeric_id:meldSlots["A"], numeric_id2:meldSlots["B"] })
    })
    .then(response=>response.json())
    .then(data=>{
        if(!data.success){
            alert(data.message || "Failed to load meld menu.");
            return;
        }
        meldContainer.style.display="flex";
        meldSlotA.innerHTML = data.slotA_html && data.slotA_html !== "null" ? data.slotA_html : "<div class='item-slot'></div>";
        meldSlotB.innerHTML = data.slotB_html && data.slotB_html !== "null" ? data.slotB_html : "<div class='item-slot'></div>";
        btnA.innerText=meldSlots.A ? "Unselect":"Select";
        btnB.innerText=meldSlots.B ? "Unselect":"Select";
        const bothFilled=meldSlots.A!==null && meldSlots.B!==null;
        swapBtn.disabled=!bothFilled || !data.canSwap;
        meldBtn.disabled=!bothFilled || !data.canMeld;
        swapBtn.className=(bothFilled && data.canSwap) ? "lightbox-button-blue" : "lightbox-button-gray";
        meldBtn.className=(bothFilled && data.canMeld) ? "lightbox-button-green" : "lightbox-button-gray";
        updateMeldInfoPanel(data, bothFilled);
    })
    .catch(error=>console.error("Error:",error));
}

function updateMeldInfoPanel(data, bothFilled){
    const itemIcon = itemData["Token4"]?.image_link || "";
    let costValue = "—";
    let targetValue = "—";
    let affinityValue = "—";
    if(bothFilled){
        costValue = data.playerTokens + " / " + data.meldCost;
        targetValue = data.targetTier;
        affinityValue= data.affinityRate + "%";
    }
    costDisplay.innerHTML=`<img src="${itemIcon}" class="cost-icon">` + "<div class='meld-cost-text'>Cost: " + costValue + "</div>";
    targetDisplay.innerHTML="Target Tier: " + targetValue;
    affinityDisplay.innerHTML="Affinity: " + affinityValue;
}

function gemSelect(slot) {
    if (meldSlots[slot] !== null) {
        meldSlots[slot] = null;
        onMeld();
    } else {
        onGear("Gem");
    }
}

function selectMeldGem(item_id,slot){
    const otherSlot = slot === "A" ? "B" : "A";
    if (meldSlots[otherSlot] === item_id) {
        meldSlots[otherSlot] = null;
    }
    meldSlots[slot] = item_id;
    closeLightbox();
    onMeld();
}

function swapGems() {
    if (meldSlots.A === null || meldSlots.B === null) return;
    [meldSlots.A, meldSlots.B] = [meldSlots.B, meldSlots.A];
    onMeld();
}

function runMeld() {
    swapBtn.disabled = true;
    meldBtn.disabled = true;
    swapBtn.className = "lightbox-button-gray";
    meldBtn.className = "lightbox-button-gray";
    if (meldSlots.A === null || meldSlots.B === null || meldSlots.A === meldSlots.B) return;
    blockingScreen.style.display = "block";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({action: "executeMeld", numeric_id: meldSlots.A, numeric_id2: meldSlots.B})
    })
    .then(response => response.json())
    .then(data => {
        blockingScreen.style.display = "none";
        if (!data.success) {
            alert(data.message || "Meld failed.");
            return;
        }
        animateMeldOutcome(data.meld_success);
        meldSlotA.innerHTML = data.slotA_html || "<div class='item-slot'></div>";
        meldSlotB.innerHTML = "<div class='item-slot'></div>";
        meldSlots.B = null;
        btnB.innerText = "Select";
    })
    .catch(error => {
        blockingScreen.style.display = "none";
        console.error("Meld error:", error);
        alert("An error occurred during melding.");
    });
}

function animateMeldOutcome(success) {
    meldSlotA.classList.add(success ? "forge-success" : "forge-failure");
    meldSlotB.classList.add("forge-failure");
    setTimeout(() => {
        meldSlotA.classList.remove("forge-success", "forge-failure");
        meldSlotB.classList.remove("forge-failure");
        blockingScreen.style.display = "none";
    }, 1200);
}

function animateForgeOutcome(success) {
    forgeItemScreen.classList.add(success ? "forge-success" : "forge-failure");
    setTimeout(() => {
        forgeItemScreen.classList.remove("forge-success", "forge-failure");
        blockingScreen.style.display = "none";
    }, 1200);
}


