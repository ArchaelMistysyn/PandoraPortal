function setActiveButton(buttonClass, selectedValue) {
    document.querySelectorAll(buttonClass).forEach(button => {
        if (button.getAttribute("data-value") === selectedValue) {
            button.classList.add("active-menu");
        } else {
            button.classList.remove("active-menu");
        }
    });
}

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
}