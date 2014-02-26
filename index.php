<?php

	mb_internal_encoding("UTF-8");

	require 'Slim/Slim.php';
	require 'libraries/idiorm.php';

	/*
	ORM::configure('mysql:host=127.0.0.1;dbname=zebra');
	ORM::configure('username', 'root');
	ORM::configure('password', '');
	*/

	ORM::configure('mysql:host='.$_SERVER["DB1_HOST"].';dbname='.$_SERVER["DB1_NAME"]);
	ORM::configure('username', $_SERVER["DB1_USER"]);
	ORM::configure('password', $_SERVER["DB1_PASS"]);

	\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim();

	$app->response->headers->set('Content-Type', 'application/json');

	$app->get('/v1/article(/:md5_url)/', function ($md5_url=false) {
		$lies = ORM::for_table('lies');
		if ($md5_url) $lies->where_equal('hash', $md5_url);
		$lies = $lies->find_many();
		$data = array();
		foreach($lies AS $lie) {
			$data[] = $lie->as_array();
		}

		$response['data']   = $data;
		$response['error']  = false;
		$response['status'] = 200;
		echo json_encode($response);
	});

	$app->post('/v1/article/', function () use ($app) {
		$post = $app->request->post();

		if (!isset($post['url']) OR !isset($post['lie'])){
			$response['data']     = array();
			$response['error']    = true;
			$response['message']  = 'Missing required parameters (url and lie)';
			$response['status']   = 400;
			echo json_encode($response);
			return;
		}

		$lie = ORM::for_table('lies')->create();

		// Required fields.
		$lie->url = preg_replace('/\?.*/', '', $post['url']);
		$lie->hash = md5($lie->url);
		$lie->domain = parse_url($lie->url, PHP_URL_HOST);
		$lie->lie = trim($post['lie']);

		// Not required fields
		if (isset($post['title']))   $lie->title   = trim($post['title']);
		if (isset($post['context'])) $lie->context = trim($post['context']);
		if (isset($post['source']))  $lie->source  = trim($post['source']);

		$lie->save();

		$response['data']   = array($lie->as_array());
		$response['error']  = false;
		$response['status'] = 200;
		echo json_encode($response);

	});



	$app->run();