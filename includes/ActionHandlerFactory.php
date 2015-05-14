<?php

namespace Wikimedia\TorProxy;


class ActionHandlerFactory {

	static private $handlers = array(
		'anon' => 'Wikimedia\TorProxy\AnonActionHandler',
		'login' => 'LoginActionHandler',
		'home' => 'HomeActionHandler',
		'search' => 'SearchActionHandler',
		'article' => 'ArticleActionHandler',
	);

	public static function getHandler( $action ) {
		$handlerClass = self::$handlers[$action];
		return new $handlerClass();
	}

}
