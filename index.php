<!DOCTYPE html>
<html lang="en">
<?php
	function getFilePaths($directory) {
		$files = [];
		$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		foreach ($scanned_directory as $file) {
			$filePath = $directory . '/' . $file;
			if (is_file($filePath)) {
				$files[] = $filePath;
			}
		}
		return $files;
	}
	$paragonFiles = getFilePaths('./gallery/Tarot/Paragon');
	$arbiterFiles = getFilePaths('./gallery/Tarot/Arbiter');
	$dragonFiles = getFilePaths('./gallery/Bosses/Dragon');
	$allImageFiles = array_merge($dragonFiles, $paragonFiles, $arbiterFiles);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pandora Portal</title>
    <link rel="stylesheet" href="mainpageCSS.css">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
    <header>
        <div class="pandora-header">
            <a href="index.html">
                <img src="./images/PandoraHeader.webp" alt="Page Header">
            </a>
        </div>
    </header>
    <div class="main-menu">
		<div class="grid-layout">
			<a href="wiki.php" class="card" style="background-image: url('./gallery/Displays/Banners/Pandora Awoken.webp');">
				<div class="overlay overlay-purple">
					<span>Wiki</span>
				</div>
				<span class="hover-span hover-span-amethyst">Wiki</span>
			</a>
			<a href="characters.php" class="card" style="background-image: url('./gallery/Displays/Banners/Dragon\'s Spire.webp');">
				<div class="overlay overlay-red">
					<span>Character</span>
				</div>
				<span class="hover-span hover-span-ruby">Character</span>
			</a>
			<a href="gallery.php" class="card" style="background-image: url('./gallery/Displays/Banners/Butterfae Sanctuary.webp');">
				<div class="overlay overlay-blue">
					<span>Gallery</span>
				</div>
				<span class="hover-span hover-span-azure">Gallery</span>
			</a>
			<a href="ranking.php" class="card" style="background-image: url('./gallery/Displays/Banners/Abyss.webp');">
				<div class="overlay overlay-pink">
					<span>Rankings</span>
				</div>
				<span class="hover-span hover-span-pink">Rankings</span>
			</a>
			<a href="https://www.ArchDragonStore.ca" target="_blank" class="card" style="background-image: url('./gallery/Displays/Banners/Treasure Trove.webp');">
				<div class="overlay overlay-orange">
					<span>Store</span>
				</div>
				<span class="hover-span hover-span-gold">Store</span>
			</a>
			<div id="image-cycler" class="card"></div>
		</div>
	</div>
    <footer>
		<a href="https://discord.gg/WXWJw9QYzZ" target="_blank" class="discord-button">Discord</a>
        <p>&copy; 2024 Pandora Portal. All rights reserved.</p>
    </footer>
	<script>
        const imageCycler = document.getElementById('image-cycler');
		let allFiles = <?php echo json_encode($allImageFiles); ?>;
		allFiles = allFiles.filter(file => !file.toLowerCase().includes('cardback'));
        let previousImage = null;

        function setImageCycler() {
            let availableFiles = allFiles.filter(file => file !== previousImage);
            const randomImage = getRandomImage(availableFiles);
            imageCycler.style.backgroundImage = `url("${randomImage}")`;
            previousImage = randomImage;
            // Assign store links
            const imageNameWithExtension = randomImage.split('/').pop();
            let imageName = decodeURIComponent(imageNameWithExtension.replace(/\.[^/.]+$/, ""));
            if (imageName.toLowerCase().indexOf('dragon') !== -1) {
				imageCycler.classList.remove('portrait');
				let productName = imageName.replace(/ /g, '-').toLowerCase();
				const storelink = "https://archdragonstore.ca/products/" + productName;
				imageCycler.innerHTML = "<a id='tarot-link' href='" + storelink + "' target='_blank' style='display:block; width:100%; height:100%;'></a>";
			} else {
				imageCycler.classList.add('portrait');
				let productName = imageName.split(' - ').pop().replace(/ /g, '-').replace(/,/g, '').toLowerCase();
				const storelink = "https://archdragonstore.ca/products/framed-poster-" + productName;
				imageCycler.innerHTML = "<a id='tarot-link' href='" + storelink + "' target='_blank' style='display:block; width:100%; height:100%;'></a>";
			}
		}

        function getRandomImage(files) {
            return files[Math.floor(Math.random() * files.length)];
        }

        setImageCycler();
        setInterval(setImageCycler, 5000);
    </script>
</body>
</html>
