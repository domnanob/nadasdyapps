# Spotify mode setup

Spotify mode first uses the Spotify Web API and compares its artist popularity score (0–100). If the API is unavailable, the game falls back to `db/spotify_items.json`, which contains monthly listener counts in millions. The API credentials must be kept on the server; do not put them in JavaScript or commit them to Git.

1. Create an app in the [Spotify Developer Dashboard](https://developer.spotify.com/dashboard) and copy its client ID and client secret.
2. Configure the following environment variables on the PHP server. Do not add the credentials to JavaScript, source control, or a public file.

   **Azure App Service:** open **Settings → Environment variables → App settings**, add `SPOTIFY_CLIENT_ID` and `SPOTIFY_CLIENT_SECRET` with their respective values, then save and restart the app.

   **XAMPP:** add these directives to Apache's configuration (for example, `xamppfiles/etc/extra/httpd-xampp.conf`):

   ```apache
   SetEnv SPOTIFY_CLIENT_ID "your-client-id"
   SetEnv SPOTIFY_CLIENT_SECRET "your-client-secret"
   ```

3. Restart the web app/server after saving the settings.

The Higher/Lower page fetches artist names, popularity, and images through `server/spotify_api.php`. The endpoint obtains an app access token on the server and never exposes the client secret to the browser.
