
let green = document.getElementById("greenBtn")
let yellow = document.getElementById("yellowBtn")
let blue = document.getElementById("blueBtn")
let red = document.getElementById("redBtn")
let currentColor = document.getElementById("currentColor")
let round = document.getElementById("round")
let startGameBtn = document.getElementById("startGameBtn")
let restartGameBtn = document.getElementById("restartGameBtn")
let leaderboardBtn = document.getElementById("leaderboardBtn")
let promptContainer = document.querySelector(".promptContainer");
let leaderboardContainer = document.getElementById("leaderboardContainer")

const timer = ms => new Promise(res => setTimeout(res, ms))

let colors = ["blue", "green", "red", "yellow"]
let colorOrder = []
let currentRound = 0
let guessNumber = 0
let highScore = 0
//let oldhighScore = getCookie("hiScore")
let username = ""
let score;


const greenSound = new Audio("sfx/green.mp3")
const redSound = new Audio("sfx/red.mp3")
const blueSound = new Audio("sfx/blue.mp3")
const yellowSound = new Audio("sfx/yellow.mp3")

startGameBtn.addEventListener("click", () => {
    startGame()
})

restartGameBtn.addEventListener("click", () => {
    document.getElementById("endGameContainer").style.visibility = "hidden";
    document.getElementById("startGameContainer").style.visibility = "visible";
})

blue.addEventListener("click", () => {
    checkColor("blue")
    playSound(blueSound)
})

green.addEventListener("click", () => {
    checkColor("green")
    playSound(greenSound)
})

red.addEventListener("click", () => {
    checkColor("red")
    playSound(redSound)
})

yellow.addEventListener("click", () => {
    checkColor("yellow")
    playSound(yellowSound)
})

function startGame() {
    document.getElementById("gameContainer").style.visibility = "visible";
    document.getElementById("startGameContainer").style.visibility = "hidden";

    currentRound = 0
    guessNumber = 0
    colorOrder = []

    nextColor()
    /* document.getElementById("scoreLeader").innerHTML = getCookie("leader")  */
}

function restartGame() {

    document.getElementById("gameContainer").style.visibility = "hidden";
    document.getElementById("endGameContainer").style.visibility = "visible";

    document.getElementById("currentScore").innerHTML = `Az elért pontszámod: ${currentRound - 1}`
}

async function checkColor(color) {
    if (colorOrder[guessNumber] == color) {
        guessNumber++
        if (guessNumber == currentRound) {
            guessNumber = 0
            nextColor()
        }
    }
    else {
        restartGame()
        /* hiScore() */
        //getPlayer()
        let score = currentRound - 1;

        const proms = await fetch("server/getscores.php", {
            method: "GET",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        }).then(response => response.json())
            .then(data => {
                if (data.length == 0 || data.length < 10 || score > data[data.length - 1].score) {


                    //username  = prompt("Add meg a neved!");
                    promptContainer.hidden = false;
                    restartGameBtn.hidden = true;

                    document.getElementById("submitNameBtn").onclick = async () => {
                        let nameInput = document.getElementById("nameInput")

                        if (nameInput.value.length == "") {
                            console.log("Nem adott nevet");

                            nameInput.style.boxShadow = "0 0 15px red";
                            nameInput.style.border = "1px solid red";

                            await timer(500)

                            nameInput.style.boxShadow = "none";
                            nameInput.style.border = "none";

                            nameInput.focus();

                            return;
                        }


                        const username = nameInput.value;
                        promptContainer.hidden = true;

                        await fetch("server/uploadscore.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "username=" + username + "&score=" + score,
                        }).then(() => {
                            document.getElementById("nameInput").value = ""
                            openLeaderboard();
                        });
                    }
                } else {
                    restartGameBtn.hidden = false
                }
            })
    }
}



function randomColor() {
    let randomColors = Math.floor(Math.random() * colors.length)
    colorOrder.push(colors[randomColors])
}

function nextColor() {
    randomColor()
    currentRound++
    round.innerHTML = currentRound
    console.log("Current round", currentRound)
    console.log(colorOrder)

    showColors()

}


async function openLeaderboard() {
    gameContainer.style.visibility = "hidden";
    endGameContainer.style.visibility = "hidden";
    startGameContainer.style.visibility = "hidden";
    leaderboardContainer.hidden = false;

    const proms = await fetch("server/getscores.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    }).then(response => response.json())
        .then(data => {
            console.log(data);


            let leaderboardTable = document.getElementById("leaderboardTable");


            let table = `<table class="table">
      <thead>
        <tr>
          <th scope="col">Rank</th>
          <th scope="col">Név</th>
          <th scope="col">Pont</th>
        </tr>
      </thead>
      `;
            if (data.length == 0) {
                let t = `<tbody> <tr>
                            <td></td>
                            <td class="text-center" scope="row"> Még nincs adat </td>
                            <td></td>
                        </tr></tbody></table>`
                table += t

            }

            data.forEach((item, index) => {
            table += `
            <tbody>         

                <tr>
                <th scope="row" style="font-weight:bold;">${index + 1}</th>
                <td>${item.username}</td>
                <td>${item.score}</td>
                </tr>
                `;
                    });

                    table += `
            </tbody>
            </table>`

            leaderboardTable.innerHTML = table;
        })
}

/* async function hiScore(){
    await timer(500)
    highScore = (currentRound) - 1    
    if( highScore > oldhighScore){
        oldhighScore = highScore
        console.log("Highscore: ", oldhighScore);

        username = prompt("Add meg a neved: ")
        console.log(username);  

        setCookie("leader", username, 100)
        setCookie("hiScore", oldhighScore, 100)
    }
} */

function playSound(colorsound) {
    colorsound.volume = 0.05

    let clonedSound = colorsound.cloneNode()
    clonedSound.volume = colorsound.volume
    clonedSound.play()
}


async function showColors() {
    disableButtons(true)
    await timer(500) // Szünet a következő szín megjelenítése előtt
    for (var i = 0; i < colorOrder.length; i++) {

        await timer(300) // Szünetek két szín között

        let currentColorID = document.getElementById(`${colorOrder[i]}Btn`)

        let old = currentColorID.id
        currentColorID.id = `current${currentColorID.id}`

        let currentSound = eval(`${colorOrder[i]}Sound`)

        currentSound.volume = 0.05

        let clonedSound = currentSound.cloneNode()
        clonedSound.volume = currentSound.volume
        clonedSound.play()

        await timer(500)  // Világitás időtartama

        currentColorID.id = old
    }
    disableButtons(false)
}

function disableButtons(asd) {
    yellow.disabled = asd
    red.disabled = asd
    green.disabled = asd
    blue.disabled = asd
}

function redirect(url) {
    window.location.href = url
}

let backBtn = document.getElementById("backBtn")
backBtn.addEventListener("click", () => {
    leaderboardContainer.hidden = true;
    startGameContainer.style.visibility = "visible";
})
/* async function getPlayer(){
    await timer(500)

    var playername = prompt("Add meg a nevedet: ")
    var playerscore = currentRound - 1

    var scoreboard = new Object();
    scoreboard.name = playername
    scoreboard.score = playerscore
    scores.push(JSON.parse(JSON.stringify(scoreboard)))

    await timer(500)

    showTopTen()
    sendScoreBoard(scores.slice(0, 10));
} */

/* function showTopTen(){

    scores.sort((a, b) => parseInt(b.score) - parseInt(a.score))
    console.log(scores);
}
 */
//Példa a scoreboard elküldésére

//1. lépés lekérjük a mostanit
//var scores = await getScoreBoard()

//2. megnézed, hogy volt-e rekord. ha volt akkor hozzáírod a mostanit megfelelő sorrendben figyelve arra hogy max akkor 10 legyen benne


//...

//3. elküldöd a válasz objektumot a szerverre, erre itt egy példa ez elküld 2db rekordot amit a szerver bele is ír a fájlba
/* var obj = [

] */


