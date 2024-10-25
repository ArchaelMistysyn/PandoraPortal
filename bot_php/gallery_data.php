<?php
header('Content-Type: application/json');

if (!isset($_GET['action'])) {
    echo json_encode(['error' => 'Action parameter is missing']);
    exit;
}
$action = $_GET['action'];

switch ($action) {
    case 'getPrimaryFolders':
        getPrimaryFolders();
        break;
    case 'getSubfolders':
        if (!isset($_GET['folder'])) {
            echo json_encode(['error' => 'Folder parameter is missing']);
            exit;
        }
        getSubfolders($_GET['folder']);
        break;
    case 'getImages':
        if (!isset($_GET['path'])) {
            echo json_encode(['error' => 'Path parameter is missing']);
            exit;
        }
        getImages($_GET['path']);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getPrimaryFolders() {
    $folders = array_diff(scandir('../gallery'), array('.', '..'));
    echo json_encode(['folders' => $folders]);
}

function getSubfolders($folder) {
	$folderPath = realpath("../gallery/$folder");
    $subfolders = array_diff(scandir($folderPath), array('.', '..'));
    echo json_encode(['subfolders' => $subfolders]);
}

function getImages($path) {
	$imagePath = realpath("../gallery/$path");
    $images = array_diff(scandir($imagePath), array('.', '..'));
    echo json_encode(['images' => $images]);
}
?>
