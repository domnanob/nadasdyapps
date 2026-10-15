const main = document.getElementById("main");
const gameLinks = JSON.parse(document.getElementById("game-links").textContent);
const delay = (milliseconds) => new Promise(resolve => setTimeout(resolve, milliseconds));

async function checkPermissions() {
    while (main.dataset.gamesAllowed !== "true") {
        await delay(2000);

        try {
            const response = await fetch("./api/permission/get.php", { cache: "no-store" });
            if (!response.ok) {
                throw new Error(`Permission check failed with status ${response.status}.`);
            }

            const result = await response.json();
            if (result.allow_games === true) {
                if (gameLinks.length > 0) {
                    const game = gameLinks[Math.floor(Math.random() * gameLinks.length)];
                    location.assign(game);
                } else {
                    location.reload();
                }
                return;
            }
        } catch (error) {
            console.error("Unable to check game permissions.", error);
            const status = document.getElementById("permission-status");
            if (status) {
                status.textContent = "A játékengedélyt most nem tudjuk ellenőrizni. Újrapróbáljuk...";
                status.classList.add("play-hint-error");
            }
        }
    }
}

function setupFullscreenButton() {
    const button = document.getElementById("fsBtn");
    if (!button) {
        return;
    }

    const updateButton = () => {
        const isFullscreen = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
        button.setAttribute("aria-label", isFullscreen ? "Kilépés a teljes képernyőből" : "Teljes képernyő");
        button.title = isFullscreen ? "Kilépés a teljes képernyőből" : "Teljes képernyő";
        button.innerHTML = isFullscreen
            ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3v5H3m13-5v5h5M3 16h5v5m8-5h5v5"/></svg>'
            : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3m13-5h3a2 2 0 0 1 2 2v3M3 16v3a2 2 0 0 0 2 2h3m13-5v3a2 2 0 0 1-2 2h-3"/></svg>';
    };

    button.addEventListener("click", async () => {
        const isFullscreen = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
        if (isFullscreen) {
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else {
                document.webkitExitFullscreen?.();
            }
        } else if (document.documentElement.requestFullscreen) {
            await document.documentElement.requestFullscreen({ navigationUI: "hide" });
        } else {
            document.documentElement.webkitRequestFullscreen?.();
        }
    });
    document.addEventListener("fullscreenchange", updateButton);
    document.addEventListener("webkitfullscreenchange", updateButton);
}

setupFullscreenButton();
checkPermissions();
