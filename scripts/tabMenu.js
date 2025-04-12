const tabMenu = document.getElementById("tabMenu");
const buttonClasses = [
  "button-green",
  "button-amethyst",
  "button-ruby",
  "button-azure",
  "button-pink",
];

function buildTabMenu() {
  let classIndex = 0; 
  for (const [mainTab, subTabs] of Object.entries(segments)) {
    const buttonClass = buttonClasses[classIndex % buttonClasses.length];
    const mainTabElement = document.createElement("div");
    mainTabElement.id = `mainTab-${mainTab}`;
    mainTabElement.classList.add("main-tab");
    mainTabElement.classList.add("highlight-text");
    mainTabElement.classList.add("unselectable");
    mainTabElement.classList.add(buttonClass);
    mainTabElement.innerHTML = `<div class="tab-span-container"><span class="main-tab-text">${mainTab}</span> <span class="arrow unselectable">▲</span></span>`;
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
    classIndex++;
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
  document.getElementById("filter-input").value = "";
  const tabContent = document.getElementById("tab-content");
  const currentSelected = tabMenu.querySelector(".selected");
  if (currentSelected) {
    currentSelected.classList.remove("selected");
  }
  subTabElement.classList.add("selected");  

  const subTab = subTabElement.textContent.trim();
  tabContent.className = subTab;
  let filePath = `./${subcontentFolder}/${mainTab}/${subTab}.html`;
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

function filterItems(mode="table") {
  const input = document.getElementById("filter-input").value.toUpperCase();
  const categories = document.querySelectorAll(".search-category");
  categories.forEach((category) => {
    const categoryName = category.querySelector("h3").textContent.toUpperCase();
    const rows = category.querySelectorAll("tbody tr");
    const images = category.querySelectorAll("img");
    let categoryMatch = false;

    // Filter rows in tables
    if (mode === "table") {
      rows.forEach((row) => {
        const cells = Array.from(row.getElementsByTagName("td"));
        const cellMatch = cells.some(
          (cell) => cell.textContent.toUpperCase().indexOf(input) > -1
        );
        if (cellMatch || categoryName.indexOf(input) > -1) {
          row.style.display = "";
          categoryMatch = true;
        } else {
          row.style.display = "none";
        }
      });
    }

    // Filter images
    if (mode === "images") {
      images.forEach((img) => {
        const altText = img.alt.toUpperCase();
        const previewDiv = img.closest(".preview-div");
        if (altText.indexOf(input) > -1 || categoryName.indexOf(input) > -1) {
          img.style.display = "";
          previewDiv.style.display = "";
          categoryMatch = true;
        } else {
          img.style.display = "none";
          previewDiv.style.display = "none";
        }
      });
    }

    // Set category visibility based on matches
    category.style.display = categoryMatch ? "" : "none";
  });
}


buildTabMenu();
initializeTabMenu();
