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
    <title>Pandora Portal - Gallery</title>
    <link rel="stylesheet" href="pandoraCSS.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
			<a href="index.php">
				<img src="./images/icon.png" alt="Website Icon">
				<h1>Gallery</h1>
			</a>
        </div>
		<nav id="primary-nav">
			<ul class="sf-menu primary-nav">
				<li class="primary-option"><a href="#">Tarot</a><ul></ul></li>
				<li class="primary-option"><a href="#">Displays</a><ul></ul></li>
				<li class="primary-option"><a href="#">Bosses</a><ul></ul></li>
				<li class="primary-option"><a href="#">Gear</a><ul></ul></li>
				<li class="primary-option"><a href="#">Items</a><ul></ul></li>
				<li class="primary-option"><a href="#">Icons</a><ul></ul></li>
			</ul>
		</nav>
        <nav id="page-nav">
            <ul>
				<li><a href="wiki.php">Wiki</a></li>
                <li><a href="characters.php">Character</a></li>
                <li><a href="gallery.php" class="selected">Gallery</a></li>
                <li><a href="ranking.php">Ranking</a></li>
                <li><a href="https://www.ArchDragonStore.ca" target="_blank">Store</a></li>
            </ul>
        </nav>
    </header>
    <!-- Main Content Section -->
    <main class="no-footer">
        <div class="content-container">
			<div id="gallery-glass">
				<div id="gallery-content">
					<div id="preview-box"></div>
					<div id="info-box" class="ease-load">
						<div class="style-line"></div>
						<div id="color-bar" class="color-bar">
							<button class="color-button" data-color="#FFFFFF"></button>
							<button class="selected color-button" data-color="#000000"></button>
							<button class="color-button" data-color="#8B0000"></button>
							<button class="color-button" data-color="#1A2E5C"></button>
							<button class="color-button" data-color="#006400"></button>
							<button class="color-button" data-color="#4B0082"></button>
						</div>
						<h2 id="image-name"></h2>
						<p id="image-dimensions"></p>
						<a id="shop-link" href="#" target="_blank"><button class="glow-button small-button" role="button">View Store</button></a>
						<div id="expand-button" class='bottom-button toggle-button toggle-hover'>Expand Image</div>
					</div>
				</div>
			</div>
            <div class="fullsize-container"><div id="fullsize-image"></div></div>
        </div>
    </main>
	<script>
		const colorButtons = document.querySelectorAll('.color-button');
		const fullImageDisplay = document.getElementById('fullsize-image');
		const expandButton = document.getElementById('expand-button');
		let currentImageSrc = '';
		const preloadedImages = new Set();
		const subfolders = {
			"Tarot": [
				{"label": "Paragon", "folder": "Tarot", "subfolders": ["Paragon"]}
			],
			"Displays": [
				{"label": "Banners", "folder": "Displays", "subfolders": ["Banners"]},
				{"label": "Locations", "folder": "Displays", "subfolders": ["Locations"]}
			],
			"Bosses": [
				{"label": "Fortress", "folder": "Bosses", "subfolders": ["Fortress"]},
				{"label": "Dragon", "folder": "Bosses", "subfolders": ["Dragon"]}
			],
			"Gear": [
				{"label": "Sovereign", "folder": "Gear", "subfolders": ["Sovereign"]},
				{"label": "Weapons", "folder": "Weapons", "subfolders": ["Knight", "Ranger", "Mage", "Assassin", "Weaver", "Rider", "Summoner"]},
				{"label": "Rings", "folder": "Rings", "subfolders": ["Signets", "Primordial", "Path", "Fabled", "Sovereign"]},
				{"label": "Equipment", "folder": "Equipment", "subfolders": ["Armour", "Amulet", "Wings", "Crest", "Gem"]},
				{"label": "Pact", "folder": "Misc", "subfolders": ["Pact"]},
			],
			"Items": [
				{"label": "Lotus", "folder": "Items", "subfolders": ["Lotus"]},
				{"label": "Gems", "folder": "Items", "subfolders": ["Gemstones", "Fragments"]},
				{"label": "Misc", "folder": "Items", "subfolders": ["Misc", "Skulls", "Hearts", "Fae Cores"]}
			],
			"Icons": [
				{"label": "Symbols", "folder": "Icons", "subfolders": ["Classes", "Elements", "BossTypes"]},
				{"label": "Misc", "folder": "Icons", "subfolders": ["Misc", "Stars", "Pearls"]}
			]
		};

		colorButtons.forEach(button => {
			button.addEventListener('click', function () {
				colorButtons.forEach(btn => btn.classList.remove('selected'));
				button.classList.add('selected');
				fullImageDisplay.style.backgroundColor = button.getAttribute('data-color');
			});
		});
		
		function highlightSelectedFolder(navItem) {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(a => a.classList.remove('active'));
			navItem.classList.add('active');
		}
	
		async function preloadSegments() {
			const allPaths = Object.keys(subfolders).flatMap(folder => 
				subfolders[folder].flatMap(subfolder => {
					if (typeof subfolder === 'string') {
						return `${folder}/${subfolder}`;
					} else if (typeof subfolder === 'object' && subfolder !== null) {
						return subfolder.subfolders.map(subSub => `${subfolder.folder}/${subSub}`);
					}
				})
			);
			const maxConcurrentRequests = 5;
			for (let i = 0; i < allPaths.length; i += maxConcurrentRequests) {
				const batch = allPaths.slice(i, i + maxConcurrentRequests).map(path => 
					fetch(`./bot_php/gallery_data.php?action=getImages&path=${encodeURIComponent(path)}`)
						.then(response => {
							if (!response.ok) {
								throw new Error(`HTTP error! status: ${response.status}`);
							}
							return response.json();
						})
						.then(data => {
							const imagesArray = Array.isArray(data.images) ? data.images : Object.values(data.images);
							if (imagesArray.length) {
								imagesArray.forEach(image => {
									const imgSrc = `https://pandoraportal.ca/gallery/${path}/${encodeURIComponent(image)}`;
									preloadedImages.add(imgSrc);
									const img = new Image();
									img.src = imgSrc;
								});
							}
						})
						.catch(error => console.error(`Error loading images from path: ${path}`, error))
				);
				try {
					await Promise.all(batch);
				} catch (error) {
					console.error('Error preloading images:', error);
				}
			}
			console.log('All images preloaded successfully.');
		}

		function loadPrimaryFolders() {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(navItem => {
				navItem.addEventListener('click', () => {
					highlightSelectedFolder(navItem);
					const folder = navItem.textContent;
					const firstSubfolder = subfolders[folder] ? subfolders[folder][0] : null;
					if (firstSubfolder) {
						loadImages(folder, firstSubfolder);
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
				if (subfolders[folder]) {
					subfolders[folder].forEach(subfolder => {
						const li = document.createElement('li');
						const a = document.createElement('a');
						li.classList.add('sub-option');
						a.classList.add('sub-a');
						if (typeof subfolder === 'object' && subfolder.label && subfolder.folder && subfolder.subfolders) {
							a.href = "#";
							a.textContent = subfolder.label;
							li.appendChild(a);
							subfolderList.appendChild(li);
							a.addEventListener('click', () => {
								loadImages(subfolder.folder, subfolder);
								subfolderList.style.display = 'none';
								subfolderList.style.visibility = 'hidden';
								subfolderList.style.opacity = '0';
								subfolderList.style.transform = 'translateY(-10px)';
								highlightSelectedFolder(navItem.querySelector('a'));
							});
						}
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
		
		function flattenSubfolderPaths(folder, subfolder) {
			const result = [];
			subfolder.subfolders.forEach(subSub => {
				result.push(`${folder}/${subSub}`);
			});
			return result;
		}
		
		function loadImages(folder, subfolder) {
			const paths = flattenSubfolderPaths(folder, subfolder);
			const previewBox = document.getElementById('preview-box');
			previewBox.innerHTML = '';
			paths.forEach(path => {
				fetch(`./bot_php/gallery_data.php?action=getImages&path=${encodeURIComponent(path)}`)
					.then(response => response.json())
					.then(data => {
						console.log('Data received for path:', path, data);
						const imagesArray = Object.values(data.images).map(image => {
							const img = new Image();
							img.src = `./gallery/${path}/${encodeURIComponent(image)}`;
							img.dataset.imageName = image;
							return img;
						});
						imagesArray.forEach(image => {
							appendImage(previewBox, image);
						});
						if (imagesArray.length > 0) {
							displayFullSizeImage(imagesArray[0].src);
						}
					});
			});
		}

		function lazyLoadImage(img, src) {
			const observer = new IntersectionObserver((entries, observer) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						img.src = src;
						observer.unobserve(img);
					}
				});
			}, {
				rootMargin: "0px 0px 200px 0px"
			});
			observer.observe(img);
		}
		
		function appendImage(previewBox, image) {
			const img = new Image();
			const wrapper = document.createElement('div');
			wrapper.className = 'image-wrapper';
			img.src = image.src;
			if (preloadedImages.has(image.src)) {
				img.className = 'gallery-image';
			} else {
				const spinner = document.createElement('div');
				img.className = 'gallery-image lazy';
				img.alt = "Loading...";
				spinner.className = 'loading-spinner';
				wrapper.appendChild(spinner);
				lazyLoadImage(img, image.src);
				img.onload = () => {
					img.classList.remove('lazy');
					img.classList.add('lazy-loaded');
					spinner.remove();
				};
			}
			wrapper.appendChild(img);
			previewBox.appendChild(wrapper);
			img.onclick = () => displayFullSizeImage(img.src);
		}

		function displayFullSizeImage(src) {
			const fullImageDisplay = document.getElementById('fullsize-image');
			const infoBox = document.getElementById('info-box');
			const img = document.createElement('img');
			fullImageDisplay.innerHTML = '';
			img.src = src;
			currentImageSrc = src;
			fullImageDisplay.appendChild(img);
			document.querySelectorAll('.gallery-image').forEach(image => {
				image.classList.remove('selected-preview');
				if (image.dataset.src === src) {
					image.classList.add('selected-preview');
				}
			});
			img.onload = () => {
				updateInfoBox(img, src);
				infoBox.classList.remove('ease-load');
				void infoBox.offsetWidth;
				infoBox.classList.add('ease-load');
			};
		}
		
		function updateInfoBox(img, src) {
			const imageNameWithExtension = src.split('/').pop();
			let imageName = decodeURIComponent(imageNameWithExtension.replace(/\.[^/.]+$/, ""));
			const dimensions = `Original Image Size: ${img.naturalWidth}px, ${img.naturalHeight}px`;
			const shopLink = document.getElementById('shop-link');
			// Tier Naming
			if (src.includes('Weapons') || (src.includes('Equipment') && !src.includes('Sovereign'))) {
				const itemType = imageName.match(/[a-zA-Z]+/)[0];
				const tier = imageName.match(/\d+/)[0];
				imageName = `${itemType} (Tier ${tier})`;
			// Tarot Shop Links
			} else if (imageName.includes(' - ')) {
				let productName = imageName.split(' - ').pop();
				productName = productName.replace(/ /g, '-').replace(/,/g, '');
				shopLink.href = `https://archdragonstore.ca/products/framed-poster-${productName.toLowerCase()}`;
				shopLink.style.display = 'flex';
			} else if (imageName.includes('Dragon')) {
				let productName = imageName
				productName = productName.replace(/ /g, '-').replace(/,/g, '').toLowerCase();
				shopLink.href = `https://archdragonstore.ca/products/${productName}`;
				shopLink.style.display = 'flex';
			} else {
				shopLink.style.display = 'none';
			}
			document.getElementById('image-name').textContent = imageName;
			document.getElementById('image-dimensions').textContent = dimensions;
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
		
		fullImageDisplay.addEventListener('click', function() {
			openImageInNewTab(currentImageSrc);
		});

		expandButton.addEventListener('click', function() {
			openImageInNewTab(currentImageSrc);
		});
		
		preloadSegments();
		loadPrimaryFolders();
		createSubfolders();
    </script>
</body>
</html>
