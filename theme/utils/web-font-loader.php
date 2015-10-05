<?php

function web_fonts($config = null) {
	if(!$config) {
		return '';
	}
	$deferFlags = ' async defer';
	if(!empty($_COOKIE['webfonts-preloaded'])) {
		$deferFlags = '';
	}
	return Nette\Utils\Html::el()->setHtml('
<script>
  ;(function(){ WebFontConfig = ' . json_encode($config) . ';
  var el = document.documentElement;
  el.className += " wf-loading";
  setTimeout(function() {
      el.className = el.className.replace(/(^|\s)wf-loading(\s|$)/g, " ");
  }, 800)})();
</script>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js"' . $deferFlags . '></script>');
}

function use_typekit($id = null) {
	if(!$id) {
		return '';
	}
	return web_fonts([ 'typekit' => [ 'id' => $id ]]);
}

function use_google_fonts($families = null) {
	if(!$families) {
		return '';
	}
	$families = (array) $families;
	return web_fonts([ 'google' => [ 'families' => $families ]]);
}
