<?php
/**
 * Created by PhpStorm.
 * User: Karst
 * Date: 16-8-2017
 * Time: 17:49
 */

session_start();

include_once "classes/TermLink.php";

use ROBCO\TermLink;

$request = $_SERVER["REQUEST_URI"];
$params = explode("/", $request);

$difficulty = TermLink::DIFF_NOVICE;

if (filter_has_var(INPUT_GET, "diff")) {
	$difficulty = filter_input(INPUT_GET, "diff", FILTER_SANITIZE_NUMBER_INT);
	if ($difficulty < 0 || $difficulty > 3) {
		$difficulty = TermLink::DIFF_NOVICE;
	}
}

$termLink = new TermLink($difficulty);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ROBCO Industries (TM) Termlink</title>
    <link rel="stylesheet" href="scss/styles.css">
</head>
<body>
<div class="grid">

	<?php
	if (!empty($params[1])) {
		include_once($params[1] . ".php");
	} else {
		?>
        <div class="column column--54 terminal__header">
            <div class="row"><?= $termLink->wrapCharacters("Welcome to ROBCO Industries (TM) Termlink") ?></div>
            <div class="row"></div>
            <div class="row"><?= $termLink->wrapCharacters(">SET TERMINAL/INQUIRE") ?></div>
            <div class="row"></div>
            <div class="row"><?= $termLink->wrapCharacters("RIT-V300") ?></div>
            <div class="row"></div>
            <div class="row"><?= $termLink->wrapCharacters(">SET FILE/PROTECTION=OWNER:RWED ACCOUNTS.F") ?></div>
            <div class="row"><?= $termLink->wrapCharacters(">SET HALT RESTART/MAINT") ?></div>
            <div class="row"></div>
            <div class="row"><?= $termLink->wrapCharacters("Initializing Robco Industries(TM) MF Boot Agent v2.3.0") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("RETROS BIOS") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("RBIOS-4.02.08.00 52EE5.E7.E8") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("Copyright 2201-2203 Robci Ind.") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("Uppermem: 64 KB") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("Root (5A8)") ?></div>
            <div class="row"><?= $termLink->wrapCharacters("Maintenance Mode") ?></div>
            <div class="row"></div>
            <div class="row"><?= $termLink->wrapCharacters(">RUN DEBUG/ACCOUNTS.F") ?></div>
            <div class="row"></div>
            <div class="row menu"><?= $termLink->wrapCharacters("[ INSTRUCTIONS ]") ?></div>
            <div class="row menu"><?= $termLink->wrapCharacters("[ HACK TERMINAL ]") ?></div>
        </div>
	<?php } ?>

</div>
<script
        src="http://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>