<?php
/**
 * Created by PhpStorm.
 * User: Karst
 * Date: 3-8-2017
 * Time: 22:56
 */

session_start();

include_once "classes/TermLink.php";

use ROBCO\TermLink;

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
    <div class="column column--54 terminal__header">
        <div class="row"><?= $termLink->wrapCharacters("Welcome to ROBCO Industries (TM) Termlink") ?></div>
        <div class="row"></div>
        <div class="row"><?= $termLink->wrapCharacters("Password Required") ?></div>
        <div class="row"></div>
        <div class="row"><?= $termLink->wrapCharacters("Attempts Remaining:") ?>&nbsp;
            <div class="attempts" data-attempts="4"></div>
        </div>
        <div class="row"></div>
    </div>
    <div class="row row--full-width">
        <div class="column column--40" id="entries">
			<?= $termLink->createRows($termLink->code, 12) ?>
        </div>
        <div class="column column--14" id="console">
            <div class="row" id="consoleLine"><span>></span><em></em><span class="caret"></span></div>
        </div>
    </div>
</div>
<script
        src="http://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>
