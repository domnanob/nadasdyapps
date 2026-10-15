

//Adatbázisba rakni a cuccokat és onnan betölteni, API,  
const gameBody = document.querySelector(".gameBody")

let items = []
const mode = sessionStorage.getItem('gameMode')
if (mode === 'spotify') {
    fetch("db/spotify_items.json")
        .then((res) => {
            if (!res.ok) {
                throw new Error("Could not load spotify_items.json")
            }
            return res.json()
        })
        .then((jsonArray) => {
            items = jsonArray

            preloadItems(() => {
                loadingscreen.hidden = true
                gameDiv.hidden = false
                GameStart()
            })
        })
        .catch((err) => {
            console.error("Error fetching Spotify items:", err)
            loadingscreen.textContent = "Nem sikerült betölteni a Spotify-adatokat. Frissítsd az oldalt, és próbáld újra."
        })

}

else if (mode === 'countries') {
    fetch("server/countries_api.php")
        .then(async response => {
            const countries = await response.json()
            if (!response.ok) {
                throw new Error(countries.error || "Az országadatok betöltése nem sikerült.")
            }
            if (!Array.isArray(countries)) {
                throw new Error("Az országadatok forrása érvénytelen választ adott.")
            }
            return countries
        })
        .then(countries => {
            const countryNames = new Intl.DisplayNames(['hu'], { type: 'region' })
            countries.forEach(country => {
                country.name = countryNames.of(country.code) || country.name
            })
            items = countries
            preloadItems(() => {
                loadingscreen.hidden = true
                gameDiv.hidden = false
                gameBody.classList.add('mode-countries');
                document.querySelectorAll('.has').forEach(element => {
                    element.innerText = 'Területe:'
                })
                document.querySelectorAll('.valueText').forEach(element => {
                    element.innerText = ''
                })
                document.querySelectorAll('.unit').forEach(element => {
                    element.innerText = 'km²'
                })
                higherbtn.innerHTML = 'Nagyobb<i class="fa-solid fa-caret-up"></i>'
                lowerbtn.innerHTML = 'Kisebb<i class="fa-solid fa-caret-down"></i>'
                GameStart()
            })
        })
        .catch(error => {
            console.error("Error fetching country data:", error)
            loadingscreen.textContent = "Nem sikerült betölteni az országadatokat. Frissítsd az oldalt, és próbáld újra."
        })
}

function preloadItems(callback) {
    let loaded = 0
    let total = items.length

    items.forEach(item => {
        const img = new Image()
        img.src = item.img
        img.onload = () => {
            loaded++
            //console.log(`Loaded ${item.name} image.`);

            if (loaded === total) {
                callback()
            }
        }
    })

}

let loadingscreen = document.querySelector(".loading-screen")

let imgL = document.getElementById("imgL")
let imgR = document.getElementById("imgR")
let artistL = document.getElementById("artistL")
let artistR = document.getElementById("artistR")
let valueL = document.getElementById("valueL")
let valueR = document.getElementById("valueR")
let valueLNum = document.getElementById("valueLNum")
let valueRNum = document.getElementById("valueRNum")
let higherbtn = document.getElementById("higherBtn")
let lowerbtn = document.getElementById("lowerBtn")

let guessingItemIndex = 0
let guessedItemIndex = 0


let buttons = document.querySelector(".buttons")
let answerbox = document.querySelector(".answerbox")
let correctIcon = document.querySelector(".correct")
let incorrectIcon = document.querySelector(".incorrect")
let vs = document.querySelector(".vs")
let vertLine = document.querySelector(".vert-line")

let score = 0
let usedItems = []

let scoreValue = document.getElementById("scoreValue")
let finalScore = document.getElementById("finalScore")
let scoreInput = document.getElementById("scoreInput")

let backToMenu = document.getElementById("backToMenu")

backToMenu.addEventListener("click", function () {
    window.location.href = "index.php"
})


let valueText = document.querySelectorAll(".valueText")
let valueTxt = 'millió'

let unit = document.querySelectorAll(".unit")
let unitTxt = ''

function GameStart() {
    guessedItemIndex = Math.floor(Math.random() * items.length)

    imgL.src = items[guessedItemIndex].img
    artistL.innerText = items[guessedItemIndex].name
    valueLNum.innerText = items[guessedItemIndex].value.toLocaleString('hu-HU')
    valueText.forEach(element => {
        element.innerText = valueTxt
    })
    unit.forEach(element => {
        element.innerText = unitTxt
    });
    score = 0

    loadItem()
    higherOrLower()

}


function loadItem() {
    let nextItemIndex
    do {
        nextItemIndex = Math.floor(Math.random() * items.length)
    } while (nextItemIndex === guessedItemIndex || usedItems.includes(nextItemIndex))

    guessingItemIndex = nextItemIndex
    usedItems.push(guessingItemIndex)

    imgR.src = items[guessingItemIndex].img
    artistR.innerText = items[guessingItemIndex].name
    valueRNum.innerText = items[guessingItemIndex].value.toLocaleString('hu-HU')


}


function higherOrLower() {
    higherbtn.onclick = () => checkAnswer("higher")
    lowerbtn.onclick = () => checkAnswer("lower")

}

function checkAnswer(choice) {
    const guessedItem = items[guessedItemIndex].value
    const guessingItem = items[guessingItemIndex].value
    let isCorrect = false

    if (choice === "higher" && guessingItem > guessedItem || guessingItem == guessedItem) {
        //Helyes
        isCorrect = true
    }
    else if (choice === "lower" && guessingItem < guessedItem || guessingItem == guessedItem) {
        //Helyes
        isCorrect = true
    }

    answerbox.hidden = false

    buttons.hidden = true
    vs.hidden = true
    if (isCorrect) {
        correctIcon.hidden = false
        answerbox.style.backgroundColor = "green"
        score++
        valueR.hidden = false

        //Eltoljuk a kovire
        guessedItemIndex = guessingItemIndex

        setTimeout(() => {
            correctIcon.hidden = true
            valueR.hidden = true
            vs.hidden = false
            answerbox.style.backgroundColor = "white"
            loadNextItem()
        }, 1000)
    }
    else {
        incorrectIcon.hidden = false
        answerbox.style.backgroundColor = "red"
        valueR.hidden = false
        vertLine.hidden = true

        setTimeout(() => {
            incorrectIcon.hidden = true
            valueR.hidden = true
            vs.hidden = false
            answerbox.style.backgroundColor = "white"
            gameEnds()
        }, 1000)
    }
}



function loadNextItem() {
    correctIcon.hidden = true
    incorrectIcon.hidden = true
    buttons.hidden = false

    imgL.src = items[guessedItemIndex].img
    artistL.innerText = items[guessedItemIndex].name
    valueLNum.innerText = items[guessedItemIndex].value.toLocaleString('hu-HU')
    scoreValue.innerText = score

    loadItem()

}


let gameDiv = document.getElementById("gameDiv")
let gameEnd = document.querySelector(".gameEnd")

function gameEnds() {
    gameDiv.hidden = true
    gameEnd.hidden = false
    finalScore.innerText = score
    scoreInput.value = score
    //API meghivasa
    if (score > 0 && !userId.is_guest) {
        fetch("server/uploadscore_api.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({ score: String(score), mode }),
        })
            .then(async response => {
                const result = await response.json()
                if (!response.ok || !result.success) {
                    throw new Error(result.error || `A pontszám mentése nem sikerült (${response.status}).`)
                }
            })
            .catch(error => console.error("Could not save Higher/Lower score:", error))
    }
}

let playAgainBtn = document.getElementById("playAgainBtn")

playAgainBtn.addEventListener("click", function () {
    gameEnd.hidden = true
    gameDiv.hidden = false
    buttons.hidden = false
    vertLine.hidden = false
    usedItems = []
    GameStart()
    scoreValue.innerText = score

})




window.onload = () => {
    preloadItems(() => {
        loadingscreen.hidden = true
        gameDiv.hidden = false
        GameStart()
    })
}
