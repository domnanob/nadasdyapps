import { Cell, Table, events, lbevents, GAME_MODES } from "./classes.js"

const map = document.getElementById("game_map")
const xselement = document.getElementById("x")
const oselement = document.getElementById("o")
const eventelement = document.getElementById("eventname")
const AlertBox = document.getElementById("customAlert")
const xfinalscore = document.getElementById("xfinalscore")
const ofinalscore = document.getElementById("ofinalscore")
const winner = document.getElementById("winner")
const lbx = document.getElementById("lbx")
const lbo = document.getElementById("lbo")
const cturn = document.getElementById("cturn")

const startbtn = document.getElementById("start")
const startmenu = document.getElementById("startmenu")
const select = document.getElementById("Select")
const numberinput = document.getElementById("NumberInput")
const gameboard = document.getElementById("gameboard")


var GAMEMODE;
var MATRIX_COLS;
var MATRIX_ROWS;
var matrix;
var tableobj;
var cells;
startbtn.addEventListener("click", () => {
    GAMEMODE = select.value
    MATRIX_COLS = parseInt(numberinput.value)
    MATRIX_ROWS = parseInt(numberinput.value)
    
    //MATRIX_ROWS = 12
    //MATRIX_COLS = 12
    matrix = generate_matrix(MATRIX_ROWS, MATRIX_COLS)
    console.log(matrix)
    place_matrix(matrix)
    startmenu.style.display = "none"
    gameboard.style.display = "block"
    cells = document.getElementsByClassName("cella")
    tableobj = new Table(game())
    
})

//event listener START
 //selectnek a valueja
var SCORE_X = 0
var SCORE_O = 0
var SINCE_LAST_EVENT = 0
var TURN = "X"
var LB_NEXT_TURN = ""
cturn.innerText = "Következik: " + TURN
var EVENT_DURATION = 0
var WIN = 5







//event listener END

function game() {
    let table = []
    for (const cell of cells) {
        table.push(new Cell(parseInt(cell.id.split("-")[0]), parseInt(cell.id.split("-")[1]), 0, true, true))
        cell.addEventListener("click", () => {
            var cell_pos = cell.id.split("-")
            var cellx = parseInt(cell_pos[0])
            var celly = parseInt(cell_pos[1])
            place(cellx, celly)
        })
    }
    return table
}



function place(rowAxis, colAxis) {
    insert(rowAxis, colAxis, TURN)
}

function generate_matrix(rows, cols) {
    //console.log(rows)
    let matrix = []
    for (var i = 0; i < rows; i++) {
        let row = new Array(cols)
        var asd = row.fill(0, 0, cols)
        console.log(asd)
        matrix.push(row.fill(0, 0, cols))
    }
    return matrix
}
function place_matrix(matrix) {
    let i = 0
    for (const rows of matrix) {
        map.innerHTML += "<tr id='row-" + i + "'>"
        //console.log(rows)
        for (const cols in rows) {
            document.getElementById("row-" + i).innerHTML += "<td><button class='btn cella' id='" + i + "-" + cols + "'></button></td>"
        }
        i++
    }

}

function insert(rowAxis, colAxis, item) {
    if (EVENT_DURATION > 0) {
        EVENT_DURATION -= 1
    }
    else if (EVENT_DURATION == 0) {
        WIN = 5
    }
    var done = [false, 0]
    //console.log(tableobj.cells.filter((cell) => cell.placeable == false))
    if (rowAxis > MATRIX_ROWS || colAxis > MATRIX_COLS || tableobj.getCell(colAxis, rowAxis).placeable == false) {
        return 0;
    }
    if (matrix[rowAxis][colAxis] == 0 && tableobj.getCell(colAxis, rowAxis).item == 0 && tableobj.getCell(colAxis, rowAxis).placeable == true) {
        done = [true, 0]
        matrix[rowAxis][colAxis] = item
        tableobj.getCell(colAxis, rowAxis).item = item
        tableobj.getCell(colAxis, rowAxis).placeable = false
        //console.log(tableobj.getCellIdwCell(tableobj.getCell(colAxis, rowAxis)))
        //tableobj.getCell(colAxis, rowAxis).status = false
        //console.log(tableobj.getCell(colAxis, rowAxis).item)
        for (const cell of cells) {
            if (cell.id == rowAxis + "-" + colAxis) {
                cell.innerText = item
                cell.style.backgroundImage = ""
            }
        }
    }
    else if (tableobj.getCell(colAxis, rowAxis).item == "lb") {
        console.log("lb collected")
        matrix[rowAxis][colAxis] = item
        tableobj.getCell(colAxis, rowAxis).item = item
        tableobj.getCell(colAxis, rowAxis).placeable = false
        for (const cell of cells) {
            if (cell.id == rowAxis + "-" + colAxis) {
                cell.innerText = item
                cell.style.backgroundImage = ""
            }
        }
        done = lb_event()
    }
    //console.log("rows with 5 same: " + (check_rows(rowAxis) ? "true" : "false"))
    //console.log("cols with 5 same: " + (check_cols(colAxis) ? "true" : "false"))
    //console.log("right pars with 5 same: " + (check_par_right(rowAxis, colAxis) ? "true" : "false"))
    //console.log("left pars with 5 same: " + (check_par_left(rowAxis, colAxis) ? "true" : "false"))
    //console.log("a lépéssel: " + (check_all(rowAxis, colAxis) ? "nyertél" : "nem nyertél"))
    if (check_all(rowAxis, colAxis)) {
        if (TURN == "X") {
            SCORE_X += 1
            xselement.innerText = "X pontszáma: " + SCORE_X
        }
        else if (TURN == "O") {
            SCORE_O += 1
            oselement.innerText = "O pontszáma: " + SCORE_O
        }
    }
    //console.log(done[1])
    console.log(LB_NEXT_TURN)
    if (done[0] == true && LB_NEXT_TURN == "" && done[1] == 0) {
        if (TURN == "X") {
            TURN = "O"
            cturn.innerText = "Következik: " + TURN
        }
        else if (TURN == "O") {
            TURN = "X"
            cturn.innerText = "Következik: " + TURN
        }
    }
    else if (LB_NEXT_TURN == "" && done[1] != 0) {
        console.log("next turn set to " + TURN)
        LB_NEXT_TURN = TURN
    }
    else if (LB_NEXT_TURN != "" && done[0] == true) {
        console.log("turn set to " + LB_NEXT_TURN + " LBNT cleared")
        TURN = LB_NEXT_TURN
        LB_NEXT_TURN = ""
    }
    if (check_game_end()) {
        return
    }
    random_event()

}

function check_all(rowAxis, colAxis) {
    return check_rows(rowAxis)
        || check_cols(colAxis)
        || check_par_left(rowAxis, colAxis)
        || check_par_right(rowAxis, colAxis)
}

function check_rows(rowAxis) {
    for (var i = 0; i < MATRIX_COLS - WIN + 1; i++) {
        let elements = new Array()
        let objelements = new Array()
        for (var k = 0; k < WIN; k++) {
            elements.push(matrix[rowAxis][k + i])
            objelements.push(tableobj.getCell(k + i, rowAxis))
        }
        if (check_conditions(elements, objelements)) {
            return true
        }
    }
    //console.log("vizszintes pipa")
    return false
}

function check_cols(colAxis) {
    for (var i = 0; i < MATRIX_ROWS - WIN + 1; i++) {
        let elements = new Array()
        let objelements = new Array()
        for (let k = 0; k < WIN; k++) {
            elements.push(matrix[k + i][colAxis])
            objelements.push(tableobj.getCell(colAxis, k + i))
        }
        if (check_conditions(elements, objelements)) {
            return true
        }
    }
    //console.log("fuggoleges pipa")
    return false
}

function check_par_left(rowAxis, colAxis) {
    let leftpar = new Array()
    let objlp = new Array()
    //upper
    for (var i = 0; i < WIN && rowAxis - i >= 0 && colAxis - i >= 0; i++) {
        leftpar.unshift(matrix[rowAxis - i][colAxis - i])
        objlp.unshift(tableobj.getCell(colAxis - i, rowAxis - i))
    }
    //lower
    for (var i = 1; i < WIN && rowAxis + i < MATRIX_ROWS && colAxis + i < MATRIX_COLS; i++) {
        leftpar.push(matrix[rowAxis + i][colAxis + i])
        objlp.push(tableobj.getCell(colAxis + i, rowAxis + i))
    }
    //console.log("leftpar:"+leftpar);
    for (var i = 0; i < leftpar.length - WIN + 1; i++) {
        let elements = new Array()
        let objelements = new Array()
        for (var k = 0; k < WIN; k++) {
            elements.push(leftpar[k + i])
            objelements.push(objlp[k + i])
        }
        if (check_conditions(elements, objelements)) {
            return true
        }
    }
    //console.log("paralel left pipa")
    return false
}

function check_par_right(rowAxis, colAxis) {
    let leftpar = new Array()
    let objlp = new Array()
    //upper
    for (let i = 0; i < WIN && rowAxis - i >= 0 && colAxis + i < MATRIX_COLS; i++) {
        //console.log("par_right upper")
        leftpar.unshift(matrix[rowAxis - i][colAxis + i])
        objlp.unshift(tableobj.getCell(colAxis + i, rowAxis - i))
    }
    //lower
    for (let i = 1; i < WIN && rowAxis + i < MATRIX_ROWS && colAxis - i >= 0; i++) {


        //console.log("asd "+rowAxis+i)
        leftpar.push(matrix[rowAxis + i][colAxis - i])
        objlp.push(tableobj.getCell(colAxis - i, rowAxis + i))
    }
    //console.log("rightpar:"+leftpar);
    for (let i = 0; i < leftpar.length - WIN + 1; i++) {
        let elements = new Array()
        let objelements = new Array()
        for (let k = 0; k < WIN; k++) {
            elements.push(leftpar[k + i])
            objelements.push(objlp[k + i])
        }
        //console.log(elements)
        if (check_conditions(elements, objelements)) {
            return true
        }
    }
    //console.log("paralel right pipa")
    return false
}

function check_conditions(elements, objelements) {
    //console.log(objelements)
    if (elements.length < WIN) {
        return false
    }
    for (var i = 1; i < elements.length; i++) {
        if (elements[i - 1] != elements[i] || objelements[i].status != true) {
            return false
        }
        //console.log("fut a vizsgalat")
    }
    if (elements[0] != 0) {
        for (const cell of objelements) {
            //console.log("státusz - false")
            tableobj.getCellwCell(cell).status = false
            for (const cell1 of cells) {
                if (tableobj.getCellIdwCell(cell) == cell1.id) {
                    cell1.style.color = "green"
                }
            }
        }

    }
    return (elements[0] != 0) ? true : false
}

function addLuckyBlockHistory(lbevent) {
    const history = TURN == "X" ? lbx : lbo
    const entry = document.createElement("div")
    const name = document.createElement("strong")
    const effect = document.createElement("div")
    const duration = document.createElement("small")

    entry.className = "event-history-entry"
    name.textContent = lbevent.name
    effect.textContent = lbevent.effect
    duration.textContent = "Időtartam: " + lbevent.duration + " kör"
    entry.append(name, effect, duration)
    history.append(entry)
}

function lb_event() {
    var lbevent = lbevents[Math.floor(Math.random() * lbevents.length)]
    if (lbevent == lbevents[0]) {
        addLuckyBlockHistory(lbevent)
        return [false, 2]
    }
    //SELF CLEAR
    else if (lbevent == lbevents[1]) {
        let goal = Math.floor(tableobj.cells.filter((cell) => cell.item == TURN).length * 0.2)
        var filtered = tableobj.cells.filter((cell) => cell.item == TURN)
        for (let i = 0; i < goal; i++) {
            let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
            tableobj.Event(chosencell, lbevent.event)
            matrix[chosencell.y][chosencell.x] = 0
            let chosencellid = tableobj.getCellIdwCell(chosencell)
            for (const cell of cells) {
                if (cell.id == chosencellid) {
                    cell.innerText = ""
                }
            }

        }
        addLuckyBlockHistory(lbevent)
        return [true, 0]
    }
    else if (lbevent == lbevents[2]) {
        if (TURN == "X") {
            var filtered = tableobj.cells.filter((cell) => cell.item == "O")
            let goal = Math.floor(tableobj.cells.filter((cell) => cell.item == TURN).length * 0.2)
            for (let i = 0; i < goal; i++) {
                let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
                tableobj.Event(chosencell, lbevent.event)
                matrix[chosencell.y][chosencell.x] = 0
                let chosencellid = tableobj.getCellIdwCell(chosencell)
                for (const cell of cells) {
                    if (cell.id == chosencellid) {
                        cell.innerText = ""
                    }
                }

            }
        }
        else {
            var filtered = tableobj.cells.filter((cell) => cell.item == "X")
            let goal = Math.floor(tableobj.cells.filter((cell) => cell.item == TURN).length * 0.2)
            for (let i = 0; i < goal; i++) {
                let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
                tableobj.Event(chosencell, lbevent.event)
                matrix[chosencell.y][chosencell.x] = 0
                let chosencellid = tableobj.getCellIdwCell(chosencell)
                for (const cell of cells) {
                    if (cell.id == chosencellid) {
                        cell.innerText = ""
                    }
                }

            }
        }
        addLuckyBlockHistory(lbevent)
        return [true, 0]
    }
    else if (lbevent == lbevents[3]) {
        addLuckyBlockHistory(lbevent)
        return [true, 0]
    }
    else if (lbevent == lbevents[4]) {
        random_event(true)
        addLuckyBlockHistory(lbevent)
        return [true, 0]
    }
    else if (lbevent == lbevents[5]) {
        WIN = 3
        EVENT_DURATION = lbevent.duration
        addLuckyBlockHistory(lbevent)
        return [true, 0]
    }
}



function random_event(bool = false) {
    let random = Math.floor(Math.random() * 100)
    //console.log(random)
    //console.log(events[0].item)
    if (random < GAME_MODES[GAMEMODE].event || bool) {
        SINCE_LAST_EVENT = 0
        var event = events[Math.floor(Math.random() * events.length)]
        console.log(event.item)
        if (event == events[0]) {
            eventelement.innerText = "Üresség"
            for (const cell of tableobj.cells.filter((cell) => cell.placeable == false && cell.item == "void")) {
                cell.status = true
                cell.placeable = true
                cell.item = 0
                let chosencellid = tableobj.getCellIdwCell(cell)
                for (const cell of cells) {
                    if (cell.id == chosencellid) {
                        cell.style.backgroundColor = ""
                    }
                }
            }
            let goal = Math.floor((MATRIX_COLS * MATRIX_ROWS) * 0.25)
            var filtered = tableobj.cells.filter((cell) => cell.item == 0)
            for (let i = 0; i < goal; i++) {
                let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
                tableobj.Event(chosencell, event)
                let chosencellid = tableobj.getCellIdwCell(chosencell)
                for (const cell of cells) {
                    if (cell.id == chosencellid) {
                        cell.style.backgroundColor = "black"
                    }
                }
            }
        }
        //RANDOM CELL CLEAR
        if (event == events[1]) {
            eventelement.innerText = "Véletlen mező törölve"
            var filtered = tableobj.cells.filter((cell) => cell.item != 0)
            let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
            //console.log("y" + chosencell.y)
            matrix[chosencell.y][chosencell.x] = 0
            //console.log(matrix[chosencell.y][chosencell.x])
            tableobj.Event(chosencell, event)
            let chosencellid = tableobj.getCellIdwCell(chosencell)
            for (const cell of cells) {
                if (cell.id == chosencellid) {
                    cell.innerText = ""
                }
            }
        }
        if (event == events[2]) {
            eventelement.innerText = "SZERENCSEMEZŐ"
            var filtered = tableobj.cells.filter((cell) => cell.item == 0)
            let chosencell = filtered[Math.floor(Math.random() * filtered.length)]
            //console.log("y" + chosencell.y)
            matrix[chosencell.y][chosencell.x] = "lb"
            //console.log(matrix[chosencell.y][chosencell.x])
            tableobj.Event(chosencell, event)
            let chosencellid = tableobj.getCellIdwCell(chosencell)
            for (const cell of cells) {
                if (cell.id == chosencellid) {
                    cell.style.backgroundImage = "url('./img/lb.png')"
                }
            }
        }
    }
    else{
        //console.log(SINCE_LAST_EVENT)
        //SINCE_LAST_EVENT += 1
        eventelement.innerText = "Nincs"
    }
}

function check_game_end() {
    //console.log(tableobj.cells.filter((cell) => cell.placeable == true).length)
    if (tableobj.cells.filter((cell) => cell.placeable == true).length == 0) {
        AlertBox.style.display = "inline"
        xfinalscore.innerText = "X: " + SCORE_X
        ofinalscore.innerText = "O: " + SCORE_O
        if (SCORE_X > SCORE_O) {
            winner.innerText = "X győzött!"
        }
        else if (SCORE_X < SCORE_O) {
            winner.innerText = "O győzött!"
        }
        else {
            winner.innerText = "Döntetlen!"
        }
        return true
    }

}