<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Wiki</title>
	<link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/wikiCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="page-body">
    <!-- Header Section -->
    <header id="header"></header>
    <!-- Main Content Section -->
    <main id="wiki-main">
        <div id="wiki-main-container">
          <div id="tab-menu">
              <input type="text" id="filter-input" placeholder="Filter Content" oninput="filterItems()">
              <div class="tab-menu" id="tabMenu"></div>
          </div>
          <div id="wiki-content-container">
              <div id="tab-content"></div>
          </div>
        </div>
    </main>
    <script>
        const subcontentFolder = "wiki";
        const segments = {
            Main: ["Items", "Commands", "Droprates", "Elements", "Infuse"],
            Gear: ["Rings", "Misc Gear", "Tarot", "Sovereign", "Crafting", "Details"],
            Explore: ["Maps", "Map Rooms", "Rewards", "Select Pools", "Automapper", "Manifest"],
            Build: ["Classes", "Paths", "Application", "Item Rolls"],
            Misc: ["Credits"]
        };
    </script>
	<script src="scripts/header.js"></script>
	<script src="scripts/tabMenu.js"></script>
    <script src="scripts/screensizeWarning.js"></script>
</body>
</html>
