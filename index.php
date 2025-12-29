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
	$specialFiles = getFilePaths('./gallery/Tarot/Special');
	$allImageFiles = array_merge($dragonFiles, $paragonFiles, $arbiterFiles, $specialFiles);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pandora Portal</title>
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
	<link rel="stylesheet" href="CSS/mainpageCSS.css">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="page-body">
    <header>
        <div class="pandora-header">
            <a href="index.php">
                <img src="./images/PandoraHeader.webp" alt="Page Header">
            </a>
        </div>
    </header>
    <main id="index-main">
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
			<!-- "https://www.ArchDragonStore.ca" -->
			<a href="https://www.archdragonstore.ca" target="_blank" class="card" style="background-image: url('./gallery/Displays/Banners/Treasure Trove.webp');">
				<div class="overlay overlay-orange">
					<span>Store</span>
				</div>
				<span class="hover-span hover-span-gold">Store</span>
			</a>
			<div id="image-cycler" class="card"></div>
		</div>
	</main>
    <footer>
		<a href="https://discord.gg/WXWJw9QYzZ" target="_blank" class="discord-button"></a>
        <p>&copy; 2024 Pandora Portal. All rights reserved.</p>
    </footer>
	<script>
        const allFiles = <?php echo json_encode($allImageFiles); ?>;
    </script>
    <script src="scripts/cycler.js"></script>
	<script src="scripts/screensizeWarning.js"></script>
</body>
</html>
