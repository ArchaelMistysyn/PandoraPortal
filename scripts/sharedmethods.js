function setActiveButton(buttonClass, selectedValue) {
    document.querySelectorAll(buttonClass).forEach(button => {
        if (button.getAttribute("data-value") === selectedValue) {
            button.classList.add("active-menu");
        } else {
            button.classList.remove("active-menu");
        }
    });
}

/* not in use? not working? need later?
function openCostMenu(itemCost = null, staminaCost = null, coinCost = null) {
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "costCheck", item_id: item.item_id })
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
}*/

function numberConversion(inputNumber) {
    const labels = ['', 'K', 'M', 'B', 'T', 'Q', 'Qt', 'Z', 'Z+', 'Z++', 'Z+++', 
                    'ZZ', 'ZZ+', 'ZZ++', 'ZZ+++', 'ZZZ', 'ZZZ+', 'ZZZ++', 'ZZZ+++'];

    const input = BigInt(inputNumber);
    if (input < 1000n) return input.toString();
    let idx = 0;
    let threshold = 1000n;
    while (input >= threshold * 1000n) {
        threshold *= 1000n;
        idx++;
    }
    const scaled = (input * 100n) / threshold;
    const whole = scaled / 100n;
    const frac = scaled % 100n;

    let result = whole.toString();
    if (frac !== 0n) {
        let fracStr = frac.toString().padStart(2, '0');
        fracStr = fracStr.replace(/0+$/, '');
        result += '.' + fracStr;
    }

    const label = labels[idx + 1] || '';
    return `${result} ${label}`.trim();
}
