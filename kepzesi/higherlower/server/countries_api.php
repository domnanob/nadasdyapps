<?php

header('Content-Type: application/json; charset=utf-8');

$url = 'https://api.worldbank.org/v2/country/all/indicator/AG.SRF.TOTL.K2?format=json&mrv=1&per_page=1000';
$context = stream_context_create([
    'http' => [
        'timeout' => 15,
        'user_agent' => 'HigherLowerGame/1.0',
    ],
]);

$response = @file_get_contents($url, false, $context);
if ($response === false) {
    $error = error_get_last();
    error_log('Could not load country area data: ' . ($error['message'] ?? 'Unknown network error'));
    http_response_code(502);
    echo json_encode(['error' => 'Az országadatok jelenleg nem érhetők el.']);
    exit;
}

$data = json_decode($response, true);
if (!is_array($data) || !isset($data[1]) || !is_array($data[1])) {
    error_log('Country area data source returned an invalid response.');
    http_response_code(502);
    echo json_encode(['error' => 'Az országadatok forrása érvénytelen választ adott.']);
    exit;
}

$countries = [];
foreach ($data[1] as $country) {
    $code = $country['country']['id'] ?? '';
    $name = $country['country']['value'] ?? '';
    $area = $country['value'] ?? null;

    if (!is_string($code) || !preg_match('/^[A-Z]{2}$/', $code) ||
        !is_string($name) || $name === '' || !is_numeric($area)) {
        continue;
    }

    $countries[] = [
        'code' => $code,
        'name' => $name,
        'value' => (float) $area,
        'img' => 'https://purecatamphetamine.github.io/country-flag-icons/3x2/' . $code . '.svg',
    ];
}

if (count($countries) < 2) {
    error_log('Country area data source returned fewer than two usable countries.');
    http_response_code(502);
    echo json_encode(['error' => 'Az országadatok forrása nem adott elegendő adatot.']);
    exit;
}

echo json_encode($countries);
