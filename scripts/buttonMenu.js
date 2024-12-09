const tabMenu = document.getElementById("tabMenu");
const savedTab = localStorage.getItem("selectedTab") || "DPS";

function buildSimpleTabMenu() {
  const tabNames = ["DPS", "Damage", "Level"];
  const buttonClasses = [
    "button-green",
    "button-amethyst",
    "button-ruby",
    "button-azure",
    "button-pink",
  ];

  tabNames.forEach((tabName, index) => {
    const mainTabElement = document.createElement("div");
    const spanContainer = document.createElement("div");
    const tabTextSpan = document.createElement("span");
    const buttonClass = buttonClasses[index % buttonClasses.length];

    mainTabElement.id = `mainTab-${tabName}`;
    mainTabElement.classList.add("main-tab", "highlight-text", "unselectable");
    spanContainer.className = "tab-span-container";
    tabTextSpan.className = "main-tab-text";
    tabTextSpan.textContent = tabName;
    mainTabElement.classList.add(buttonClass);

    spanContainer.appendChild(tabTextSpan);
    mainTabElement.appendChild(spanContainer);
    mainTabElement.addEventListener("click", () => {
      selectSimpleTab(mainTabElement, tabName);
      localStorage.setItem("selectedTab", tabName);
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

  const leaderboardContainer = document.getElementById("leaderboard-container");
  leaderboardContainer.querySelectorAll(".rank-table").forEach((table) => {
    if (table.id === `table-${tabName}`) {
      table.classList.add("active");
    } else {
      table.classList.remove("active");
    }
  });
}

function filterItems() {
    const filterInput = document.getElementById("filter-input").value.trim().toLowerCase();
    const tables = document.querySelectorAll(".rank-table");
    const maxResults = 10;
    tables.forEach((table) => {
        const rows = table.querySelectorAll("tbody tr");
        let matchCount = 0;
        rows.forEach((row) => {
            const playerId = row.dataset.playerId.toLowerCase();
            const playerUsername = row.dataset.playerUsername.toLowerCase();
            const isExactMatch = filterInput === playerId || filterInput === playerUsername;
            const isPartialMatch = playerId.includes(filterInput) || playerUsername.includes(filterInput);
            if (filterInput && (isExactMatch || isPartialMatch)) {
                row.style.display = "";
                matchCount++;
                row.classList.toggle("exact-match", isExactMatch);
            } else if (!filterInput && matchCount < maxResults) {
                row.style.display = "";
                row.classList.remove("exact-match");
                matchCount++;
            } else {
                row.style.display = "none";
                row.classList.remove("exact-match");
            }
            console.log({
                playerId,
                playerUsername,
                filterInput,
                isExactMatch,
                isPartialMatch,
            });
        });
    });
}
  
buildSimpleTabMenu();
document.getElementById(`mainTab-${savedTab}`).click();
