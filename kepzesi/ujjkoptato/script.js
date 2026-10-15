let startGameBtn = document.getElementById("startGameBtn")
let restartGameBtn = document.getElementById("restartGameBtn")
let leaderboardBtn = document.getElementById("leaderboardBtn")
let scoreTxt = document.getElementById("scoreTxt")

let gameContainer = document.getElementById("gameContainer")
let endGameContainer = document.getElementById("endGameContainer")
let startGameContainer = document.getElementById("startGameContainer")
let leaderboardContainer = document.getElementById("leaderboardContainer")
let promptContainer = document.querySelector(".promptContainer");

let timeLeft = 15;
let counter;
let score;
let nextNumber;
let timerRunning = false
let gameActive = false
let activeCell;
let countdown
let latestPlaya

let backBtn = document.getElementById("backBtn")
backBtn.addEventListener("click", () => {
  showContainers(startGameContainer)
})

let neonShadow = document.querySelectorAll(".neonColorShadow");

const timer = ms => new Promise(res => setTimeout(res, ms))

document.querySelectorAll(".cell").forEach(cell => {
  cell.addEventListener("click", async (event) => {
    if (!gameActive) return

    if (event.target.textContent != counter) {
      if (!timerRunning) {
        startTimer();
      }


      event.target.style.color = "#ff4a3d"
      event.target.classList.add("neonColorShadowRed")


      await timer(200);

      event.target.classList.remove("neonColorShadowRed")
      event.target.style.color = "white"


      if (navigator.vibrate) {
        navigator.vibrate([200, 20, 200]);
      }


      event.target.classList.add("animate__animated", "animate__shakeX");
      event.target.addEventListener("animationend", () => {
        event.target.classList.remove("animate__animated", "animate__shakeX");
      }, { once: true });


      timeLeft = Math.max(0, timeLeft - 1);
      document.getElementById("timer").textContent = "Hátralévő idő: " + timeLeft + "s";

      if (timeLeft > 0) {
        MinusOneToast();
      }
    }
  });
});

function MinusOneToast() {
  const toastContainer = document.getElementById("toastContainer");

  const toastEl = document.createElement("div");
  toastEl.className = "toast align-items-center text-bg-danger border-0 show";
  toastEl.role = "alert";
  toastEl.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        -1s
      </div>
    </div>
  `;

  toastContainer.appendChild(toastEl);

  setTimeout(() => {
    toastEl.remove();
  }, 1000);


}

startGameBtn.addEventListener("click", () => {
  StartGame()
  gameContainer.hidden = false
  startGameContainer.hidden = true
})

restartGameBtn.addEventListener("click", () => {
  endGameContainer.hidden = true
  startGameContainer.hidden = false
})



async function placeNumber() {
  const cells = document.querySelectorAll(".cell");

  let freePlace = false;

  while (!freePlace) {
    const randomIndex = Math.floor(Math.random() * cells.length);
    const candidate = cells[randomIndex];

    if (candidate.id !== "activeCell") {
      activeCell = candidate;
      freePlace = true;
    }
  }

  activeCell.textContent = nextNumber;
  activeCell.style.backgroundImage = "none";
  activeCell.id = "activeCell";
  nextNumber++;

  activeCell.onclick = async (event) => {
    if (event.target.textContent == counter) {
      if (!timerRunning) {
        startTimer();
      }


      event.target.style.color = "#74ee15";
      event.target.classList.add("neonColorShadowGreen");
      counter++;
      score++;

      await timer(100);

      event.target.classList.remove("neonColorShadowGreen");
      event.target.style.color = "white";
      event.target.textContent = "";
      event.target.onclick = null;
      event.target.id = "";
      event.target.style.backgroundImage =
        "url('ntszki.png')";

      placeNumber();
    }
  };

  scoreTxt.textContent = `Pont: ${counter - 1}`;
}


function startTimer() {
  timerRunning = true
  const timerElement = document.getElementById("timer");

  clearInterval(countdown)
  countdown = setInterval(() => {
    timeLeft--;
    timerElement.textContent = "Hátralévő idő:  " + timeLeft + "s";
    if (timeLeft <= 0) {
      clearInterval(countdown);
      gameOver();
    }
  }, 1000);
}


function StartGame() {
  console.log("Game started");

  gameActive = true
  score = 0;
  counter = 1;
  nextNumber = 1;
  timeLeft = 15;

  document.getElementById("timer").textContent = "Hátralévő idő: " + timeLeft + "s";

  document.querySelectorAll(".cell").forEach(stuff => {
    stuff.textContent = "";
    stuff.id = "";
    stuff.onclick = null;
    stuff.style.backgroundImage = "url('ntszki.png')";
  });


  while (nextNumber <= 3) {
    placeNumber()
  }

}


async function gameOver() {
  console.log("Game ended");

  gameActive = false
  showContainers(endGameContainer)

  let finalScore = score;
  timerRunning = false;


  document.getElementById("currentScore").innerHTML = `Elért pontszámod: ${finalScore}`;
  document.getElementById("cpmValue").innerText = Math.round((finalScore / 15) * 60);

  if (activeCell) {
    activeCell.onclick = null;
    activeCell.textContent = "";
    activeCell.id = "";
  }



  const proms = await fetch("server/getscores.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    }
  }).then(response => response.json())
    .then(data => {
      if (data.length == 0 || data.length < 10 || finalScore > data[data.length - 1].score) {
        console.log("TOP 10 be jutott");

        //username  = prompt("Add meg a neved!");
        promptContainer.hidden = false;
        restartGameBtn.hidden = true;

        document.getElementById("submitNameBtn").onclick = async () => {
          let nameInput = document.getElementById("nameInput");
          console.log("Név beküldés kattintás");


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
          console.log("Név beküldése...");


          const username = nameInput.value;
          promptContainer.hidden = true;

          await fetch("server/uploadscore.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "username=" + username + "&score=" + finalScore,
          }).then(() => {
            console.log("Név adatbázisba csatolt");
            latestPlaya = nameInput.value
            openLeaderboard();
            nameInput.value = "";
          });
        }
      }
      else {
        restartGameBtn.hidden = false
        latestPlaya = null
        console.log("Nem jutott top 10be");
      }
    })
}

async function openLeaderboard() {
  showContainers(leaderboardContainer)
  console.log("Legutóbbi felhasználó: ", latestPlaya);


  const proms = await fetch("server/getscores.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    }
  }).then(response => response.json())
    .then(data => {
      console.log(data);
      let leaderboardTable = document.getElementById("leaderboardBody");

      let table = ``;

      data.forEach((item, index) => {
        let rankClass = ""
        if(index === 0) rankClass = "first-place"
        if(index === 1) rankClass = "second-place"
        if(index === 2) rankClass = "third-place"
        
        // Kiemeli a legutóbbi nyertest, figyelem felhívó, hogy te vagy az a listán
          table += `
          <tr class="${rankClass} ${latestPlaya == item.username ? "highlightRow" : ""}">
            <th scope="row">${index + 1}</th>
            <td>${item.username}</td>
            <td>${item.score} pts</td>
          </tr>
        `;
        
      });

      table += `
      </tbody>
    </table> `

      leaderboardTable.innerHTML = table;
    })
}

function showContainers(container) {
  startGameContainer.hidden = true;
  gameContainer.hidden = true;
  endGameContainer.hidden = true;
  leaderboardContainer.hidden = true;
  console.log("Minden elrejtve");

  container.hidden = false;
  console.log(container, "Megjelenítve");

}