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
					'src/templates/*.pug',
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

use Aws\CloudFront\CloudFrontClient;

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
			"isLocalDeploy" => !empty($config['dataTarget']),
			"dataTarget" => $config['dataTarget'] ?? NULL,
			"revision" => $config['revision'] ?? NULL,
			"subfolder" => $config['aws']['subfolder'] ?? NULL,
			"bucket" => $config['aws']['bucket'] ?? NULL,
			"preview" => Json::encode(createMangowebBuilderDataset(TRUE), Json::PRETTY),
		]);
	}
	
	function doDeploy($tasks = NULL) {
		global $App;
		global $Req;
		global $sitepress;
		
		$config = $App->parameters['builder'];
		$httpRequest = $App->getByType('Nette\Http\Request');
		$subfolder = $config['aws']['subfolder'];
		
		if ($sitepress) {
			$langs = $Req->getPost('deployLang') ? [ $Req->getPost('deployLang') => [] ] : $sitepress->get_active_languages();
			$langDefault = $sitepress->get_default_language();
		} else {
			$langDefault = substr(get_locale(), 0, 2);
			$langs = [ $langDefault => $langDefault ];
		}
		
		$datasets = [];
		
		// Default language dataset
		if(isset($langs[$langDefault])) {
			if ($sitepress) {
				$sitepress->switch_lang($langDefault);
			}
			$datasetDefault = createMangowebBuilderDataset();
			$datasetDefault['data']['language'] = $langDefault; // inject current language
			$datasetDefault['data']['languages'] = $langs; // inject languages object
			$datasets['default'] = $datasetDefault;
		}
		
		// Get aditional languages
		foreach ($langs as $key => $lang) {
			if ($key != $langDefault) {
				if ($sitepress) {
					$sitepress->switch_lang($key);
				}
				
				$dataset = createMangowebBuilderDataset();
				$dataset['data']['language'] = $key; // inject current language
				$dataset['data']['languages'] = $langs; // inject languages object
				$datasets[$key] = $dataset;
			}
		}
		
		if ($sitepress) {
			$sitepress->switch_lang($langDefault); // switch back to the default to be sure
		}
		
		// Iterate over each language specific dataset and do a separate deploy
		ini_set('max_execution_time', 900); // try to negotiate longer time execution limit for our deploy
		$status = 'success';
		$statuses = [];
		$deployType = !empty($config['dataTarget']) ? 'local' : 'remote';
		foreach ($datasets as $lang => $dataset) {
			// Local deploy option, stops the upload process and writes JSON file to disk
			if(!empty($config['dataTarget'])) {
				$filepath = $config['dataTarget'];
				$filepath = \Nette\Utils\Strings::replace($filepath, '~\{lang\}~', $lang);
				if (!file_exists(dirname($filepath))) {
					$statuses[$lang] = [ 'message' => 'Directory doesn\'t exists' ];
				} else {
					$json = Json::encode($dataset, Json::PRETTY);
					file_put_contents($filepath, $json);
					$statuses[$lang] = [ 'message' => 'Written out to ' . $filepath ];
					$buildId = md5($json);
				}
			} else {
				$folder = $subfolder . ($lang === 'default' ? '' : ($subfolder ? '/' . $lang : $lang));
				$payload = [
					'remote' => $config['remote'],
					'revision' => $config['revision'],
					'mango-cli' => $config['mango-cli'],
					'tasks' => $tasks ? $tasks : $config['tasks'],
					'aws' => [
						'bucket' => $config['aws']['bucket'],
						'accessKeyID' => $config['aws']['key'],
						'secretAccessKey' => $config['aws']['secret'],
						'subfolder' => $folder,
					],
					'dataset' => $dataset,
				];
				
				$client = new Client([ 'base_uri' => $config['url'], 'timeout'  => 0	]);
				
				try {
					$response = $client->post('/upload?async=pravda', ['json' => $payload]);
				} catch (BadResponseException $e) {
					$response = $e->getResponse();
				}
				$responseData = json_decode($response->getBody(), TRUE);
				$statuses[$lang] = $responseData;
				
				$buildId = $responseData['id'];
				$session = $App->getByType('Nette\Http\Session')->getSection('mangoDeploy');
				$session->buildId = $buildId;
			}
		}
		
		return [ 'statuses' => $statuses, 'deployType' => $deployType, 'id' => $buildId ];
	}
	
	function triggerDeploy() {
		sendPayload(doDeploy());
	}
	
	function triggerTemplatesDeploy() {
		sendPayload(doDeploy('templates'));
	}
	
	add_action('admin_menu', function() {
		add_menu_page('Builder Deploy', 'Deploy', 'edit_posts', 'deploy', 'Mangoweb\\Builder\\deployPage');
	});
	
	
	// State watcher
	function ajaxDeployStatus() {
		global $App;
		$httpRequest = $App->getByType('Nette\Http\Request');
		$buildId = $httpRequest->getPost('buildId');
		$config = $App->parameters['builder'];
		if(empty($buildId) || empty($config['url'])) {
			sendPayload([ 'error' => 'build not available' ]);
			return;
		}
		$client = new Client([ 'base_uri' => $config['url'], 'timeout'  => 0 ]);
		try {
			$res = $client->get('/status?id=' . $buildId);
		} catch (BadResponseException $e) {
			$res = $e->getResponse();
		}
		sendPayload(json_decode($res->getBody(), true));
	}
	
	add_action( 'wp_ajax_mango_deploy_status', 'Mangoweb\\Builder\\ajaxDeployStatus' );
	add_action( 'wp_ajax_mango_deploy_trigger', 'Mangoweb\\Builder\\triggerDeploy' );
	add_action( 'wp_ajax_mango_deploy_trigger_templates', 'Mangoweb\\Builder\\triggerTemplatesDeploy' );
	
	/**
	 * Customize WordPress Toolbar
	 *
	 * @param obj $wp_admin_bar An instance of the global object WP_Admin_Bar
	 */
	function customizeToolbar( $wp_admin_bar ){
		global $sitepress;
		
		if(!$sitepress) {
			return;
		}
		
		$wp_admin_bar->add_node( [
			'id'		=> 'deploy-menu',
			'title'		=> "<div class='deploy'><select id='mango_deploy_lang' name='deployLang'>
					<option value=''>all languages</option>
					" . implode("", array_map(function ($code) {return "<option value='$code'>only $code</option>";}, array_keys($sitepress->get_active_languages()))) . "
				</select>

				<button class='deploy-button' id='mango_trigger_deploy'>Trigger deploy now</button>
				<div class='deploy-status' id='mango_deploy_status'></div>
				<script>
					$ = jQuery
					var interval = null
					function mangoStatusUpdate(id) {
						$.post(ajaxurl, {action: 'mango_deploy_status', buildId: id}, function (data) {
							document.getElementById('mango_deploy_status').setAttribute('title', JSON.stringify(data))
							document.getElementById('mango_deploy_status').textContent = data.status || '?'
							if(data.status == 'error' || data.status == 'success') {
								clearInterval(interval)
							}
						})
					}
					function mangoStartWatchStatus(id) {
						clearInterval(interval)
						mangoStatusUpdate(id)
						interval = setInterval(function () {
							mangoStatusUpdate(id)
						}, 3000)
					}
					$(document.getElementById('mango_trigger_deploy')).on('click', function () {
						var lang = document.getElementById('mango_deploy_lang').value
						document.getElementById('mango_deploy_status').textContent = 'waiting'
						$.post(ajaxurl, {action: 'mango_deploy_trigger', deployLang: lang}, function (data) {
							sessionStorage.setItem('mango_depoy_buildId', data.id);
							mangoStartWatchStatus(data.id)
						})
					})
					if(sessionStorage.getItem('mango_depoy_buildId')) {
						mangoStartWatchStatus(sessionStorage.getItem('mango_depoy_buildId'))
					}

				</script></div>",
		] );
	}
	add_action( 'admin_bar_menu', 'Mangoweb\\Builder\\customizeToolbar', 999 );
	
	// Autodeploy on publish
	// Saves buildId to session
	function handlePublishPost($ID, $post) {
		if(stringToBool(get_option('mango_deploy_autodeploy', FALSE))) {
			doDeploy('templates');
		}
	}
	add_action('save_post', 'Mangoweb\\Builder\\handlePublishPost', 10, 2);
	
	// Adds inline script to page to save buildId to session storage
	// Not doing it in handlePublishPost, because there is a redirect
	function autodeployAddToSessionStorage() {
		global $App;
		$sess = $App->getByType('Nette\Http\Session');
		if($sess->hasSection('mangoDeploy')) {
			$buildId = $sess->getSection('mangoDeploy')->buildId;
			if($buildId) {
				echo "<script>sessionStorage.setItem('mango_depoy_buildId', '$buildId')</script>";
				unset($sess->getSection('mangoDeploy')->buildId);
			}
		}
	}
	add_action('admin_print_scripts', 'Mangoweb\\Builder\\autodeployAddToSessionStorage');
	
	function stringToBool($str) {
		if($str === FALSE || strtolower($str) == 'false') {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	add_action( 'wp_ajax_mango_deploy_autodeploy_set', function() {
		global $App;
		$httpRequest = $App->getByType('Nette\Http\Request');
		$enable = $httpRequest->getPost('enable');
		sendPayload([
			'updated' => update_option('mango_deploy_autodeploy', $enable, TRUE)
		]);
	});
	
	function getAutodeployOption() {
		return stringToBool(get_option('mango_deploy_autodeploy', FALSE));
	}
}
