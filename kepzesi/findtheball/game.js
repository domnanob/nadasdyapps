const game = document.getElementById("game");
const stage = document.getElementById("cup-stage");
const message = document.getElementById("stage-message");
const caption = document.getElementById("stage-caption");
const scoreElement = document.getElementById("score");
const cupCountElement = document.getElementById("cup-count");
const bestElement = document.getElementById("best-score");
const startButton = document.getElementById("start-button");
const scoreList = document.getElementById("score-list");
const dialog = document.getElementById("result-dialog");
const scoreForm = document.getElementById("score-form");
const nameInput = document.getElementById("player-name");
const saveMessage = document.getElementById("save-message");
const playAgainButton = document.getElementById("play-again");
const resultTitle = document.getElementById("result-title");
const resultDescription = document.getElementById("result-description");
const resultIcon = document.getElementById("result-icon");
const initialScores = JSON.parse(document.getElementById("initial-scores").textContent);

const state = {
    score: 0,
    cupCount: 3,
    ballCup: null,
    cups: [],
    phase: "ready",
    busy: false,
    scores: initialScores,
};

const cupMarkup = `
    <span class="cup-ball"></span>
    <svg class="cup-svg" viewBox="0 0 80 108" aria-hidden="true">
        <ellipse class="cup-rim" cx="40" cy="24" rx="25" ry="8"></ellipse>
        <path class="cup-body" d="M15 24h50l-7 62c-.5 6-5 10-11 10H33c-6 0-10.5-4-11-10l-7-62Z"></path>
        <path class="cup-band" d="m18 48 44 4-2 13-40-4-2-13Z"></path>
        <path class="cup-shine" d="m25 31 5 45"></path>
        <path class="cup-rim" d="M14 23c0-6 12-11 26-11s26 5 26 11-12 9-26 9-26-3-26-9Z"></path>
    </svg>`;

const wait = (milliseconds) => new Promise(resolve => setTimeout(resolve, milliseconds));

function createCups(count) {
    stage.replaceChildren();
    state.cups = Array.from({ length: count }, (_, index) => {
        const cup = document.createElement("button");
        cup.className = "cup";
        cup.type = "button";
        cup.setAttribute("aria-label", `Pohár ${index + 1}`);
        cup.innerHTML = cupMarkup;
        cup.addEventListener("click", () => chooseCup(cup));
        stage.append(cup);
        return cup;
    });
    layoutCups();
}

function layoutCups() {
    if (!state.cups.length) {
        return;
    }

    const compact = window.matchMedia("(max-width: 520px)").matches;
    const cupWidth = compact ? 62 : 76;
    const cupHeight = compact ? 96 : 108;
    const gapX = compact ? 3 : 13;
    const gapY = compact ? 1 : 7;
    const availableWidth = Math.max(stage.clientWidth, cupWidth);
    const columns = Math.max(1, Math.min(state.cups.length, Math.floor((availableWidth + gapX) / (cupWidth + gapX))));
    const rows = Math.ceil(state.cups.length / columns);
    const rowWidth = columns * cupWidth + (columns - 1) * gapX;
    const leftOffset = Math.max(0, (availableWidth - rowWidth) / 2);
    stage.style.height = `${Math.max(compact ? 145 : 158, rows * cupHeight + (rows - 1) * gapY + 14)}px`;

    state.cups.forEach((cup, slot) => {
        const row = Math.floor(slot / columns);
        const column = slot % columns;
        const cupsInThisRow = Math.min(columns, state.cups.length - row * columns);
        const thisRowWidth = cupsInThisRow * cupWidth + (cupsInThisRow - 1) * gapX;
        const rowOffset = Math.max(0, (availableWidth - thisRowWidth) / 2);
        cup.style.transform = `translate(${rowOffset + column * (cupWidth + gapX)}px, ${row * (cupHeight + gapY)}px)`;
    });
}

function setMessage(text, subtext = "") {
    message.textContent = text;
    caption.textContent = subtext;
}

function setInteractive(enabled) {
    state.cups.forEach((cup, index) => {
        cup.disabled = !enabled;
        cup.classList.toggle("is-selectable", enabled);
        cup.setAttribute("aria-label", `Pohár ${index + 1}${enabled ? ", válaszd ki" : ""}`);
    });
}

function shuffleIndexes(size) {
    const indexes = Array.from({ length: size }, (_, index) => index);
    for (let index = indexes.length - 1; index > 0; index -= 1) {
        const randomIndex = Math.floor(Math.random() * (index + 1));
        [indexes[index], indexes[randomIndex]] = [indexes[randomIndex], indexes[index]];
    }
    return indexes;
}

async function shuffleCups() {
    state.phase = "shuffling";
    const moves = Math.min(5 + state.score * 2, 25);
    setMessage("Figyelj jól — indul a keverés!", `${moves} gyors csere, ne veszítsd szem elől a labdát!`);
    await wait(350);

    for (let move = 0; move < moves; move += 1) {
        const [first, second] = shuffleIndexes(state.cups.length).slice(0, 2);
        state.cups[first].classList.add("is-swapping");
        state.cups[second].classList.add("is-swapping");
        [state.cups[first], state.cups[second]] = [state.cups[second], state.cups[first]];
        if (state.ballCup === first) {
            state.ballCup = second;
        } else if (state.ballCup === second) {
            state.ballCup = first;
        }

        caption.textContent = `Keverés: csere ${move + 1}/${moves} — kövesd a fénylő poharakat!`;
        layoutCups();
        await wait(Math.max(540, 680 - state.score * 8));
        state.cups[first].classList.remove("is-swapping");
        state.cups[second].classList.remove("is-swapping");
    }

    state.phase = "guess";
    setInteractive(true);
    setMessage("Most te jössz! Melyik pohár alatt van a labda?", "Koppints a pohárra, amelyik szerinted rejti a labdát.");
}

async function startRound() {
    if (state.busy || game.dataset.databaseReady !== "true") {
        return;
    }

    state.busy = true;
    state.phase = "showing";
    state.ballCup = Math.floor(Math.random() * state.cupCount);
    createCups(state.cupCount);
    scoreElement.textContent = String(state.score);
    cupCountElement.textContent = String(state.cupCount);
    startButton.disabled = true;
    startButton.querySelector("span:last-child").textContent = "Figyelj...";
    setMessage("Nézd meg, hová kerül a labda!", "Jegyezd meg a labdás poharat!");
    state.cups[state.ballCup].classList.add("has-ball", "lifted");
    await wait(1300);
    state.cups[state.ballCup].classList.remove("has-ball", "lifted");
    await wait(350);
    await shuffleCups();
    state.busy = false;
}

async function chooseCup(cup) {
    if (state.phase !== "guess" || state.busy) {
        return;
    }

    state.phase = "result";
    state.busy = true;
    setInteractive(false);
    const chosenCupIndex = state.cups.indexOf(cup);
    const isCorrect = chosenCupIndex === state.ballCup;
    state.cups[state.ballCup].classList.add("revealed");

    if (isCorrect) {
        cup.classList.add("correct");
        state.score += 1;
        state.cupCount += 1;
        scoreElement.textContent = String(state.score);
        cupCountElement.textContent = String(state.cupCount);
        bestElement.textContent = String(Math.max(Number(bestElement.textContent), state.score));
        setMessage("Megvan! A labda ott volt!", `Szép szem! A következő körben már ${state.cupCount} pohár lesz.`);
        await wait(1000);
        state.busy = false;
        startButton.disabled = false;
        startButton.querySelector("span:last-child").textContent = "Következő kör";
        state.phase = "ready";
        return;
    }

    cup.classList.add("missed");
    setMessage("Jaj, ez most nem az volt!", `A labdás pohár a ${state.ballCup + 1}. volt. A köröd véget ért.`);
    await wait(900);
    state.busy = false;
    await showGameOver();
}

function qualifiesForLeaderboard(score) {
    if (score < 1) {
        return false;
    }
    if (state.scores.length < 10) {
        return true;
    }
    return score > Number(state.scores[state.scores.length - 1].score);
}

function renderScores(scores) {
    state.scores = scores;
    scoreList.replaceChildren();

    if (scores.length === 0) {
        const empty = document.createElement("li");
        empty.className = "empty-score";
        empty.textContent = "Még nincs eredmény — te lehetsz az első!";
        scoreList.append(empty);
        return;
    }

    scores.forEach((entry, index) => {
        const item = document.createElement("li");
        const rank = document.createElement("span");
        rank.className = "rank";
        rank.textContent = String(index + 1).padStart(2, "0");
        const name = document.createElement("span");
        name.className = "score-name";
        name.textContent = entry.player_name;
        const score = document.createElement("strong");
        score.className = "score-value";
        score.textContent = String(entry.score);
        item.append(rank, name, score);
        scoreList.append(item);
    });
}

async function showGameOver() {
    resultIcon.textContent = state.score > 0 ? "🏆" : "🥤";
    resultTitle.textContent = state.score > 0 ? "Szép kör!" : "Még egy próbát?";
    resultDescription.textContent = state.score > 0
        ? `${state.score} pontot szereztél, mielőtt elbújt a labda.`
        : "Most nem sikerült pontot szerezni. Próbáld újra, és figyeld a labdát!";
    saveMessage.textContent = "";
    scoreForm.hidden = !qualifiesForLeaderboard(state.score);
    playAgainButton.textContent = scoreForm.hidden ? "Új kör" : "Kihagyom, új kör";
    dialog.showModal();

    if (!scoreForm.hidden) {
        nameInput.value = "";
        nameInput.focus();
    }
}

async function saveScore(event) {
    event.preventDefault();
    const submitButton = scoreForm.querySelector("button");
    submitButton.disabled = true;
    saveMessage.textContent = "Mentés...";

    try {
        const response = await fetch("./?action=submit-score", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: nameInput.value.trim(),
                score: state.score,
            }),
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.error || "Az eredményt nem sikerült elmenteni.");
        }

        renderScores(result.scores);
        if (result.saved) {
            saveMessage.textContent = "Sikeresen felkerültél a ranglistára!";
            resultDescription.textContent = `${state.score} pont — szép munka, ${nameInput.value.trim()}!`;
            scoreForm.hidden = true;
            playAgainButton.textContent = "Új kör";
        } else {
            saveMessage.textContent = "Közben bekerült egy jobb eredmény; ez most nem fért be a top 10-be.";
        }
    } catch (error) {
        saveMessage.textContent = error.message;
    } finally {
        submitButton.disabled = false;
    }
}

startButton.addEventListener("click", startRound);
scoreForm.addEventListener("submit", saveScore);
function resetGame() {
    state.score = 0;
    state.cupCount = 3;
    state.phase = "ready";
    state.busy = false;
    state.ballCup = null;
    scoreElement.textContent = "0";
    cupCountElement.textContent = "3";
    startButton.disabled = false;
    startButton.querySelector("span:last-child").textContent = "Játék indítása";
    createCups(state.cupCount);
    setMessage("Nyomd meg az indítást, és figyeld a labdát!");
    caption.textContent = "Kezdéskor mutatjuk, hová kerül a labda.";
}

playAgainButton.addEventListener("click", () => dialog.close());
dialog.addEventListener("close", resetGame);

new ResizeObserver(layoutCups).observe(stage);
createCups(state.cupCount);
