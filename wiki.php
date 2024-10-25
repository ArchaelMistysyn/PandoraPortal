<!DOCTYPE html>
<html lang="en">
<?php
	include_once('./nonpublic/db_credentials.php');
	// session_start();
	$sn = DB_SERVER;
	$dbu = DB_USERNAME;
	$dbp = DB_PASSWORD;
	$dbn = DB_NAME;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Wiki</title>
    <link rel="stylesheet" href="pandoraCSS.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
			<a href="index.php">
				<img src="./images/icon.png" alt="Website Icon">
				<h1>Wiki</h1>
			</a>
        </div>
		<nav id="primary-nav">
			<ul class="sf-menu primary-nav">
				<li class="primary-option"><a href="#">Main</a><ul></ul></li>
				<li class="primary-option"><a href="#">Gear</a><ul></ul></li>
				<li class="primary-option"><a href="#">Build</a><ul></ul></li>
				<li class="primary-option"><a href="#">Explore</a><ul></ul></li>
			</ul>
		</nav>
        <nav id="page-nav">
            <ul>
				<li><a href="wiki.php" class="selected">Wiki</a></li>
                <li><a href="characters.php">Character</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="ranking.php">Ranking</a></li>
                <li><a href="https://www.ArchDragonStore.ca" target="_blank">Store</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Content Section -->
    <main class="no-footer">
        <div class="content-container">
            <div id="wiki-content">
            </div>
        </div>
    </main>
	<script>
		const preloadedContent = {};
        const segments = {
			"Main": ["Items", "Commands", "Droprates", "Elements", "Infuse"],
			"Gear": ["Rings", "Misc Gear", "Tarot", "Sovereign", "Crafting", "Details"],
			"Explore": ["Maps", "Map Rooms", "Rewards", "Select Pools", "Automapper", "Manifest"],
            "Build": ["Classes", "Paths", "Application", "Item Rolls"],
        };
		
		function preloadSegments() {
			Object.keys(segments).forEach(folder => {
				segments[folder].forEach(subfolder => {
					const url = `./wiki/${folder}/${subfolder}.html`;
					fetch(url)
						.then(response => response.text())
						.then(data => {
							if (!preloadedContent[folder]) {
								preloadedContent[folder] = {};
							}
							preloadedContent[folder][subfolder] = data;
						})
						.catch(error => console.error(`Error preloading ${url}:`, error));
				});
			});
		}
		
		function highlightSelectedFolder(navItem) {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(a => a.classList.remove('active'));
			navItem.classList.add('active');
		}
		
		function loadPrimaryFolders() {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(navItem => {
				navItem.addEventListener('click', () => {
					highlightSelectedFolder(navItem);
					const folder = navItem.textContent;
					const firstSubfolder = segments[folder] ? segments[folder][0] : null;
					if (firstSubfolder) {
						loadSegmentContent(folder, firstSubfolder);
					}
				});
			});
			if (primaryNavItems.length > 0) {
				primaryNavItems[0].click();
			}
		}

		function createSubfolders() {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li');
			primaryNavItems.forEach(navItem => {
				const folder = navItem.querySelector('a').textContent;
				const subfolderList = navItem.querySelector('ul');
				if (segments[folder]) {
					segments[folder].forEach(subfolder => {
						const li = document.createElement('li');
						const a = document.createElement('a');
						li.classList.add('sub-option');
						a.classList.add('sub-a');
						a.href = "#";
						a.textContent = subfolder;
						li.appendChild(a);
						subfolderList.appendChild(li);
						a.addEventListener('click', () => {
							loadSegmentContent(folder, subfolder);
							subfolderList.style.display = 'none';
							subfolderList.style.visibility = 'hidden';
							subfolderList.style.opacity = '0';
							subfolderList.style.transform = 'translateY(-10px)';
							highlightSelectedFolder(navItem.querySelector('a'));
						});
					});
					navItem.addEventListener('mouseenter', () => {
						subfolderList.style.display = 'block';
						subfolderList.style.visibility = 'visible';
						subfolderList.style.opacity = '1';
						subfolderList.style.transform = 'translateY(0)';
					});

					navItem.addEventListener('mouseleave', () => {
						subfolderList.style.visibility = 'hidden';
						subfolderList.style.opacity = '0';
						subfolderList.style.transform = 'translateY(-10px)';
					});
				}
			});
		}

		
		function loadSegmentContent(folder, subfolder) {
			const wikiContent = document.getElementById('wiki-content');
			const cachedContent = preloadedContent[folder] && preloadedContent[folder][subfolder];
			if (cachedContent) {
				wikiContent.innerHTML = "<div class='top-gap'></div>" + cachedContent + "<div class='bottom-gap'></div>";
				addImageClickListeners();
			} else {
				const url = `./wiki/${folder}/${subfolder}.html`;
				fetch(url)
					.then(response => {
						if (!response.ok) {
							throw new Error('Network response was not ok ' + response.statusText);
						}
						return response.text();
					})
					.then(data => {
						wikiContent.innerHTML = "<div class='top-gap'></div>" + data + "<div class='bottom-gap'></div>";
						addImageClickListeners();
					})
					.catch(error => {
						console.error('Fetch Failed:', error);
						wikiContent.innerHTML = '<h2>Error loading content for ' + folder + ' - ' + subfolder + '</h2>';
					});
			}
		}
				
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
		
		function addImageClickListeners() {
			const images = document.querySelectorAll('img');
			images.forEach(function(img) {
				img.style.cursor = 'pointer';
				img.addEventListener('click', function() {
					const imageUrl = img.src;
					openImageInNewTab(imageUrl);
				});
			});
		}

		function openImageInNewTab(imageUrl) {
			const form = document.createElement('form');
			form.method = 'POST';
			form.action = './image_display/image_display.php';
			form.target = '_blank';
			const input = document.createElement('input');
			input.type = 'hidden';
			input.name = 'imgSrc';
			input.value = imageUrl;
			form.appendChild(input);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}

		preloadSegments();
		loadPrimaryFolders();
		createSubfolders();
	
    </script>
</body>
</html>
