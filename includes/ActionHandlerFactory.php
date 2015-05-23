<?php

namespace Wikimedia\TorProxy;


class ActionHandlerFactory {

	static private $handlers = array(
		'anon' => 'Wikimedia\TorProxy\AnonActionHandler',
		'login' => 'Wikimedia\TorProxy\LoginActionHandler',
		'logout' => 'Wikimedia\TorProxy\LogoutActionHandler',
		'home' => 'Wikimedia\TorProxy\HomeActionHandler',
		'search' => 'Wikimedia\TorProxy\SearchActionHandler',
		'article' => 'Wikimedia\TorProxy\ArticleActionHandler',
		'authzreq' => 'Wikimedia\TorProxy\AuthzReqActionHandler',
		'search' => 'Wikimedia\TorProxy\SearchActionHandler',
		'edit' => 'Wikimedia\TorProxy\EditActionHandler',
	);

	public static function getHandler( $action ) {
		$handlerClass = self::$handlers[$action];
		return new $handlerClass();
	}

}
