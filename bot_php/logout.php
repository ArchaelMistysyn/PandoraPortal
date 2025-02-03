<?php
session_start();
setcookie("player_id", "", time() - 3600, "/");
session_destroy();
header("Location: ../play.php");
exit();
?>
