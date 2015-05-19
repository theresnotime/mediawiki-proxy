<?php
namespace Wikimedia\TorProxy;

class LogoutActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {

		$proxyConfig = $config->getProxyConfig();

		if ( !$user->validateToken( $request['token'], 'logout' ) ) {
			throw new \Exception( "Logout csrf" );
		}

		session_destroy();
		session_regenerate_id(true);
		session_start();

		$output->setRedirect( $proxyConfig['base_url'] );
	}

	protected function requireLoggedIn() {
		return true;
	}


}
