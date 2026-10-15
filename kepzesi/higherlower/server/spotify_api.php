<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['error' => 'Ez a végpont csak GET kéréseket fogad.']);
    exit;
}

$clientId = getenv('SPOTIFY_CLIENT_ID');
$clientSecret = getenv('SPOTIFY_CLIENT_SECRET');
if (!is_string($clientId) || $clientId === '' || !is_string($clientSecret) || $clientSecret === '') {
    error_log('Spotify API credentials are not configured.');
    http_response_code(503);
    echo json_encode([
        'error' => 'A Spotify API nincs beállítva. Add meg a SPOTIFY_CLIENT_ID és SPOTIFY_CLIENT_SECRET Apache környezeti változókat, majd indítsd újra az Apache-ot.',
    ]);
    exit;
}

$request = static function (string $url, array $headers, ?string $body = null): array {
    $options = [
        'http' => [
            'method' => $body === null ? 'GET' : 'POST',
            'header' => implode("\r\n", $headers),
            'timeout' => 15,
            'ignore_errors' => true,
        ],
    ];
    if ($body !== null) {
        $options['http']['content'] = $body;
    }

    $http_response_header = [];
    $response = @file_get_contents($url, false, stream_context_create($options));
    $status = 0;
    foreach ($http_response_header as $headerLine) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $headerLine, $matches)) {
            $status = (int) $matches[1];
        }
    }

    if ($response === false) {
        $error = error_get_last();
        error_log('Spotify API request failed: ' . ($error['message'] ?? 'Unknown network error'));
    }

    return ['status' => $status, 'body' => $response];
};

$tokenResponse = $request(
    'https://accounts.spotify.com/api/token',
    [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
    ],
    http_build_query(['grant_type' => 'client_credentials'])
);
$tokenData = is_string($tokenResponse['body'])
    ? json_decode($tokenResponse['body'], true)
    : null;

if (
    $tokenResponse['status'] !== 200
    || !is_array($tokenData)
    || !isset($tokenData['access_token'])
    || !is_string($tokenData['access_token'])
) {
    error_log('Spotify token request returned HTTP ' . $tokenResponse['status'] . '.');
    http_response_code(502);
    echo json_encode(['error' => 'A Spotify hitelesítése nem sikerült. Ellenőrizd a szerver API-beállításait.']);
    exit;
}

$genres = ['pop', 'rock', 'hip-hop', 'indie', 'electronic', 'country', 'jazz', 'latin'];
$genre = $genres[random_int(0, count($genres) - 1)];
$query = http_build_query([
    'q' => 'genre:' . $genre,
    'type' => 'artist',
    'limit' => 50,
    'market' => 'HU',
]);
$artistsResponse = $request(
    'https://api.spotify.com/v1/search?' . $query,
    [
        'Authorization: Bearer ' . $tokenData['access_token'],
        'Accept: application/json',
    ]
);
$artistsData = is_string($artistsResponse['body'])
    ? json_decode($artistsResponse['body'], true)
    : null;

if (
    $artistsResponse['status'] !== 200
    || !is_array($artistsData)
    || !isset($artistsData['artists']['items'])
    || !is_array($artistsData['artists']['items'])
) {
    error_log('Spotify artist search returned HTTP ' . $artistsResponse['status'] . '.');
    http_response_code(502);
    echo json_encode(['error' => 'A Spotify előadói adatai jelenleg nem érhetők el. Próbáld újra később.']);
    exit;
}

$items = [];
foreach ($artistsData['artists']['items'] as $artist) {
    $name = $artist['name'] ?? null;
    $popularity = $artist['popularity'] ?? null;
    $image = $artist['images'][0]['url'] ?? null;
    $spotifyUrl = $artist['external_urls']['spotify'] ?? null;

    if (
        !is_string($name) || $name === ''
        || !is_int($popularity) || $popularity < 0 || $popularity > 100
        || !is_string($image) || filter_var($image, FILTER_VALIDATE_URL) === false
        || !is_string($spotifyUrl) || filter_var($spotifyUrl, FILTER_VALIDATE_URL) === false
    ) {
        continue;
    }

    $items[] = [
        'name' => $name,
        'value' => $popularity,
        'img' => $image,
        'url' => $spotifyUrl,
    ];
}

if (count($items) < 2) {
    error_log('Spotify artist search returned fewer than two usable artists.');
    http_response_code(502);
    echo json_encode(['error' => 'A Spotify-adatforrás most nem adott elegendő előadót. Próbáld újra.']);
    exit;
}

shuffle($items);
echo json_encode(
    array_slice($items, 0, 30),
    JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
);
