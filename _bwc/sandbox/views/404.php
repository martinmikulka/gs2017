<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');
?>
<section id="NotFound">
	<div class="content">
		<h1>Oops, stránka nenalezena!</h1>
		<p>Litujeme, ale vámi požadovaná stránka neexistuje. Začněte prosím znovu na <a href="<?php ehtml('/'); ?>">výchozí stránce</a>.</p>
	</div><!-- .content -->
</section><!-- #NotFound -->
