<?php

header('HTTP/1.1 503 Service Unavailable');

?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1">

<style>
body { color: #333; background: white; font: normal 20px/1.5 sans-serif }
h1 { font-size: 2em; margin: 0 }
p { margin: 0 }
.msg { max-width: 500px; margin: 10% auto }
</style>

<title>Site is temporarily down for maintenance</title>

<div class="msg">
	<h1>We're Sorry</h1>
	<p>The site is temporarily down for maintenance.<br>Please try again in a few minutes.</p>
</div>

<script type="text/javascript">
	function ping() {
		var httpRequest = new XMLHttpRequest();
		if (httpRequest) {
			httpRequest.onreadystatechange = function() {
				if (httpRequest.readyState === XMLHttpRequest.DONE) {
					if (httpRequest.status === 200) {
						location.reload();
					} else {
						setTimeout(function(){
							ping();
						}, 1000);
					}
				}
			};
			httpRequest.open('GET', '');
			httpRequest.send();
		}
	}
	ping();
</script>

<?php

exit;
