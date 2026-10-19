<?php
$databasePath = __DIR__ . "/iskola.sqlite";
$conn = new PDO("sqlite:" . $databasePath);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
