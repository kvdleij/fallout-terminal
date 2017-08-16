<div class="column column--54 terminal__header">
    <div class="row"><?= $termLink->wrapCharacters("Welcome to ROBCO Industries (TM) Termlink", false, 0, true) ?></div>
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