<?php
namespace Wikimedia\TorProxy;

class SearchActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {
		$proxyConfig = $config->getProxyConfig();
		$wikiConfig = $config->getWikiConfig();
		$OAuthConfig = $config->getOAuthConfig();

		#$token = isset( $request['token'] ) ? $request['token'] : 'none';
		#if ( !$user->validateToken( $token, 'Search' ) ) {
		#	throw new \Exception( 'Invalid search csrf token' );
		#}

		$searchterm = isset( $request['search'] ) ? $request['search'] : false;
		if ( $searchterm === false ) {
			throw new Exception( "need search term to search.." );
		}

		// Proxy Search, padded
		$wiki = new Wiki( $wikiConfig, $OAuthConfig, $proxyConfig );
		$results = $wiki->search( $user, $searchterm );

		$res = array();
		if ( isset( $results->query->search ) ) {
			foreach( $results->query->search as $t ) {
				$res[] = array(
					'titleurl' => urlencode($t->title),
					'title' => $t->title,
					'snippet' => strip_tags( $t->snippet )
				);
			}
		}
		$items = array(
			'results' => $res,
			'term' => $searchterm,
			'termurl' => urlencode( $searchterm )
		);
		if ( !$res ) {
			$items['empty'] = true;
		}

		// Show results
		$html = $output->getTemplateHtml(
			'searchresult',
			$items
		);

		$output->addTemplate( 'content', Array( 'html' => $html ) );
	}

}
