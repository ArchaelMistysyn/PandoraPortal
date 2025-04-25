const lightboxScreen = document.getElementById("lightbox-screen");
const lightboxDisplay = document.getElementById("lightbox-display");
const lightboxMenu = document.getElementById("lightbox-menu");
const loadScreen = document.getElementById("loadscreen");
const selectMenu = document.getElementById("interface-screen");
const gearContainer = document.getElementById("gear-container");
const inventoryContainer = document.getElementById("inventory-container");
const forgeContainer = document.getElementById("forge-container");
const forgeItemScreen = document.getElementById("forge-item-screen");
const forgeMenu = document.getElementById('forge-menu');
const blockingScreen = document.getElementById("blocking-screen");
const loreContainer = document.getElementById("lore-container");
const loreScreen = document.getElementById("lore-screen");
const loreMenu = document.getElementById("lore-menu");
const questContainer = document.getElementById("quest-container");
const battleContainer = document.getElementById("battle-container");
const slotTypes = {"W": "Weapon", "A": "Armour", "V": "Greaves", "Y": "Amulet", "R": "Ring", "G": "Wings", "C": "Crest"};

document.getElementById("lightbox-screen").addEventListener("click", function(event) {
    let lightboxContainer = document.getElementById("lightbox-container");
    if (!lightboxContainer.contains(event.target)) {
        closeLightbox();
    }
});

function closeLightbox() {
    lightboxScreen.style.display = "none";
    blockingScreen.style.display = "none";
}

function clearScreens() {
    loadScreen.style.display = 'none';
    gearContainer.style.display = "none";
    inventoryContainer.style.display = "none";
    forgeContainer.style.display = "none";
    loreContainer.style.display = "none";
    battleContainer.style.display = "none";
    questContainer.style.display = "none";
}