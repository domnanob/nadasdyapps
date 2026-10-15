<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon"
    href="https://www.ntszki.hu/_next/image?url=https%3A%2F%2Fvas-nadasdy.cms.intezmeny.edir.hu%2Fuploads%2Fthumbnail_Nadasdy_Logo_2020_Technikum_KOZEPES_bec1a30946.png&w=256&q=90">
  <script src="https://kit.fontawesome.com/bb8ffdf1b9.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style.css">
  <title>Ujjkoptató játék</title>
</head>

<body>
  <a href="../" class="position-absolute top-0 start-0 p-4" style="color: #fff; font-size:20px;"><i class="fa-solid fa-arrow-left"></i></a>
  <div class="container" id="gameContainer" hidden>
    <div>
      <div class="container" style="flex-direction: column;">

      </div>
    </div>
    <div class="container">
      <div class="centered-div">
        <div class="d-flex justify-content-center position-relative">
          <div id="timer" class="display-4" style="font-size: 2em; font-weight: bold; color:#9B7CA6">
            Hátralévő idő: 15s
          </div>
          <div id="toastContainer" class="position-absolute"></div>
        </div>
        <br>
        <h1 id="scoreTxt" class="text-center display-4" style="color:#9B7CA6"></h1>
        <br>
        <div class="grid">
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
          <div class="cell"></div>
        </div>
      </div>
    </div>
  </div>

  <div id="startGameContainer" class="container text-center">

    <div class="btnsContainer">
      <div class="col">
        <div class="row">
          <button type="button" id="startGameBtn" class="mb-3 btns startBtn"><i class="fa-solid fa-play"></i>
            Indítás</button>
          <!-- <p id="highscore" class="ms-2"></p> -->
        </div>
        <div class="row">
          <button type="button" id="leaderboardBtn" class="btns" onclick="openLeaderboard()"><i
              class="fa-solid fa-trophy"></i> Ranglista</button>
          <!--         <a href="leaderboard.php">
          <button type="button" id="leaderboardBtn" class="btn btn-lg btns">Ranglista</button>
        </a> -->
        </div>
      </div>
    </div>
  </div>

  <div id="endGameContainer" class="container text-center" hidden>
    <div class="row justify-content-center">
      <div class="col">
        <button type="button" id="restartGameBtn" class="btns startBtn mb-2"
          style="background-color:#1DB954; color:white;">Vissza a menübe</button>
        <p id="currentScore" style="color:#9B7CA6"></p>
        <p style="color:#9B7CA6">A sebessége: <span id="cpmValue">0</span> CPM</p>
      </div>
    </div>
  </div>

  <div id="leaderboardContainer" class="container text-center" hidden>
    <div class="col">
      <h1 class="my-4 text-center"><span style="color:#1ed760; text-shadow: 0 0 17px #1ed760">Ranglista</span></h1>
      <table class="table" id="leaderboardTable">
        <!--         <thead>
          <tr>
            <th scope="col">Rank</th>
            <th scope="col">Név</th>
            <th scope="col">Pont</th>
          </tr>
        </thead> -->
        <tbody id="leaderboardBody">

        </tbody>
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



  <script src="script.js"></script>
</body>

</html>