<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/bb8ffdf1b9.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Magasabb vagy alacsonyabb?</title>

    <?php

    session_start();

    require_once __DIR__ . '/../includes/database.php';
    $conn = kepzesi_database_connection('higherorlowergame');
    include("server/functions.php");


    $user_data = check_login($conn);
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    ?>

</head>

<body class="gameBody">
    <div class="loading-screen">
        Betöltés...
    </div>
            <div class="vert-line"></div>

    <div class="gameEnd" hidden>
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-12 gameEndCard">
                    <h1 id="gameEndTitle">Vége a játéknak</h1>
                    <p id="gameEndText">Pontszámod: <span id="finalScore"></span> pont!<?php echo $user_data['is_guest'] ? ' Vendégként a pontszámaid nem kerülnek mentésre.' : ''; ?></p>
                    <button class="butn" id="playAgainBtn">Új játék</button>
                    <button class="butn" id="backToMenu">Vissza a menübe</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid" id="gameDiv">
        <div class="row text-center">
            <div class="col-6 p-0 itemcard">
                <img class="selectDisable" id="imgL">
                <div class="text">
                    <p class="artistName" id="artistL">Előadó</p>
                    <p class="has">Havi hallgatók száma:</p>
                    <p class="artistValue" id="valueL">
                        <span id="valueLNum">50</span>
                        <span class="valueText">millió</span>
                    </p>
                    <p class="unit"></p>
                </div>
            </div>
            <div class="col-6 p-0 itemcard">
                <img class="selectDisable" id="imgR" onerror="this.onerror = null; this.src='fallback.png'">
                <div class="text">
                    <p class="artistName" id="artistR">Előadó</p>
                    <p class="has">Havi hallgatók száma:</p>
                    <p class="artistValue" id="valueR" hidden>
                        <span id="valueRNum">50</span>
                        <span class="valueText">millió</span>
                    </p>

                    <div class="buttons">
                        <div class="row text-center ">
                            <button class="butn" id="higherBtn">Több<i class="fa-solid fa-caret-up"></i></button>
                        </div>
                        <div class="row text-center ">
                            <button class="butn" id="lowerBtn">Kevesebb<i class="fa-solid fa-caret-down"></i></button>
                        </div>
                    </div>
                    <p class="unit"></p>
                </div>
            </div>
            <div class="answerbox">
                <p class="vs">vagy</p>
                <i class="fa-solid fa-check correct" hidden></i>
                <i class="fa-solid fa-xmark incorrect" hidden></i>
            </div>
        </div>
        <div class="scorebox">
            <p class="score">Pontszám: <span id="scoreValue">0</span></p>
        </div>
    </div>

    <input type="hidden" name="score" id="scoreInput" value="0">


    <script>
        const userId = <?php echo json_encode($user_data) ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="game.js"></script>


</body>

</html>