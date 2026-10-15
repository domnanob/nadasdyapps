

//Adatbázisba rakni a cuccokat és onnan betölteni, API,  
const gameBody = document.querySelector(".gameBody")

let items = []
const mode = sessionStorage.getItem('gameMode')
if (mode === 'spotify') {
    fetch("server/spotify_api.php")
        .then(async response => {
            const artists = await response.json()
            if (!response.ok) {
                throw new Error(artists.error || "A Spotify-adatok betöltése nem sikerült.")
            }
            if (!Array.isArray(artists) || artists.length < 2) {
                throw new Error("A Spotify-adatforrás nem adott elegendő előadót.")
            }
            return artists
        })
        .then(artists => {
            items = artists
            document.querySelectorAll('.has').forEach(element => {
                element.innerText = 'Spotify-népszerűség:'
            })
            document.querySelectorAll('.valueText').forEach(element => {
                element.innerText = 'pont'
            })
            document.querySelectorAll('.unit').forEach(element => {
                element.innerText = '/ 100'
            })
            document.querySelectorAll('.spotify-attribution').forEach(element => {
                element.hidden = false
            })
            preloadItems(startLoadedGame)
        })
        .catch(error => {
            console.error("Error fetching Spotify artists:", error)
            loadingscreen.textContent = error.message || "Nem sikerült betölteni a Spotify-adatokat. Frissítsd az oldalt, és próbáld újra."
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
            valueTxt = ''
            unitTxt = 'km²'
            gameBody.classList.add('mode-countries')
            document.querySelectorAll('.has').forEach(element => {
                element.innerText = 'Területe:'
            })
            document.querySelectorAll('.valueText').forEach(element => {
                element.innerText = valueTxt
            })
            document.querySelectorAll('.unit').forEach(element => {
                element.innerText = unitTxt
            })
            higherbtn.innerHTML = 'Nagyobb<i class="fa-solid fa-caret-up"></i>'
            lowerbtn.innerHTML = 'Kisebb<i class="fa-solid fa-caret-down"></i>'
            preloadItems(startLoadedGame)
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
        const finish = () => {
            loaded++
            if (loaded === total) {
                callback()
            }
        }
        img.addEventListener('load', finish, { once: true })
        img.addEventListener('error', finish, { once: true })
        img.src = item.img
    })

}

let loadingscreen = document.querySelector(".loading-screen")

let imgL = document.getElementById("imgL")
let imgR = document.getElementById("imgR")
let artistL = document.getElementById("artistL")
let artistR = document.getElementById("artistR")
let spotifyLinkL = document.getElementById("spotifyLinkL")
let spotifyLinkR = document.getElementById("spotifyLinkR")
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
let valueTxt = 'pont'

let unit = document.querySelectorAll(".unit")
let unitTxt = '/ 100'

function startLoadedGame() {
    loadingscreen.hidden = true
    gameDiv.hidden = false
    GameStart()
}

function GameStart() {
    guessedItemIndex = Math.floor(Math.random() * items.length)

    imgL.src = items[guessedItemIndex].img
    artistL.innerText = items[guessedItemIndex].name
    updateSpotifyLink(spotifyLinkL, items[guessedItemIndex])
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

function updateSpotifyLink(link, item) {
    if (mode === 'spotify' && typeof item.url === 'string') {
        link.href = item.url
    }
}


function loadItem() {
    let availableItems = items
        .map((item, index) => index)
        .filter(index => index !== guessedItemIndex && !usedItems.includes(index))

    if (availableItems.length === 0) {
        usedItems = []
        availableItems = items
            .map((item, index) => index)
            .filter(index => index !== guessedItemIndex)
    }

    const nextItemIndex = availableItems[Math.floor(Math.random() * availableItems.length)]

    guessingItemIndex = nextItemIndex
    usedItems.push(guessingItemIndex)

    imgR.src = items[guessingItemIndex].img
    artistR.innerText = items[guessingItemIndex].name
    updateSpotifyLink(spotifyLinkR, items[guessingItemIndex])
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
    updateSpotifyLink(spotifyLinkL, items[guessedItemIndex])
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
