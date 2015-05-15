<?php

namespace Wikimedia\TorProxy;

abstract class ActionHandler {

	public function checkAccess( User $user ) {
		if ( $this->requireLoggedIn() && !$user->authenticated() ) {
			throw new \Exception();
		}
		return true;
	}

	final public function process( User $user, array $request, Output &$output ) {
		$this->validateRequest( $request );
		$this->writeChrome( $user, $output );
		$this->exec( $user, $request, $output );
	}

	abstract public function exec( User $user, array $request, Output &$output );

	protected function validateRequest( $request ) {
		// noop
	}

	protected function writeChrome( User $user, Output &$output ) {
		// write logged-in header
		if ( $user->authenticated() ) {
			$output->addTemplate(
				'navbarloggedin',
				Array(
					'token' => $user->getToken( 'logout' ),
					'username' => $user->getFromSession( 'username' ),
				)
			);
		} else {
			$output->addTemplate( 'navbarloggedout', Array( 'token' => $user->getToken( 'login' ) ) );
		}
	}

	protected function requireLoggedIn() {
		return true;
	}

}
