const tabMenu = document.getElementById("tabMenu");
const segments = {
  Main: ["Items", "Commands", "Droprates", "Elements", "Infuse"],
  Gear: ["Rings", "Misc Gear", "Tarot", "Sovereign", "Crafting", "Details"],
  Explore: [
    "Maps",
    "Map Rooms",
    "Rewards",
    "Select Pools",
    "Automapper",
    "Manifest",
  ],
  Build: ["Classes", "Paths", "Application", "Item Rolls"],
  Misc: ["Credits"]
};

function buildTabMenu() {
  for (const [mainTab, subTabs] of Object.entries(segments)) {
    const mainTabElement = document.createElement("div");
    mainTabElement.id = `mainTab-${mainTab}`;
    mainTabElement.classList.add("main-tab");
    mainTabElement.classList.add("highlight-text");
    mainTabElement.classList.add("unselectable");
    mainTabElement.innerHTML = `<span class="main-tab-text">${mainTab}</span> <span class="arrow unselectable">▲</span>`;
    const subTabsList = document.createElement("ul");
    subTabsList.id = `subTabsList-${mainTab}`;
    subTabsList.classList.add("sub-tabs");
    subTabsList.style.display = "block";
    subTabs.forEach((subTab) => {
      const subTabElement = document.createElement("li");
      subTabElement.id = `subTab-${subTab}`;
      subTabElement.textContent = subTab;
      subTabElement.addEventListener("click", () =>
        selectSubTab(mainTab, subTabElement)
      );
      subTabsList.appendChild(subTabElement);
    });

    mainTabElement.addEventListener("click", () => {
      const isExpanded = subTabsList.style.display === "block";
      subTabsList.style.display = isExpanded ? "none" : "block";
      mainTabElement.querySelector(".arrow").textContent = isExpanded
        ? "▼"
        : "▲";
    });

    tabMenu.appendChild(mainTabElement);
    tabMenu.appendChild(subTabsList);
  }
}

function initializeTabMenu() {
  const firstMainTab = Object.keys(segments)[0];
  const firstSubTab = segments[firstMainTab][0];
  const mainTabElement = document.getElementById(`mainTab-${firstMainTab}`);
  const subTabsList = document.getElementById(`subTabsList-${firstMainTab}`);
  const firstSubTabElement = document.getElementById(`subTab-${firstSubTab}`);
  subTabsList.style.display = "block";
  mainTabElement.querySelector(".arrow").textContent = "▲";
  firstSubTabElement.classList.add("selected");
  selectSubTab(firstMainTab, firstSubTabElement);
}

function selectSubTab(mainTab, subTabElement) {
  const currentSelected = tabMenu.querySelector(".selected");
  if (currentSelected) {
    currentSelected.classList.remove("selected");
  }
  subTabElement.classList.add("selected");

  const subTab = subTabElement.textContent.trim();
  const filePath = `./wiki/${mainTab}/${subTab}.html`;
  fetch(filePath)
    .then((response) => {
      if (!response.ok) throw new Error(`Could not load ${filePath}`);
      return response.text();
    })
    .then((html) => {
      document.getElementById("wiki-content").innerHTML = html;
    })
    .catch((error) => {
      console.error(error);
      document.getElementById("wiki-content").innerHTML =
        "<p>Error loading content.</p>";
    });
}

function filterItems() {
  const input = document.getElementById("filter-input").value.toUpperCase();
  const categories = document.querySelectorAll(".search-category");
  categories.forEach((category) => {
    const categoryName = category.querySelector("h3").textContent.toUpperCase();
    const items = category.querySelectorAll("tbody tr");
    let categoryMatch = false;
    items.forEach((item) => {
      const itemText = item.textContent.toUpperCase();
      const cells = Array.from(item.getElementsByTagName("td"));
      const cellMatch = cells.some(
        (cell) => cell.textContent.toUpperCase().indexOf(input) > -1
      );
      if (cellMatch || categoryName.indexOf(input) > -1) {
        item.style.display = "";
        categoryMatch = true;
      } else {
        item.style.display = "none";
      }
    });
    category.style.display = categoryMatch ? "" : "none";
  });
}

buildTabMenu();
initializeTabMenu();
