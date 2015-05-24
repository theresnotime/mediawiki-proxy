<?php
namespace Wikimedia\TorProxy;

session_start();

include_once 'autoload.php';
require 'config.php';

$output = new Output( $TorProxyTemplateConfig );

try {
	$logger = new Logger( $TorProxyLogConfig );
	$db = new Database( $TorProxyDBConfig, $logger );

	$conn = array(
		'ip' => $_SERVER['REMOTE_ADDR'],
		'ua' => $_SERVER['HTTP_USER_AGENT'],
	);

	$config = Settings::getInstance();
	$config->setVars(
		$db,
		$logger,
		$TorProxyOAuthConfig,
		$TorProxyWikiConfig,
		$TorProxyConfig,
		$conn
	);


	$user = User::getUser( $db );

	$action = isset( $_GET['action'] ) ? $_GET['action'] : 'anon';
	$request = $_REQUEST;

	$handler = ActionHandlerFactory::getHandler( $action );
	$handler->checkAccess( $user );
	$handler->checkAccess( $user );

	$handler->process( $user, $request, $output, $config );
} catch ( \Exception $e ) {
	$output->addTemplate(
		'msgerror',
		array( 'message' => $e->getMessage() )
	);
}

$output->show();
