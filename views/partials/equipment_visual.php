<?php
$visualName = strtr(mb_strtolower($equipment['name'] ?? ''), [
    'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
    'à' => 'a', 'â' => 'a', 'î' => 'i', 'ï' => 'i',
    'ô' => 'o', 'ù' => 'u', 'û' => 'u', 'ç' => 'c',
]);
$visualType = 'toolbox';
if (str_contains($visualName, 'projecteur') || str_contains($visualName, 'ecran')) {
    $visualType = 'projector';
} elseif (str_contains($visualName, 'enceinte') || str_contains($visualName, 'haut-parleur')) {
    $visualType = 'speaker';
} elseif (str_contains($visualName, 'microphone')) {
    $visualType = 'microphone';
} elseif (str_contains($visualName, 'camera')) {
    $visualType = 'camera';
} elseif (str_contains($visualName, 'stabilisateur')) {
    $visualType = 'stabilizer';
} elseif (str_contains($visualName, 'trepied')) {
    $visualType = 'tripod';
} elseif (str_contains($visualName, 'lumiere')) {
    $visualType = 'light';
} elseif (str_contains($visualName, 'console') || str_contains($visualName, 'mixage')) {
    $visualType = 'mixer';
} elseif (str_contains($visualName, 'scie')) {
    $visualType = 'saw';
} elseif (str_contains($visualName, 'perforateur') || str_contains($visualName, 'perceuse')) {
    $visualType = 'drill';
} elseif (str_contains($visualName, 'laser') || str_contains($visualName, 'niveau')) {
    $visualType = 'level';
} elseif (str_contains($visualName, 'nettoyeur') || str_contains($visualName, 'aspirateur')) {
    $visualType = 'cleaner';
} elseif (str_contains($visualName, 'echelle')) {
    $visualType = 'ladder';
}
?>
<div class="equipment-visual equipment-visual-<?= htmlspecialchars($visualType) ?>" role="img" aria-label="Illustration de <?= htmlspecialchars($equipment['name']) ?>">
    <span class="equipment-visual-glow" aria-hidden="true"></span>
    <svg viewBox="0 0 240 160" aria-hidden="true" focusable="false">
        <?php if ($visualType === 'projector'): ?>
            <path class="visual-shadow" d="M56 126h129" />
            <rect class="visual-dark" x="48" y="52" width="118" height="62" rx="15" />
            <path class="visual-highlight" d="M68 64h52" />
            <circle class="visual-lens" cx="157" cy="83" r="20" />
            <circle class="visual-lens-core" cx="157" cy="83" r="9" />
            <path class="visual-detail" d="M83 115v9m54-9v9" />
        <?php elseif ($visualType === 'speaker'): ?>
            <path class="visual-shadow" d="M72 130h99" />
            <rect class="visual-dark" x="77" y="29" width="84" height="102" rx="17" />
            <circle class="visual-speaker" cx="119" cy="66" r="19" />
            <circle class="visual-speaker-core" cx="119" cy="66" r="7" />
            <circle class="visual-speaker" cx="119" cy="105" r="12" />
            <circle class="visual-speaker-core" cx="119" cy="105" r="4" />
        <?php elseif ($visualType === 'microphone'): ?>
            <path class="visual-shadow" d="M74 130h93" />
            <rect class="visual-dark" x="102" y="27" width="36" height="78" rx="18" />
            <path class="visual-grille" d="M108 43h24m-24 9h24m-24 9h24" />
            <path class="visual-detail" d="M91 77v8a29 29 0 0 0 58 0v-8m-29 37v18m-20 0h40" />
        <?php elseif ($visualType === 'camera'): ?>
            <path class="visual-shadow" d="M53 125h134" />
            <path class="visual-dark" d="M55 54h35l12-16h43l13 16h26a12 12 0 0 1 12 12v45a12 12 0 0 1-12 12H55a12 12 0 0 1-12-12V66a12 12 0 0 1 12-12Z" />
            <circle class="visual-lens" cx="122" cy="84" r="29" />
            <circle class="visual-lens-core" cx="122" cy="84" r="16" />
            <circle class="visual-highlight-dot" cx="177" cy="68" r="5" />
        <?php elseif ($visualType === 'stabilizer'): ?>
            <path class="visual-shadow" d="M54 129h132" />
            <path class="visual-metal" d="M117 53V36h51v20m-33 1-16 24m25-24 17 24m-28 0v37m-15-37h30" />
            <rect class="visual-dark" x="89" y="68" width="61" height="33" rx="8" />
            <circle class="visual-lens" cx="141" cy="84" r="12" />
        <?php elseif ($visualType === 'tripod'): ?>
            <path class="visual-shadow" d="M73 130h94" />
            <rect class="visual-dark" x="91" y="36" width="58" height="35" rx="7" />
            <circle class="visual-lens" cx="144" cy="53" r="11" />
            <path class="visual-metal" d="m120 73-36 53m36-53 37 53m-37-53v53" />
        <?php elseif ($visualType === 'light'): ?>
            <path class="visual-shadow" d="M69 131h103" />
            <path class="visual-metal" d="M120 62v68m-24 0h48m-24-68L83 38m37 24 37-24" />
            <path class="visual-light" d="M74 29h30l-9 29H83Zm62 0h30l-9 29h-12Z" />
            <path class="visual-detail" d="m84 35 5 16m54-16-5 16" />
        <?php elseif ($visualType === 'mixer'): ?>
            <path class="visual-shadow" d="M46 124h148" />
            <path class="visual-dark" d="m68 42 100 8 11 66-110-7Z" />
            <path class="visual-control" d="m87 62 4 36m18-34 4 37m18-35 4 37m18-35 4 37" />
            <path class="visual-highlight" d="m79 55 74 6" />
            <circle class="visual-light-dot" cx="162" cy="70" r="4" />
            <circle class="visual-light-dot" cx="166" cy="86" r="4" />
        <?php elseif ($visualType === 'saw'): ?>
            <path class="visual-shadow" d="M52 127h136" />
            <path class="visual-dark" d="m70 77 73-34 37 31-20 42-78-8Z" />
            <circle class="visual-metal" cx="130" cy="94" r="25" />
            <path class="visual-teeth" d="m121 70 4-7 5 7m13 7 8-1-3 8m-1 18 2 8-8-1m-15 9-5 7-5-7m-13-8-8 1 3-8m1-18-2-8 8 1" />
            <path class="visual-handle" d="m70 77-11-18 17-9 15 18" />
        <?php elseif ($visualType === 'drill'): ?>
            <path class="visual-shadow" d="M56 126h127" />
            <path class="visual-dark" d="M62 59h87a15 15 0 0 1 0 30h-23l-2 38h-29l-7-38H62a15 15 0 0 1 0-30Z" />
            <path class="visual-metal" d="M149 66h42v16h-42m42 8h20" />
            <path class="visual-highlight" d="M76 67h49" />
            <path class="visual-handle" d="M96 91h28l-3 26h-19Z" />
        <?php elseif ($visualType === 'level'): ?>
            <path class="visual-shadow" d="M55 124h130" />
            <rect class="visual-metal" x="52" y="67" width="136" height="28" rx="10" />
            <path class="visual-highlight" d="M67 76h39m42 10h24" />
            <circle class="visual-bubble" cx="124" cy="81" r="10" />
            <path class="visual-bubble-line" d="M120 81h8" />
        <?php elseif ($visualType === 'cleaner'): ?>
            <path class="visual-shadow" d="M63 130h118" />
            <path class="visual-dark" d="M88 61h67l13 57H75Z" />
            <path class="visual-metal" d="M101 61V45a20 20 0 0 1 40 0v16m13 13 27 9m-3 0-9 34" />
            <circle class="visual-wheel" cx="92" cy="122" r="8" />
            <circle class="visual-wheel" cx="151" cy="122" r="8" />
        <?php elseif ($visualType === 'ladder'): ?>
            <path class="visual-shadow" d="M68 130h104" />
            <path class="visual-metal" d="m88 29-19 99m83-99 19 99m-87-78h67m-72 24h76m-81 24h86m-91 24h96" />
            <path class="visual-highlight" d="m99 38-15 81m53-81 15 81" />
        <?php else: ?>
            <path class="visual-shadow" d="M52 127h136" />
            <rect class="visual-dark" x="61" y="55" width="118" height="70" rx="13" />
            <path class="visual-highlight" d="M79 55V44a12 12 0 0 1 12-12h58a12 12 0 0 1 12 12v11" />
            <path class="visual-detail" d="M61 83h118m-59 0v42" />
            <circle class="visual-light-dot" cx="161" cy="69" r="4" />
        <?php endif; ?>
    </svg>
    <span class="equipment-visual-caption"><?= htmlspecialchars($equipment['name']) ?></span>
</div>
