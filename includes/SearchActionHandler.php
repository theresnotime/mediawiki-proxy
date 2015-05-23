<?php
namespace Wikimedia\TorProxy;

class SearchActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {
		$proxyConfig = $config->getProxyConfig();
		$wikiConfig = $config->getWikiConfig();
		$OAuthConfig = $config->getOAuthConfig();

		$token = isset( $request['token'] ) ? $request['token'] : 'none';
		if ( !$user->validateToken( $token, 'Search' ) ) {
			throw new \Exception( 'Invalid search csrf token' );
		}

		// Proxy Search, padded

		// Show results
		$html = $output->getTemplateHtml(
					'searchresult',
					Array(
						'results'=>Array( 'a', 'b', 'c' ),
					)
				);

		$output->addTemplate( 'content', Array( 'html' => $html ) );
	}

}
