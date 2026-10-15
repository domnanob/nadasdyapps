class Event {
    constructor(item, status, placeable) {
        this.item = item
        this.status = status
        this.placeable = placeable
    }
}
class GameModes{
    constructor(name, event){
        this.name = name
        this.event = event
    }
}
class LuckyBlockEvents {
    constructor(name, effect, duration, event) {
        this.name = name
        this.effect = effect
        this.duration = duration
        this.event = event
    }
}
class Table {
    constructor(cells) {
        this.cells = cells
    }
    getCellIdwCell(gcell) {
        for (const cell of this.cells) {
            if (gcell == cell) {
                let id = cell.y + "-" + cell.x
                return id
            }
        }
    }
    getCell(x, y) {
        for (const cell of this.cells) {
            if (cell.x == x && cell.y == y) {
                return cell
            }
        }
    }
    getCellwCell(gcell) {
        for (const cell of this.cells) {
            if (gcell == cell) {
                return cell
            }
        }
    }
    changeStatus(cell, status) {
        cell.status = status
    }
    setItem(cell, item) {
        if (cell.item == 0) {
            cell.item = item
        }
    }
    Event(cell, event) {
        cell.status = event.status
        cell.item = event.item
        cell.placeable = event.placeable
    }
}


class Cell {
    constructor(x, y, item, status, placeable) {
        this.x = x
        this.y = y
        this.item = item
        this.status = status
        this.placeable = placeable
    }

}

//events
var voidEvent = new Event("void", false, false)
var clearACell = new Event(0, true, true)
var luckyBlock = new Event("lb", true, true)

//events for lb
var sc = new Event(0, true, true)
//lb events
var speedGame = new LuckyBlockEvents("Gyorsított játék", "A győzelemhez 5 helyett 3 mező kell", 4, "")
var doubleTurn = new LuckyBlockEvents("Dupla kör", "Most újra te következel", 1, "")
var selfClear = new LuckyBlockEvents("Saját mezők törlése", "A mezőid 20%-a eltűnik", 1, clearACell)
var clearBonus = new LuckyBlockEvents("Bónusztörlés", "Az ellenfeled mezőinek 20%-a eltűnik", 1, clearACell)
var secret = new LuckyBlockEvents("Titok", "Ezt sosem tudod meg", 15, "", "")
var casualEvent = new LuckyBlockEvents("Véletlen esemény", "Egy véletlen játékesemény történik", 1, "")

var events = [voidEvent, clearACell, luckyBlock]

var lbevents = [doubleTurn, selfClear, clearBonus, secret, casualEvent, speedGame]

const first = new GameModes("Események nélkül", 0)
const secon = new GameModes("Normál", 20)
const third = new GameModes("Káosz", 100)

const GAME_MODES = [first, secon, third]

export { Cell, Table, events, lbevents, GAME_MODES}
