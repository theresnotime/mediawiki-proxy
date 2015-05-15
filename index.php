<?php
namespace Wikimedia\TorProxy;

include_once 'autoload.php';
include 'Settings.php';

session_start();

$logger = new Logger( $TorProxyLogConfig );
$db = new Database( $TorProxyDBConfig, $logger );
$output = new Output( $TorProxyTemplateConfig );
$user = User::getUser( $_SESSION, $db );

$action = isset( $_GET['action'] ) ?: 'anon';
$request = $_REQUEST;

$handler = ActionHandlerFactory::getHandler( $action );
$handler->checkAccess( $user );
$handler->checkAccess( $user );

$handler->process( $user, $request, $output );

$output->show();
