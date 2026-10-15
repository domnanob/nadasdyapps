<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = json_decode(file_get_contents('./permission.json'));
    
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode($data));
}