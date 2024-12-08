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
    <link rel="stylesheet" href="CSS/mainpageCSS.css">
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/storeCSS.css">
	<link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body id="page-body">
    <header id="header"></header>
    <main id="store-main">
        <div class="page-menu">
            <div class="grid-layout">
                <a onclick="loadCategory('Shirts')" class="card" style="background-image: url('./gallery/Displays/Banners/Pandora Awoken.webp');">
                    <div class="overlay overlay-purple">
                        <span>Shirts</span>
                    </div>
                    <span class="hover-span hover-span-amethyst">Shirts</span>
                </a>
                <a onclick="loadCategory('Posters')" class="card" style="background-image: url('./gallery/Displays/Banners/Dragon\'s Spire.webp');">
                    <div class="overlay overlay-red">
                        <span>Posters</span>
                    </div>
                    <span class="hover-span hover-span-ruby">Posters</span>
                </a>
                <a onclick="loadCategory('Mousepads')" class="card" style="background-image: url('./gallery/Displays/Banners/Butterfae Sanctuary.webp');">
                    <div class="overlay overlay-blue">
                        <span>Mousepads</span>
                    </div>
                    <span class="hover-span hover-span-azure">Mousepads</span>
                </a>
                <a onclick="loadCategory('Gift Cards')" class="card" style="background-image: url('./gallery/Displays/Banners/Abyss.webp');">
                    <div class="overlay overlay-pink">
                        <span>Gift Cards</span>
                    </div>
                    <span class="hover-span hover-span-pink">Gift Cards</span>
                </a>
                <a onclick="loadCategory('Other')" class="card" style="background-image: url('./gallery/Displays/Banners/Treasure Trove.webp');">
                    <div class="overlay overlay-orange">
                        <span>Other</span>
                    </div>
                    <span class="hover-span hover-span-gold">Other</span>
                </a>
                <div id="image-cycler" class="card"></div>
            </div>
        </div>
        <div id="backdrop" class="backdrop" onclick="closeBackdrop()"></div>
        <div id="content-display" class="content-display"></div>
    <main>
    <script>
        const allFiles = <?php echo json_encode($allImageFiles); ?>;
    </script>
    <script src="scripts/header.js"></script>
    <script src="scripts/store.js"></script>
    <script src="scripts/cycler.js"></script>
    <script src="scripts/screensizeWarning.js"></script>
</body>