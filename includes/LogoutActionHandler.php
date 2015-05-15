<?php
namespace Wikimedia\TorProxy;

class LogoutActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output ) {

		if ( !$user->validateToken( $request['token'], 'logout' ) ) {
			throw new \Exception( "Logout csrf" );
		}

		session_destroy();
		session_regenerate_id(true);
		session_start();

		$output->setRedirect( 'http://localhost/torproxy/' ); // TODO: Config base url
	}

	protected function requireLoggedIn() {
		return true;
	}


}
