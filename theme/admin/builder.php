<?php
/*
  # Common builder core

  - adds settings page, but only if function createMangowebBuilderDataset exists
  - triggers deploy on a builder instance
  - shows status
  - provides expandPaths fnc to simplify data structure (by __ delimiter)
  - requires https://github.com/manGoweb/builder running instance

  ## How-To

  1. Implement a payload contruction in the theme/functions.php file:

		function createMangowebBuilderDataset() {
			return [
				'templates' => [
					'src/templates/*.jade',
				],
				'data' => [
		 			'site' => Mangoweb\Builder\expandPaths(get_option('site_settings')),
				],
			];
		}

	2. Fill in configuration paremeters:builder options

	3. Test it and profit

 */

namespace Mangoweb\Builder;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

use Nette\Utils\Json;

if (function_exists('createMangowebBuilderDataset')) {

	function pipeTransformer($key, $value, $transformers) {
		if(!empty($transformers[$key])) {
			return $transformers[$key]($value);
		}
		return $value;
	}

	function expandPaths($root, $delimiter = NULL, $transformers = []) {
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
				$updated[$firstKeyPart] = $updated[$firstKeyPart] ?? [];
				// to-do: optimize
				$updated[$firstKeyPart][$keyRest] = pipeTransformer($keyRest, $val, $transformers);
				$updated[$firstKeyPart] = pipeTransformer($key, expandPaths($updated[$firstKeyPart], $delimiter, $transformers), $transformers);
			}
			else {
				$updated[$key] = pipeTransformer($key, expandPaths($val, $delimiter, $transformers), $transformers);
			}
		}
		return $updated;
	}


	function deployPage() {
		global $App;
		$config = $App->parameters['builder'];

		view('../admin/builder', [
			"revision" => $config['revision'],
			"subfolder" => $config['aws']['subfolder'],
			"preview" => Json::encode(createMangowebBuilderDataset(), Json::PRETTY),
		]);
	}

	function triggerDeploy() {
		global $App;
		$config = $App->parameters['builder'];
		$httpRequest = $App->getByType('Nette\Http\Request');

		$dataset = createMangowebBuilderDataset();

		echo "<h1>Starting deploy</h1>";
		\Tracy\Dumper::dump($httpRequest->getPost());
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

		$responseBody = Json::decode((string) $response->getBody());

		$status = $responseBody->status === 'success' ? 'success' : 'error';

		$params = [
			'status' => $status,
			'error' => $status === 'error' ? $responseBody->message : NULL,
			'url' => $status === 'success' ? $responseBody->url : NULL,
		];

		view('../admin/builderResult', $params);
	}

	add_action('admin_menu', function() {
		add_menu_page('Builder Deploy', 'Deploy', 'edit_posts', 'deploy', 'Mangoweb\\Builder\\deployPage');
	});

	add_action('admin_post_mango_deploy_trigger', 'Mangoweb\\Builder\\triggerDeploy');

}
