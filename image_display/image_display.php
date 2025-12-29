<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Display</title>
    <link rel="stylesheet" href="image_display.css">
	<link rel="icon" type="img/ico" href="../images/favicon.ico">
</head>
<body>
	<div id="hover-area">
		<div id="sidebar">
			<button class='selected' data-color='#000000' style="background-color: #000000;"></button>
			<button data-color='#FFFFFF' style="background-color: #FFFFFF;"></button>
			<button data-color='#8B0000' style="background-color: #8B0000;"></button>
			<button data-color='#1A2E5C' style="background-color: #1A2E5C;"></button>
			<button data-color='#006400' style="background-color: #006400;"></button>
			<button data-color='#4B0082' style="background-color: #4B0082;"></button>
			<!--<button class='hide-button' style="background-color: gray;" onclick="toggleSidebar()"></button>-->
		</div>
	</div>
    <div id="content">
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $imgSrc = htmlspecialchars($_POST["imgSrc"], ENT_QUOTES, 'UTF-8');
                echo "<img id='displayedImage' src='$imgSrc' alt='Displayed Image'>";
            } else {
                echo "<p>No image data received.</p>";
            }
        ?>
    </div>

    <script>
		const buttons = document.querySelectorAll('#sidebar button');
		const sidebar = document.getElementById('sidebar');
		const sidebarWidth = 50;
		buttons.forEach(button => {
			button.addEventListener('click', function () {
				buttons.forEach(btn => btn.classList.remove('selected'));
				button.classList.add('selected');
				document.body.style.backgroundColor = button.getAttribute('data-color');
			});
		});
		function toggleSidebar() {
			if (sidebar.classList.contains('show')) {
				sidebar.classList.remove('show');
			} else {
				sidebar.classList.add('show');
			}
		}
    </script>
</body>
</html>
