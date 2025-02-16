function setActiveButton(buttonClass, selectedValue) {
    document.querySelectorAll(buttonClass).forEach(button => {
        if (button.getAttribute("data-value") === selectedValue) {
            button.classList.add("active-menu");
        } else {
            button.classList.remove("active-menu");
        }
    });
}