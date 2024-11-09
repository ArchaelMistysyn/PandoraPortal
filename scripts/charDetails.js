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
}

function showEquipmentSlot(slotId) {
    const currentButton = document.getElementById(`item-slot-${slotId}`);
    const itemSlot = document.getElementById(`item-${slotId}`);
    const gemSlot = document.getElementById(`gem-${slotId}`);
    const tarotImage = document.getElementById(`image-tarot`);

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
}

