<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_input']) && ctype_digit($_GET['search_input'])) {
    $_SESSION['post_data'] = ['search_input' => $_GET['search_input']];
    header('Location: ../characters.php');
    exit;
}
http_response_code(400);
exit('Invalid or missing input.');
?>
