<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simon Says</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.cdnfonts.com/css/arcade-classic" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/bb8ffdf1b9.js" crossorigin="anonymous"></script>
    <link href="https://fonts.cdnfonts.com/css/arcade-classic" rel="stylesheet">

</head>

<body>
    <!-- <a href="../" class="position-absolute top-0 start-0 p-4" style="color: #fff; font-size:20px;"><i class="fa-solid fa-arrow-left"></i></a>-->
    <h1 id="currentColor" class="text-center display-4"></h1>
    <div class="container" id="gameContainer">
        <div class="row row-cols-2 g-3">
            <div class="col text-end">
                <button id="blueBtn" type="button" class="btn btn-lg hover"></button>
            </div>
            <div class="col">
                <button id="greenBtn" type="button" class="btn btn-lg hover"></button>
            </div>
            <div class="col text-end">
                <button id="redBtn" type="button" class="btn btn-lg hover"></button>
            </div>
            <div class="col">
                <button id="yellowBtn" type="button" class="btn btn-lg hover"></button>
            </div>
        </div>
        <button type="button" id="centerCircle">
            <h2 id="round" class="text-center display-4"></h2>
        </button>
    </div>


    <div id="startGameContainer" class="container text-center">
        <div class="row">
            <div class="col">
                <button type="button" id="startGameBtn" class="btns mb-3">START GAME</button>
                <!-- <p id="highscore" class="ms-2"></p> -->
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button type="button" id="leaderboardBtn" class="btns" onclick="openLeaderboard()">LEADERBOARD</button>
            </div>
        </div>
    </div>

    <div id="endGameContainer" class="container text-center">
        <div class="row justify-content-center">
            <div class="col">
                <button type="button" id="restartGameBtn" class="btn btn-lg btns">BACK TO THE MENU</button>
                <p id="currentScore" class="ms-2"></p>
            </div>
        </div>
    </div>

    <div id="leaderboardContainer" class="container text-center leaderboardContainer" hidden>
        <div class="col">
            <h1 class="retro-text">Rang lista</h1>

            <table id="leaderboardTable" class="leaderboard">
            </table>
            <button class="btns" id="backBtn">Vissza</button>
        </div>
    </div>

    <div class="promptContainer text-center" data-bs-theme="dark" hidden>
        <h1>Add meg a neved!</h1>
        <input type="text" id="nameInput" class="form-control mb-3" placeholder="Név">
        <button type="button" id="submitNameBtn" class="btn btn-lg">Küldés</button>
    </div>

    <button id="fsBtn" onclick="goFullscreen()" class="btn fsBtn" style="color: #fff;"><i
            class="fa-solid fa-expand"></i></button>

    <script>
        let fsBtn = document.getElementById("fsBtn");


        function goFullscreen() {
            if (!document.fullscreenElement) {
                // Enter fullscreen

                fsBtn.innerHTML = '<i class="fa-solid fa-compress"></i>';
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen({ navigationUI: "hide" });
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } else {
                // Exit fullscreen
                fsBtn.innerHTML = '<i class="fa-solid fa-expand"></i>';

                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }
    </script>

    <!-- <div class="d-flex justify-content-center align-items-center fs-1 display-4">
        <p id="scoreLeader" class="mb-0"> </p>
        <span id="highscore" class="ms-2"></span>
    </div> -->

    <script src="script.js"></script>
</body>

</html>