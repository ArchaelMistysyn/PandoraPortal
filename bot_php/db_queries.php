<?php
$webEnvFile = "/home/kylep910/PandoraPortalEnv/.env";
$localEnvFile = "./nonpublic/.env";

$envFile = file_exists($localEnvFile) ? $localEnvFile : $webEnvFile;
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}
$DB_SERVER   = getenv($envFile === $localEnvFile ? 'DB_SERVER_REMOTE' : 'DB_SERVER_LIVE');
$DB_USERNAME = getenv('DB_USERNAME');
$DB_PASSWORD = getenv('DB_PASSWORD');
$DB_NAME = getenv('DB_NAME');

function run_query($query, $return_value = true) {
    global $DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME;
    $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($query);
    if (!$return_value) {
        $success = $result === true;
        $conn->close();
        return $success;
    }
    $data = [];
    if ($result !== true) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    $conn->close();
    return $data;
}
?>

