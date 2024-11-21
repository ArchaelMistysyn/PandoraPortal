<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Wiki</title>
	<link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/WikiCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="page-body">
    <!-- Header Section -->
    <header id="header"></header>
    <!-- Main Content Section -->
    <main id="wiki-flex">
        <div id="wiki-menu">
            <input type="text" id="filter-input" placeholder="Filter Content" oninput="filterItems()">
            <div class="tab-menu" id="tabMenu"></div>
        </div>
        <div id="content-container">
            <div id="wiki-content"></div>
        </div>
    </main>
	<script src="scripts/header.js"></script>
	<script src="scripts/wikiMenu.js"></script>
</body>
</html>
