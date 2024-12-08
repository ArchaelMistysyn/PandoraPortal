<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pandora Portal - Gallery</title>
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/galleryCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
	<header id="header"></header>
	<main id="gallery-main">
    <div id="lightbox" class="lightbox hidden">
      <span id="lightbox-close" class="lightbox-close">&times;</span>
      <div class="lightbox-main-content">
        <img id="lightbox-image" src="" alt="Lightbox Image" />
        <div class="carousel-container">
          <button id="carousel-prev" class="carousel-control"><img src="/images/Icons/arrow-square-left-fill.png" alt="carousel left"></button>
          <div id="carousel" class="carousel">
          </div>
          <button id="carousel-next" class="carousel-control"><img src="/images/Icons/arrow-square-right-fill.png" alt="carousel left"></button>
        </div>
      </div>
    </div>
		<div id="gallery-main-container">
			<div id="tab-menu">
        <input type="text" id="filter-input" placeholder="Filter Content" oninput="filterItems()">
        <div class="tab-menu" id="tabMenu"></div>
      </div>
			<div id="gallery-content-container">
				<div id="tab-content">
          <div id="lightbox" class="lightbox hidden">
            <span id="lightbox-close" class="lightbox-close">&times;</span>
            <img id="lightbox-image" src="" alt="Lightbox Image" />
          </div>
        </div>
			</div>
		</div>
	</main>
	<script>
        const subcontentFolder = "galleryPages";
        const segments = {
            Bosses: ["Dragons", "Paragons"],
            Displays: ["Banners", "Locations"],
            Gear: ["Weapons", "Equipment", "Rings"],
            Icons: ["Items", "Thumbnails", "Misc"]
        };
  </script>
	<script src="scripts/header.js"></script>
	<script src="scripts/tabMenu.js"></script>
	<script src="scripts/galleryLightbox.js"></script>
  <script src="scripts/screensizeWarning.js"></script>
</body>
</html>
