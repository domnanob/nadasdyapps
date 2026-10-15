<?php

require_once __DIR__ . '/../../includes/database.php';
$conn = kepzesi_database_connection('higherorlowergame');
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_GET['mode'])) {
    $result = changeLeaderboard($_GET['mode']);
    $place = 0;

    foreach ($result->fetchAll() as $row) {
            $place++;
            $modeIcon = $row['mode'] === 'spotify'
                ? "<i title='Spotify mód' aria-label='Spotify mód' style='color: #1DB954;' class='fa-brands fa-spotify'></i>"
                : "<i title='Országok mód' aria-label='Országok mód' style='color: #67edff;' class='fa-solid fa-earth-americas'></i>";

            echo "<tr>";
            echo "<td>" . $place . "</td>";
            echo "<td>";
            echo "<div class='userCountry text-left'>";
            echo "<img src='https://purecatamphetamine.github.io/country-flag-icons/3x2/" . $row['country'] . ".svg'>";
            echo "<span>" . $row['username'] . "</span>";
            echo "</div>";
            echo "</td>";
            echo "<td>" . $row['score'] . "</td>";
            echo "<td>" . $modeIcon . "</td>";
            echo "</tr>";
    }
    if ($place === 0) {
        echo "<tr><td colspan='4' class='text-center'>Még nincs megjeleníthető eredmény.</td></tr>";
    }

}
