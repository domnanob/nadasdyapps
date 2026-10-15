<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amőba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<style>
    .cella {
        box-sizing: border-box;
        height: clamp(24px, 3vw, 44px);
        width: clamp(24px, 3vw, 44px);
        background-repeat: no-repeat;
        background-size: cover;
        text-align: center;
        justify-content: center;
        padding: 0;
    }

    #game_map {
        width: max-content;
        table-layout: fixed;
        border-collapse: collapse;
    }

    #game_map td {
        width: clamp(24px, 3vw, 44px);
        height: clamp(24px, 3vw, 44px);
        padding: 0;
    }

    .game-side-column {
        flex: 1 1 0;
        min-width: 0;
    }

    .game-map-column {
        flex: 0 0 auto;
        min-width: 0;
    }

    #customAlert {
        margin-left: 33.5%;
        top: 33.5%;
        position: fixed;
        z-index: 1000;
        display: none;
    }

    body {
        overflow: hidden;
    }

    .lbeventfield {
        min-width: 0;
        overflow-x: hidden;
        overflow-y: auto;
        max-height: 80vh;
        overflow-wrap: anywhere;
    }

    .event-history-entry {
        overflow-wrap: anywhere;
    }
</style>

<body data-bs-theme="dark">
    <a href="../" class="position-absolute top-0 start-0 p-4" style="color: #fff; font-size:20px;"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="container justify-content-center" id="startmenu">
                <legend>Játékbeállítások</legend>
                <div class="mb-3">
                    <label for="NumberInput" class="form-label">Tábla mérete</label>
                    <select id="NumberInput" class="form-select">
                        <option value="12" selected>Kicsi</option>
                        <option value="15">Közepes</option>
                        <option value="20">Nagy</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Select" class="form-label">Játékmód</label>
                    <select id="Select" class="form-select">
                        <option value="0">Események nélkül</option>
                        <option value="1" selected>Normál</option>
                        <option value="2">Káosz</option>
                    </select>
                </div>
                <button id="start" class="btn btn-primary">Indítás</button>
    </div>
    <div id="gameboard" style="display: none">
        <h3 id="cturn" style="top: 0.5vh; position: fixed; color: greenyellow;" class="w-100 text-center"></h3>
        <div class="row" style="margin-top: 4vh;">
            <div class="col text-center" id="x">
                <h4>X pontszáma: 0</h4>
            </div>
            <div class="col text-center" id="event">
                <h4>Aktuális esemény:<br><span id="eventname">Nincs</span></h4>
            </div>
            <div class="col text-center" id="o">
                <h4>O pontszáma: 0</h4>
            </div>
        </div>
        <br>
        <div class="row d-flex">
            <div class="col text-center lbeventfield game-side-column">
                <h5>X szerencsemező-eseményei:</h5>
                <div id="lbx"></div>
            </div>
            <div class="justify-content-center d-flex col game-map-column">
                <table class="table-bordered" id="game_map">
                </table>
            </div>
            <div class="col text-center lbeventfield game-side-column">
                <h5>O szerencsemező-eseményei:</h5>
                <div id="lbo"></div>
            </div>
        </div>
    </div>
    <div class="alert alert-success col-4" role="alert" id="customAlert">
        <h4 class="alert-heading text-center">Vége a játéknak!</h4>
        <h6 class="text-center">Végeredmény:</h6>
        <div class="row justify-content-between">
            <div class="col-3"></div>
            <div class="col"><a id="xfinalscore">X: 0</a></div>
            <div class="col"><a style="float:right;" id="ofinalscore">O: 0</a></div>
            <div class="col-3"></div>
        </div>
        <h6 class="text-center" id="winner">X győzött!</h6>
        <hr>
        <div class="d-flex justify-content-center">
            <button class="btn btn-success" id="newGame" onclick="window.location.reload()">Új játék</button>
        </div>
    </div>
    <script src="./game.js" type="module"></script>
</body>

</html>