function refreshPlayerData() {
    location.reload();
}

function checkImageExists(url, callback) {
    const img = new Image();
    img.onload = () => callback(true);
    img.onerror = () => callback(false);
    img.src = url;
}

function handleButtonClick(index) {
    const buttons = document.querySelectorAll('#detail-buttons button');
    buttons.forEach((button, i) => {
        if (i < buttons.length - 1) {
            button.classList.remove('current-button');
            button.onclick = () => handleButtonClick(i);
        }
    });
    buttons[index].classList.add('current-button');
    buttons[index].onclick = null;
    document.getElementById('detail-box').innerHTML = sectionContent[index];
    if (index === 4) {
        addHoverListeners();
    } else {
        removeHoverListeners();
    }
}

function showEquipmentSlot(slotId) {
    const currentButton = document.getElementById(`item-slot-${slotId}`);
    const itemSlot = document.getElementById(`item-${slotId}`);
    const gemSlot = document.getElementById(`gem-${slotId}`);
    const tarotImage = document.getElementById(`image-tarot`);
    const voidPact = document.getElementById(`void-pact`);
    const voidInsignia = document.getElementById(`void-insignia`);

    document.querySelectorAll('.item-slot-button').forEach(button => {
        button.classList.remove('item-slot-button-active');
        const buttonSlotId = button.id.replace('item-slot-', '');
        button.onclick = () => showEquipmentSlot(buttonSlotId);
    });
    document.querySelectorAll('.item-slot').forEach(slot => {
        slot.style.display = 'none';
    });

    currentButton.classList.add('item-slot-button-active');
    currentButton.onclick = null;
    
    if (itemSlot) {
        itemSlot.style.display = 'flex';
    }
    if (gemSlot) {
        gemSlot.style.display = 'flex';
    }
    if (tarotImage) {
        if (slotId == "Tarot") { 
            tarotImage.style.display = 'flex';
        } else {
            tarotImage.style.display = 'none';
        }
    }
    if (slotId == "Pact") { 
        voidPact.style.display = 'block';
    } else {
        voidPact.style.display = 'none';
    }
    if (slotId == "Insignia") { 
        voidInsignia.style.display = 'block';
    } else {
        voidInsignia.style.display = 'none';
    }
}

function toggleSearchBar() {
    const charSection = document.getElementById('char-name-section');
    const searchBar = document.getElementById('search-bar-section');
    charSection.style.display = (searchBar.style.display === 'flex' || searchBar.style.display === '') ? 'flex' : 'none';
    searchBar.style.display = (searchBar.style.display === 'none' || searchBar.style.display === '') ? 'flex' : 'none';
}

function addHoverListeners() {
    const mainSections = document.querySelectorAll('.detail-section');
    mainSections.forEach(section => {
        const sectionId = section.id;
        const sideBox = document.getElementById(`side-box-${sectionId.replace('section-', '')}`);
        section.addEventListener('mouseenter', () => {
            document.querySelectorAll('.side-detail-list-active').forEach(box => {
                box.classList.remove('side-detail-list-active');
                box.classList.add('side-detail-list');
            });
            if (sideBox) {
                sideBox.classList.remove('side-detail-list');
                sideBox.classList.add('side-detail-list-active');
            }
        });
    });
}

function removeHoverListeners() {
    const mainSections = document.querySelectorAll('.detail-section');
    mainSections.forEach(section => {
        const newSection = section.cloneNode(true);
        section.parentNode.replaceChild(newSection, section);
    });
}
