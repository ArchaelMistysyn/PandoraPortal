<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_input']) && ctype_digit($_GET['search_input'])) {
    $ch = curl_init('https://pandoraportal.ca/characters.php');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['search_input' => $_GET['search_input']],
        CURLOPT_RETURNTRANSFER => true
    ]);
    echo $response = curl_exec($ch);
    http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));
    curl_close($ch);
} else {
    http_response_code(400);
    exit('Invalid or missing input.');
}
