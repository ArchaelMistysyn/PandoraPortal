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
    const buttons = document.querySelectorAll('#detail-buttons');
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