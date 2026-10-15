import Gallows from "./modules/gallows.js";
import Word from "./modules/word.js";
import { getGoodQuote, getBadQuote, secretWord } from "./modules/word.js";

const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
const incorrectLetters = ["Backspace", "CapsLock","Shift", "Enter", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "Backspace", " ", "Control", "Alt", "Tab"]
const correctLetters = "qwertzuiopőúűasdfghjkléáyxcvbnmüóöí-"
const usedfield = document.getElementById("usedfield");
const table = document.getElementById("table")
const canvas = document.getElementById("myCanvas");
const message = document.getElementById("message");
const input = document.getElementById("input");
const gallows = new Gallows(canvas)
const words = new Word()

let secret;
let hits = 0
let ingame = true
let usedLetters = []
let history = "";

document.addEventListener((isMobile)?"input":"keydown", function (event) {
    var key = (isMobile)? event.target.value : event.key
    input.value = ""
    if (!usedLetters.includes(key.toUpperCase()) && ingame && correctLetters.includes(key)) {
        let contains = false
        for (let i = 0; i < secret.length; i++) {
            if (secret[i].toUpperCase() == key.toUpperCase()) {
                document.getElementById("c-" + i).innerText = key.toUpperCase()
                contains = true
                hits++
            }
        }
        usedLetters.push(key.toUpperCase())
        if (contains) {
            usedfield.innerHTML += "<span style='color: green'>" + key.toUpperCase() + " </span>"
            message.style.color = "green";
            message.innerText = getGoodQuote();
            if (hits == secret.length) {
                endGame(true)
            }
        } else {
            usedfield.innerHTML += "<span style='color: red'>" + key.toUpperCase() + " </span>"
            var b = gallows.printError()
            message.style.color = "red";
            message.innerText = getBadQuote();
            if (b) {
                endGame(false)
            }
        }
    }
    history += key.toUpperCase();
    if (history.includes(secretWord)) {
        message.style.color = "red";
        message.innerText = String.fromCodePoint(10084);
    }
});
window.addEventListener("load", async () => {
    secret = await words.getRandomWord()
    var elements = "";
    //console.log(secret)
    for (let i = 0; i < secret.length; i++) {
        elements += '<td id="c-' + i + '"></td>'
    }
    table.innerHTML += "<tr>" + elements + "</tr>"
    gallows.setColor("rgba(169,169,169,0.25)")
    gallows.printAll()
    gallows.setColor("rgba(169,169,169,1)")

    if (isMobile) {
        input.classList.remove("d-none")
    }
})

function endGame(b) {
    if (b) {
        message.style.color = "green"
        message.innerText = "Sikeresen kitaláltad! " + String.fromCodePoint(129395)
        for (let i = 0; i < secret.length; i++) {
            document.getElementById("c-" + i).style.color = "green"
        }
    } else {
        message.style.color = "red"
        message.innerText = `Sajnos ez most nem sikerült! ` + String.fromCodePoint(128532)
        for (let i = 0; i < secret.length; i++) {
            const letter = document.getElementById("c-" + i)
            if (letter.innerText == "") {
                letter.style.color = "red"
                letter.innerText = secret[i].toUpperCase()
            }
        }
    }
    ingame = false
    message.innerHTML += '<br><button onclick="location.reload()" type="button" class="btn btn-outline-light">Újra!</button>'
}



