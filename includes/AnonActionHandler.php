<?php
namespace Wikimedia\TorProxy;

class AnonActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output ) {

		$output->addHtml( "<h1>It Works</h1>" );

	}

	protected function requireLoggedIn() {
		return false;
	}

}
