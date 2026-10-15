<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = './permission.json';
    $json = file_get_contents($file);
    $data = json_decode($json);

    if ($data === null) {
        die('Invalid JSON');
    }

    $data->allow_games = !$data->allow_games;
    $newData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($file, $newData);

    if ($result === false) {
        echo 'something went wrong<br>';
    } else {
        echo 'success';
    }
}
