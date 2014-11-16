<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name=robots content=noindex>

<style>
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
	p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
</style>

<title>Website is temporarily out of order</title>

<h1>We're sorry</h1>

<p>Website is temporarily out of order due to maintenance. Please try again later in a few moments. Thank you for your patience.</p>

<?php

exit;
