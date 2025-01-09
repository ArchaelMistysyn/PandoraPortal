<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_input']) && ctype_digit($_GET['search_input'])) {
    $postData = ['search_input' => $_GET['search_input']];
    $ch = curl_init('https://pandoraportal.ca/characters.php');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    http_response_code($httpStatus);
    echo $response;
    exit;
}
http_response_code(400);
exit('Invalid or missing input.');
?>
