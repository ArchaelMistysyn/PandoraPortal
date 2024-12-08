const tabMenu = document.getElementById("tabMenu");

function buildSimpleTabMenu() {
  const tabNames = ["DPS", "Damage", "Level"];

  tabNames.forEach((tabName) => {
    const mainTabElement = document.createElement("div");
    mainTabElement.id = `mainTab-${tabName}`;
    mainTabElement.classList.add("main-tab", "highlight-text", "unselectable");
    mainTabElement.textContent = tabName;

    mainTabElement.addEventListener("click", () => {
      selectSimpleTab(mainTabElement, tabName);
    });

    tabMenu.appendChild(mainTabElement);
  });
}

function selectSimpleTab(tabElement, tabName) {
  const currentSelected = tabMenu.querySelector(".selected");
  if (currentSelected) {
    currentSelected.classList.remove("selected");
  }
  tabElement.classList.add("selected");

  // Load corresponding content
  const filePath = `./${subcontentFolder}/${tabName}.html`;
  fetch(filePath)
    .then((response) => {
      if (!response.ok) throw new Error(`Could not load ${filePath}`);
      return response.text();
    })
    .then((html) => {
      document.getElementById("tab-content").innerHTML = html;
    })
    .catch((error) => {
      console.error(error);
      document.getElementById("tab-content").innerHTML = "<p>Error loading content.</p>";
    });
}

buildSimpleTabMenu();
