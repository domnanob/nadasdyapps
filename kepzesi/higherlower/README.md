# Spotify mode setup

Spotify mode uses the Spotify Web API and compares its artist popularity score (0–100), not monthly listeners. The API credentials must be kept on the server; do not put them in JavaScript or commit them to Git.

1. Create an app in the [Spotify Developer Dashboard](https://developer.spotify.com/dashboard) and copy its client ID and client secret.
2. Add these directives to XAMPP's Apache configuration (for example, `xamppfiles/etc/extra/httpd-xampp.conf`):

   ```apache
   SetEnv SPOTIFY_CLIENT_ID "your-client-id"
   SetEnv SPOTIFY_CLIENT_SECRET "your-client-secret"
   ```

3. Restart Apache in the XAMPP control panel.

The Higher/Lower page fetches artist names, popularity, and images through `server/spotify_api.php`. The endpoint obtains an app access token on the server and never exposes the client secret to the browser.
