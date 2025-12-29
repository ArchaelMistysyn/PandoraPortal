const datasets = {
    Shirts: [
        { name: "ArchDragon T-Shirt [2024 Variant 2]", image: "./images/Store/Shirts/ArchDragonShirt2024v2.jpg", price: "$20", url: "archdragon-t-shirt-2024-variant-2" },
        { name: "ArchDragon T-Shirt [2024 Variant]", image: "./images/Store/Shirts/ArchDragonShirt2024v1.jpg", price: "$20", url: "archdragon-t-shirt-2024-variant" },
        { name: "Ruler's Crown T-Shirt", image: "./images/Store/Shirts/RulersCrownShirt.jpg", price: "$20", url: "rulers-crown-t-shirt" },
        { name: "Ruler's Star Jewel T-Shirt", image: "./images/Store/Shirts/RulersStarShirt.jpg", price: "$20", url: "rulers-star-jewel-t-shirt" }
    ],
    Posters: [
        { name: "Framed Matte Art Poster - Arcelia, the Clarity", image: "./images/Store/Posters/arcelia_clarity_poster.jpg", price: "$50", url: "framed-poster-arcelia-the-clarity" },
        { name: "Framed Matte Art Poster - Oblivia, the Void", image: "./images/Store/Posters/oblivia_void_poster.jpg", price: "$50", url: "framed-poster-oblivia-the-void" }
    ],
    Mousepads: [
        { name: "Earth Dragon Mouse pad", image: "./images/Store/Mousepads/EarthDragon.jpg", price: "$20", url: "earth-dragon" },
        { name: "Fire Dragon Mouse pad", image: "./images/Store/Mousepads/FireDragon.jpg", price: "$20", url: "fire-dragon" },
        { name: "Ice Dragon Mouse pad", image: "./images/Store/Mousepads/IceDragon.jpg", price: "$20", url: "ice-dragon" },
        { name: "Water Dragon Mouse pad", image: "./images/Store/Mousepads/WaterDragon.jpg", price: "$20", url: "water-dragon" }
    ],
    Gift_Cards: [
        { name: "Bronze Gift Card", image: "./images/Store/Giftcard/Giftcard_Bronze.png", price: "$10", url: "bronze-gift-card" },
        { name: "Silver Gift Card", image: "./images/Store/Giftcard/Giftcard_Silver.png", price: "$25", url: "silver-gift-card" },
        { name: "Gold Gift Card", image: "./images/Store/Giftcard/Giftcard_Gold.png", price: "$50", url: "gold-gift-card" },
        { name: "Platinum Gift Card", image: "./images/Store/Giftcard/Giftcard_Platinum.png", price: "$100", url: "platinum-gift-card" },
        { name: "Sovereign Gift Card", image: "./images/Store/Giftcard/Giftcard_Sovereign.png", price: "$250", url: "sovereign-gift-card" },
        { name: "Sacred Gift Card", image: "./images/Store/Giftcard/Giftcard_Sacred.png", price: "$500", url: "sacred-gift-card" }
    ],
    Other: [
        // Add other items if needed here
    ]
};


function loadCategory(categoryName) {
    const items = datasets[categoryName.replace(" ", "_")];
    if (!items) return;
    const backdrop = document.getElementById('backdrop');
    const contentDisplay = document.getElementById('content-display');
    contentDisplay.innerHTML = '';
    // contentDisplay.innerHTML = `<h2 class="category-header">${categoryName}</h2>`;

    items.forEach(item => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');
        productCard.innerHTML = `
            <a href="https://archdragonstore.ca/products/${item.url}" target="_blank">
                <img src="${item.image}" alt="${item.name}">
                <h3>${item.name}</h3>
                <p>From ${item.price}</p>
            </a>
        `;
        contentDisplay.appendChild(productCard);
    });
    backdrop.style.display = 'block';
    contentDisplay.style.display = 'grid';
}

function closeBackdrop() {
    const backdrop = document.getElementById('backdrop');
    const contentDisplay = document.getElementById('content-display');
    backdrop.style.display = 'none';
    contentDisplay.style.display = 'none';
}