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
  const buttons = document.querySelectorAll("#detail-buttons button");
  buttons.forEach((button, i) => {
    if (i < buttons.length - 1) {
      button.classList.remove("current-button");
      button.onclick = () => handleButtonClick(i);
    }
  });
  buttons[index].classList.add("current-button");
  buttons[index].onclick = null;
  document.getElementById("detail-box").innerHTML = sectionContent[index];
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
  document.querySelectorAll(".item-slot-button").forEach((button) => {
    button.classList.remove("item-slot-button-active");
    const buttonSlotId = button.id.replace("item-slot-", "");
    button.onclick = () => showEquipmentSlot(buttonSlotId);
  });
  document.querySelectorAll(".item-slot").forEach((slot) => {
    slot.classList.remove("visible-tag");
    slot.classList.add("hidden-tag");
  });

  currentButton.classList.add("item-slot-button-active");
  currentButton.onclick = null;
  if (itemSlot) {
    itemSlot.classList.add("visible-tag");
    itemSlot.classList.remove("hidden-tag");
  }
  if (gemSlot) {
    gemSlot.classList.add("visible-tag");
    gemSlot.classList.remove("hidden-tag");
  }
  if (tarotImage) {
    if (slotId == "Tarot") {
      tarotImage.classList.add("visible-tag");
      tarotImage.classList.remove("hidden-tag");
    } else {
      tarotImage.classList.add("hidden-tag");
      tarotImage.classList.remove("visible-tag");
    }
  }
  if (slotId == "Pact") {
    voidPact.classList.add("visible-tag");
    voidPact.classList.remove("hidden-tag");
  } else {
    voidPact.classList.add("hidden-tag");
    voidPact.classList.remove("visible-tag");
  }
  if (slotId == "Insignia") {
    voidInsignia.classList.add("visible-tag");
    voidInsignia.classList.remove("hidden-tag");
  } else {
    voidInsignia.classList.add("hidden-tag");
    voidInsignia.classList.remove("visible-tag");
  }
}

function toggleSearchBar() {
  const charSection = document.getElementById("char-name-section");
  const searchBar = document.getElementById("search-bar-section");
  charSection.style.display =
    searchBar.style.display === "flex" || searchBar.style.display === ""
      ? "flex"
      : "none";
  searchBar.style.display =
    searchBar.style.display === "none" || searchBar.style.display === ""
      ? "flex"
      : "none";
}

function addHoverListeners() {
  const mainSections = document.querySelectorAll(".detail-section");

  mainSections.forEach((section) => {
    const sectionId = section.id;
    const sideBox = document.getElementById(
      `side-box-${sectionId.replace("section-", "")}`
    );

    section.addEventListener("mouseenter", () => {
      // Handle selecting detail box
      document
        .querySelectorAll(".detail-section")
        .forEach((sec) => sec.classList.remove("focused-element"));

      section.classList.add("focused-element");

      // Handle selecting side box
      document.querySelectorAll(".side-detail-list-active").forEach((box) => {
        box.classList.remove("side-detail-list-active");
        box.classList.add("side-detail-list");
      });
      if (sideBox) {
        sideBox.classList.remove("side-detail-list");
        sideBox.classList.add("side-detail-list-active");
      }
    });
  });
}

function removeHoverListeners() {
  const mainSections = document.querySelectorAll(".detail-section");
  mainSections.forEach((section) => {
    const newSection = section.cloneNode(true);
    section.parentNode.replaceChild(newSection, section);
  });
}

function toggleSlotDisplay(type) {
  const itemSlot = document.getElementById(`item-${type}`);
  const gemSlot = document.getElementById(`gem-${type}`);
  const tarotImage = document.getElementById(`image-tarot`);
  const targetSlot = (type === "Tarot") ? tarotImage : gemSlot;
  
  if (itemSlot.classList.contains('active')) {
      itemSlot.classList.remove('active');
      targetSlot.classList.add('active');
  } else {
      itemSlot.classList.add('active');
      targetSlot.classList.remove('active');
  }
}
