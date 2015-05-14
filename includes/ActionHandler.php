<?php

namespace Wikimedia\TorProxy;

abstract class ActionHandler {

	public function checkAccess( User $user ) {
		if ( $this->requireLoggedIn() && !$user->authenticated() ) {
			throw new \Exception();
		}
		return true;
	}

	abstract public function exec( User $user, array $request, Output &$output );

	protected function requireLoggedIn() {
		return true;
	}

}
