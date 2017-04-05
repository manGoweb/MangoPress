<?php
/*
  # Common builder core

  - adds settings page, but only if function mango_deploy_getDataset exists
  - triggers deploy on a builder instance
  - shows status
  - provides mango_expandPaths fnc to simplify data structure (by __ delimiter)
  - requires https://github.com/manGoweb/builder running instance

  ## How-To

  1. Implement a payload contruction in the theme/functions.php file:

		function mango_deploy_getDataset() {
			return [
				'templates' => [
					'src/templates/*.jade',
				],
				'data' => [
		 			'site' => mango_expandPaths(get_option('site_settings')),
				],
			];
		}

	2. Fill in configuration paremeters:builder options

	3. Test it and profit

 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

function mango_escape($str)
{
	return htmlspecialchars($str, ENT_COMPAT | ENT_HTML5);
}

function mango_pipeTransformer($key, $value, $transformers) {
	if(!empty($transformers[$key])) {
		return $transformers[$key]($value);
	}
	return $value;
}

function mango_expandPaths($root, $delimiter = NULL, $transformers = []) {
	if(!$delimiter) {
		$delimiter = '__';
	}
	if(!is_array($root)) {
		return $root;
	}
	$updated = [];
	foreach($root as $key => $val) {
		$keyParts = explode($delimiter, (string) $key);
		if(is_string($key) && count($keyParts) > 1) {
			$firstKeyPart = array_shift($keyParts);
			$keyRest = implode($delimiter, $keyParts);
			$updated[$firstKeyPart] = isset($updated[$firstKeyPart]) ? $updated[$firstKeyPart] : [];
			// to-do: optimize
			$updated[$firstKeyPart][$keyRest] = mango_pipeTransformer($keyRest, $val, $transformers);
			$updated[$firstKeyPart] = mango_pipeTransformer($key, mango_expandPaths($updated[$firstKeyPart], $delimiter, $transformers), $transformers);
		}
		else {
			$updated[$key] = mango_pipeTransformer($key, mango_expandPaths($val, $delimiter, $transformers), $transformers);
		}
	}
	return $updated;
}


function mango_deployPage() {
	global $App;
	$config = $App->parameters['builder'];

	view('../admin/builder', [
		"revision" => $config['revision'],
		"subfolder" => $config['aws']['subfolder'],
		"preview" => json_encode(mango_deploy_getDataset(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
	]);
}

function mango_triggerDeploy() {
	global $App;
	$config = $App->parameters['builder'];
	$httpRequest = $App->getByType('Nette\Http\Request');

	$dataset = mango_deploy_getDataset();

	echo "<h1>Starting deploy</h1>";
	dump($httpRequest->getPost());
	echo "<p>It might take a while...</p>";

	ob_end_flush();
	flush();

	$payload = [
		'remote' => $config['remote'],
		'revision' => $httpRequest->getPost('revision'),
		'mango-cli' => $config['mango-cli'],
		'aws' => [
			'bucket' => $config['aws']['bucket'],
			'accessKeyID' => $config['aws']['key'],
			'secretAccessKey' => $config['aws']['secret'],
			'subfolder' => $httpRequest->getPost('subfolder'),
		],
		'dataset' => $dataset,
	];

	$client = new Client([ 'base_uri' => $config['url'], 'timeout'  => 0	]);

	try {
		$response = $client->post('/upload', ['json' => $payload]);
	} catch (BadResponseException $e) {
		$response = $e->getResponse();
	}

	$responseBody = json_decode((string) $response->getBody());

	if ($responseBody->status != 'success') {
		$error = mango_escape($responseBody->message);
		echo "<div style=\"background: lightpink; padding: 20px;\">";
			echo "<h1>Error occurred:</h1>";
			echo "<pre>$error</pre>";
		echo "</div>";
	} else {
		$url = mango_escape($responseBody->url);
		echo "<div style=\"background: lightgreen; padding: 20px;\">";
			echo "<h1>Successfully deployed to:</h1>";
			echo "<a href=\"$url\">$url</a>";
		echo "</div>";
	}

	echo "<br><br><a href=\"admin.php?page=deploy\">&larr; Go back to the Builder</a><br><br>";

}


function mango_addDeployToMenu() {
	if (function_exists('mango_deploy_getDataset')) {
		add_menu_page('Builder Deploy', 'Deploy', 'edit_posts', 'deploy', 'mango_deployPage');
	}
}

add_action('admin_menu', 'mango_addDeployToMenu');
add_action('admin_post_mango_deploy_trigger', 'mango_triggerDeploy');
