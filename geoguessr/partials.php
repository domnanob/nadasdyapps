<?php
declare(strict_types=1);

function render_head(string $title, bool $noindex = false, string $src=""): void
{ ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <?php if ($noindex): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
    <meta name="theme-color" content="#090a1f">
    <link rel="icon" type="image/png" href="assets/school-crest.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e($src) ?>assets/style.css">
</head>
<body>
<?php }

/** Neon csíkok és izzó pöttyök a háttérben, mint a plakáton. */
function render_streaks(): void
{
    $lines = [
        [-40, 760, 260, 200, 5], [-100, 520, 120, 120, 3], [40, 990, 330, 430, 2],
        [700, 900, 1040, 240, 6], [870, 1010, 1110, 520, 3], [790, 390, 990, 60, 2],
    ];
    ?>
<svg class="streaks" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs>
        <linearGradient id="streakGrad" x1="0" y1="1" x2="1" y2="0">
            <stop offset="0" stop-color="#7c3aed" stop-opacity="0"/>
            <stop offset=".55" stop-color="#c5a3ff" stop-opacity=".9"/>
            <stop offset=".82" stop-color="#ffffff"/>
            <stop offset="1" stop-color="#e23cf5" stop-opacity="0"/>
        </linearGradient>
        <filter id="streakBlur" x="-30%" y="-30%" width="160%" height="160%">
            <feGaussianBlur stdDeviation="10"/>
        </filter>
    </defs>
    <g filter="url(#streakBlur)" opacity=".75">
        <?php foreach ($lines as [$x1, $y1, $x2, $y2, $w]): ?>
            <line x1="<?= $x1 ?>" y1="<?= $y1 ?>" x2="<?= $x2 ?>" y2="<?= $y2 ?>" stroke="url(#streakGrad)" stroke-width="<?= $w * 4 ?>" stroke-linecap="round"/>
        <?php endforeach; ?>
    </g>
    <g>
        <?php foreach ($lines as [$x1, $y1, $x2, $y2, $w]): ?>
            <line x1="<?= $x1 ?>" y1="<?= $y1 ?>" x2="<?= $x2 ?>" y2="<?= $y2 ?>" stroke="url(#streakGrad)" stroke-width="<?= $w ?>" stroke-linecap="round"/>
        <?php endforeach; ?>
    </g>
    <g fill="#ff4b5c">
        <?php
        $dots = [[610,330,2],[655,372,1.5],[560,415,1.5],[700,300,1.2],[520,300,1.2],[740,440,1.6],[480,470,1.4],[690,500,1.2],[420,360,1.2],[790,330,1.4]];
        foreach ($dots as [$cx, $cy, $r]): ?>
            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>" opacity=".55"/>
        <?php endforeach; ?>
    </g>
</svg>
<?php }

/** A plakát pajzsos "Nádasdy Cup" emblémája. */
function render_crest(): void
{
    $shield = 'M150 24 L252 58 Q272 65 272 88 L272 208 Q272 272 150 338 Q28 272 28 208 L28 88 Q28 65 48 58 Z';
    ?>
<svg class="crest" viewBox="0 -22 300 366" role="img" aria-label="GeoGuessr Nádasdy Cup 2026">
    <defs>
        <linearGradient id="shieldFill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#7a3cf5"/>
            <stop offset=".55" stop-color="#3c14a3"/>
            <stop offset="1" stop-color="#1c0a5c"/>
        </linearGradient>
        <clipPath id="shieldClip"><path d="<?= $shield ?>"/></clipPath>
    </defs>
    <path d="<?= $shield ?>" fill="url(#shieldFill)" stroke="#ffffff" stroke-width="10" stroke-linejoin="round"/>
    <rect x="150" y="0" width="150" height="360" fill="#12063f" opacity=".35" clip-path="url(#shieldClip)"/>
    <path d="<?= $shield ?>" fill="none" stroke="#9a6bff" stroke-width="3" transform="translate(150 190) scale(.9) translate(-150 -190)"/>
    <g class="crest-text" transform="rotate(-8 150 200)">
        <text x="160" y="120" text-anchor="middle" font-size="17" textLength="130" lengthAdjust="spacingAndGlyphs">GEOGUESSR</text>
        <text x="160" y="180" text-anchor="middle" font-size="46" textLength="200" lengthAdjust="spacingAndGlyphs">NÁDASDY</text>
        <text x="150" y="230" text-anchor="middle" font-size="56" textLength="120" lengthAdjust="spacingAndGlyphs">CUP</text>
    </g>
    <text class="crest-text" x="150" y="300" text-anchor="middle" font-size="22">’26</text>
    <g class="pin">
        <path d="M150 -14 a34 34 0 0 1 34 34 c0 22 -22 38 -34 54 c-12 -16 -34 -32 -34 -54 a34 34 0 0 1 34 -34z"
              fill="#ff2d3f" stroke="#ffffff" stroke-width="6" stroke-linejoin="round"/>
        <circle cx="150" cy="20" r="15" fill="#ffffff"/>
        <circle cx="150" cy="20" r="7.5" fill="#ff2d3f"/>
    </g>
</svg>
<?php }
