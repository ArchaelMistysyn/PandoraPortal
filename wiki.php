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
<body>
    <!-- Header Section -->
    <header id="header"></header>
    <!-- Main Content Section -->
    <main>
		<div class="content-container">
			<span class="icon">&#128269;</span>
			<input type="text" id="filter-input" placeholder="Filter content..." oninput="filterItems()">
			<div class="tab-menu" id="tabMenu"></div>
            <div id="wiki-content"></div>
        </div>
    </main>
	<script src="scripts/header.js"></script>
	<script src="scripts/wikiMenu.js"></script>
	<script>			
		function filterItems() {
			const input = document.getElementById('filter-input').value.toUpperCase();
			const categories = document.querySelectorAll('.search-category');
			categories.forEach(category => {
				const categoryName = category.querySelector('h3').textContent.toUpperCase();
				const items = category.querySelectorAll('tbody tr');
				let categoryMatch = false;
				items.forEach(item => {
					const itemText = item.textContent.toUpperCase();
					const cells = Array.from(item.getElementsByTagName('td'));
					const cellMatch = cells.some(cell => cell.textContent.toUpperCase().indexOf(input) > -1);
					if (cellMatch || categoryName.indexOf(input) > -1) {
						item.style.display = '';
						categoryMatch = true;
					} else {
						item.style.display = 'none';
					}
				});
				category.style.display = categoryMatch ? '' : 'none';
			});
		}
    </script>
</body>
</html>
