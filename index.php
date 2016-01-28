<?php

	require_once "config/Config.php";
	Config::init( __DIR__ );

	$controllerName = !empty( $_REQUEST[ "action" ] ) ? $_REQUEST[ "action" ] . "Controller" : Config::$CONTROLLER_DEFAULT;

	try {
		$controller = new $controllerName();
	} catch ( IndexException $exception ) {
		$controller = new Config::$CONTROLLER_NOT_FOUND();
	}

	try {
		$controller->executeRequest();
	} catch ( ControllerException $exception ) {}