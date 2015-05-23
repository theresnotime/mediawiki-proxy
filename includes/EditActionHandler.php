<?php
namespace Wikimedia\TorProxy;

class EditActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {
		$proxyConfig = $config->getProxyConfig();
		$wikiConfig = $config->getWikiConfig();
		$OAuthConfig = $config->getOAuthConfig();

		$token = isset( $request['token'] ) ? $request['token'] : 'none';
		$stage = isset( $request['stage'] ) ? $request['stage'] : 'init';
		if ( $stage === 'save' && !$user->validateToken( $token, 'Search' ) ) {
			throw new \Exception( 'Invalid search csrf token' );
		}

		$html = '';

		if ( $stage === 'init' ) {

		} elseif ( $stage === 'save' ) {

		} else {
			throw new Exception( 'invalid stage' );
		}

		$output->addTemplate( 'content', Array( 'html' => $html ) );
	}

}
