<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');
?>
<div id="NotFound" class="section">
    <div class="wrapper">
        <h1><?php ehtml('Oops, stránka nenalezena!'); ?></h1>
        <p><?php ehtml('Litujeme, ale vámi požadovaná stránka neexistuje. Začněte prosím znovu na'); ?> <a href="<?php ehtml('/'); ?>"><?php ehtml('výchozí stránce'); ?></a>.</p>
    </div>
</div>
