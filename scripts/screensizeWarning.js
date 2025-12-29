function showSizeWarning() {
    const MIN_SCREEN_HEIGHT = 800;
    const MIN_SCREEN_WIDTH = 1000;

    if (window.innerHeight < MIN_SCREEN_HEIGHT || window.innerWidth < MIN_SCREEN_WIDTH) {
        if (localStorage.getItem('hideSizeWarning') === 'true') {
            return
        }

        const lightbox = document.createElement('div');
        const popup = document.createElement('div');
        const message1 = document.createElement('p');
        const message2 = document.createElement('p');
        const checkboxContainer = document.createElement('div');
        const doNotShowLabel = document.createElement('label');
        const doNotShowCheckbox = document.createElement('input');
        const okButton = document.createElement('button');

        lightbox.className = 'lightbox';        
        popup.className = 'popup';
        message1.textContent = 'This site is best viewed on larger screens. Some features might not display correctly on smaller devices.';
        message2.textContent = 'Thank you for your understanding!';
        okButton.textContent = 'OK';
        okButton.className = 'input-button';
        okButton.addEventListener('click', () => {
            if (doNotShowCheckbox.checked) {
                localStorage.setItem('hideSizeWarning', 'true');
            }
            document.body.removeChild(lightbox);
        });
        checkboxContainer.className = 'checkbox-container';
        doNotShowCheckbox.type = 'checkbox';
        doNotShowCheckbox.id = 'doNotShowCheckbox';
        doNotShowLabel.textContent = 'Do not show again';
        doNotShowLabel.htmlFor = 'doNotShowCheckbox';

        checkboxContainer.appendChild(doNotShowCheckbox);
        checkboxContainer.appendChild(doNotShowLabel);
        popup.appendChild(message1);
        popup.appendChild(message2);
        popup.appendChild(checkboxContainer);
        popup.appendChild(okButton);
        lightbox.appendChild(popup);

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                document.body.removeChild(lightbox);
            }
        });
        document.body.appendChild(lightbox);
    }
}

showSizeWarning();
