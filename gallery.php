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
    <link rel="stylesheet" href="CSS/generalpageCSS.css">
    <link rel="stylesheet" href="CSS/galleryCSS.css">
    <link rel="icon" type="img/ico" href="./images/favicon.ico">
</head>
<body>
	<header id="header"></header>
    <main>
        <div class="content-container">
			<div id="gallery-content">
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
					<h2 id="image-name" class="highlight-text"></h2>
					<p id="image-dimensions"></p>
					<a id="shop-link" href="#" target="_blank"><button class="glow-button medium-button" role="button">View Store</button></a>
					<div id="expand-button" class='bottom-button'>Expand Image</div>
				</div>
			</div>
            <div class="fullsize-container"><div id="fullsize-image"></div></div>
        </div>
    </main>
	<script src="scripts/header.js"></script>
	<script src="scripts/galleryImages.js"></script>
</body>
</html>
