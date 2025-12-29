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
    static $conn = null;
    global $DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME;
    if ($conn === null) {
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    $result = $conn->query($query);
    if ($result === false) {
        die("Query failed: " . $conn->error);
    }
    if (!$return_value) {
        return $result === true;
    }
    $data = [];
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->free();
    }
    return $data;
}

?>

