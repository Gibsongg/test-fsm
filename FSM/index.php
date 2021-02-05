<?php
include ('../vendor/autoload.php');
include_once('./services/DrawService.php');

$draw = new DrawService();

$_POST = json_decode(file_get_contents('php://input'), true);


if ($_POST) {

    echo json_encode([
        'path' => $draw->fromConstructor($_POST['places'], $_POST['transitions']) . '?t=' . time(),
        'php' => $draw->createPhpScheme($_POST)
    ], JSON_THROW_ON_ERROR);
}
