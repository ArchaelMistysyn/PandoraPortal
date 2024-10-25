<!DOCTYPE html>
<html lang="en">
<?php 
    define('BANNER_HTML_1', '<div class="inner-content"><img class="banner-image" src="./gallery/Displays/Banners/Pandora Awoken.webp" alt="Pandora Awoken"></div>'); // Wiki Page
    define('BANNER_HTML_2', '<div class="inner-content"><img class="banner-image" src="./gallery/Displays/Banners/Dragon\'s Spire.webp" alt="Dragon\'s Spire"></div>'); // Character Page
    define('BANNER_HTML_3', '<div class="inner-content"><img class="banner-image" src="./gallery/Displays/Banners/Butterfae Sanctuary.webp" alt="Butterfae Sanctuary"></div>'); // Gallery Page
    define('BANNER_HTML_4', '<div class="inner-content"><img class="banner-image" src="./gallery/Displays/Banners/Abyss.webp" alt="Abyss"></div>'); // Leaderboard Page
    define('BANNER_HTML_5', '<div class="inner-content"><img class="banner-image" src="./gallery/Displays/Banners/Treasure Trove.webp" alt="Infuse Banner"></div>'); // Store Page

	function getFilePaths($directory) {
		$files = [];
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		foreach ($scanned_directory as $file) {
			$files[] = $directory . '/' . $file;
		}
		return $files;
	}
	
	$paragonFiles = getFilePaths('./gallery/Tarot/Paragon');
	$arbiterFiles = getFilePaths('./gallery/Tarot/Arbiter');
	$allTarotFiles = array_merge($paragonFiles, $arbiterFiles);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal</title>
    <link rel="stylesheet" href="pandoraCSS.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>    
    <!-- Main Content Section -->
    <main id="homepage">
		<div id="main-select">
			<div class="pandora-header">
				<a href="index.php">
					<h1><img src="./images/PandoraHeader.webp" alt="Page Header"></h1>
				</a>
			</div>
			<nav id="page-select">
				<ul>
					<li><button class="image-button medium-button" role="button"><a href="wiki.php" data-banner="<?php echo htmlspecialchars(BANNER_HTML_1); ?>">Wiki</a></button></li>
					<li><button class="image-button medium-button" role="button"><a href="characters.php" data-banner="<?php echo htmlspecialchars(BANNER_HTML_2); ?>">Character</a></button></li>
					<li><button class="image-button medium-button" role="button"><a href="gallery.php" data-banner="<?php echo htmlspecialchars(BANNER_HTML_3); ?>">Gallery</a></button></li>
					<li><button class="image-button medium-button" role="button"><a href="ranking.php" data-banner="<?php echo htmlspecialchars(BANNER_HTML_4); ?>">Ranking</a></button></li>
					<li><button class="image-button medium-button" role="button"><a href="https://www.ArchDragonStore.ca" target="_blank" data-banner="<?php echo htmlspecialchars(BANNER_HTML_5); ?>">Store</a></button></li>
				</ul>
			</nav>
			<div id="tarot-cyclic"></div>
			<div id="footer">
				<button class="glow-button medium-button" role="button"><a href="https://discord.gg/WXWJw9QYzZ" target="_blank" class="join-button">Join Now</a></button>
				<div class="copyright">
					<p>&copy; 2024 Pandora Portal. All rights reserved.</p>
				</div>
			</div>
		</div>
        <div id="banner-container">
			<div id="banner-content"></div>
        </div>
    </main>   
	<script>
        const bannerLinks = document.querySelectorAll('#page-select button a');
        const bannerContentDiv = document.getElementById('banner-content');
		const tarotCyclic = document.getElementById('tarot-cyclic');
		const tarotFiles = <?php echo json_encode($allTarotFiles); ?>;
		let defaultBanner = '<div class="default-banner inner-content"><div"></div></div>';
		let activeBanner = defaultBanner;
		let previousImage = null;
		let hoverTimeout;
		let resetTimeout;

		function isHoveringOverAnyButton() {
			return Array.from(bannerLinks).some(link => link.matches(':hover'));
		}
		
		function updateBannerContent(newContent) {
			bannerContentDiv.innerHTML = newContent;
		}

		function setTarot() {
			let availableFiles = [...tarotFiles];
			if (previousImage) {
				availableFiles = availableFiles.filter(file => file !== previousImage);
			}
			const randomImage = getRandomImage(availableFiles);
			tarotCyclic.style.backgroundImage = `url("${randomImage}")`;
			previousImage = randomImage;
			const imageNameWithExtension = randomImage.split('/').pop();
			let imageName = decodeURIComponent(imageNameWithExtension.replace(/\.[^/.]+$/, ""));
			if (imageName.toLowerCase().indexOf('cardback') === -1) {
				let productName = imageName.split(' - ').pop();
				productName = productName.replace(/ /g, '-').replace(/,/g, '');
				const href = `https://archdragonstore.ca/products/framed-poster-${productName.toLowerCase()}`;
				tarotCyclic.innerHTML = '<a id="tarot-link" href="' + href + '" target="_blank" style="display:block; width:100%; height:100%;"></a>';
			} else {
				tarotCyclic.innerHTML = '';
			}
		}

		function getRandomImage(files) {
			return files[Math.floor(Math.random() * files.length)];
		}
		
		bannerLinks.forEach(link => {
			link.addEventListener('mouseenter', function() {
				clearTimeout(hoverTimeout);
				clearTimeout(resetTimeout);
				hoverTimeout = setTimeout(() => {
					const newBannerContent = this.getAttribute('data-banner');
					bannerContentDiv.innerHTML = newBannerContent;
				}, 20);
			});

			link.addEventListener('mouseleave', function() {
				clearTimeout(hoverTimeout);
				resetTimeout = setTimeout(() => {
					if (!isHoveringOverAnyButton()) {
						bannerContentDiv.innerHTML = defaultBanner;
					}
				}, 20);
			});
		});
		
		bannerContentDiv.innerHTML = defaultBanner;
		setTarot();
		setInterval(setTarot, 5000);
    </script>
</body>
</html>
