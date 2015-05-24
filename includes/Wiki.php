<?php
namespace Wikimedia\TorProxy;

class Wiki {

	private $wikiConfig;

	private $OAuthConfig;

	private $proxyConfig;

	public function __construct( $wikiConfig, $OAuthConfig, $proxyConfig ) {
		$this->wikiConfig = $wikiConfig;
		$this->OAuthConfig = $OAuthConfig;
		$this->proxyConfig = $proxyConfig;
	}

	public function doEdit( $user, $title, $wikitext ) {
		list( $accessKey, $accessSecret ) = explode( ':', $user->getOAuthToken() );
		$accessToken = new \OAuthToken( $accessKey, $accessSecret );

		$csrftoken = $this->getUserWikiEditToken( $user, $accessToken );
		$apiParams = array(
			'action' => 'edit',
			'title' => $title,
			'summary' => 'edit from tor',
			'text' => $wikitext,
			'token' => $csrftoken,
			'format' => 'json',
		);

		$client = $this->getOAuthClient( $user );
		$client->setExtraParams( $apiParams ); // sign these too
		$result = $client->makeOAuthCall(
			$accessToken,
			$this->wikiConfig['base_url'] . 'api.php',
			true,
			$apiParams
		);

		Settings::getInstance()->getLogger()->log( "Edit result: '$result'" );

		return json_decode( $result );

	}

	public function getWikitext( $user, $title ) {
		list( $accessKey, $accessSecret ) = explode( ':', $user->getOAuthToken() );
		$accessToken = new \OAuthToken( $accessKey, $accessSecret );
		$params = array(
			'title' => $title,
			'action' => 'raw',
		);
		$client = $this->getOAuthClient( $user );
		$wikitext = $client->makeOAuthCall(
			$accessToken,
			$this->wikiConfig['base_url'] . 'index.php',
			true,
			$params
		);
		return $wikitext;
	}

	public function editNotificationPage( $user, $wikitext ) {
		list( $accessKey, $accessSecret ) = explode( ':', $user->getOAuthToken() );
		$accessToken = new \OAuthToken( $accessKey, $accessSecret );
		$csrftoken = $this->getUserWikiEditToken( $user, $accessToken );
		$apiParams = array(
			'action' => 'edit',
			'title' => $this->wikiConfig['notification_page'],
			'section' => 'new',
			'summary' => 'Access request for ' . $user->getUsername(),
			'text' => $wikitext,
			'token' => $csrftoken,
			'format' => 'json',
		);

		$client = $this->getOAuthClient( $user );
		$client->setExtraParams( $apiParams ); // sign these too
		$result = $client->makeOAuthCall(
			$accessToken,
			$this->wikiConfig['base_url'] . 'api.php',
			true,
			$apiParams
		);

		Settings::getInstance()->getLogger()->log( "Edit result: '$result'" );
	}


	private function getUserWikiEditToken( $user, $accessToken ) {

		$client = $this->getOAuthClient( $user );

		$json = $client->makeOAuthCall(
			$accessToken,
			$this->wikiConfig['base_url'] . 'api.php?action=query&meta=tokens&type=csrf&format=json'
		);

		$editToken = json_decode( $json )->query->tokens->csrftoken;

		Settings::getInstance()->getLogger()->log( "Got csrf token: '$editToken' from $json" );

		return $editToken;
	}

	public function getOAuthClient( $user ) {
		$clientConfig = new \MWOAuthClientConfig(
			$this->wikiConfig['base_url'] . 'index.php?title=Special:OAuth', // url to use
			false, // do we use SSL? (we should probably detect that from the url)
			false // do we validate the SSL certificate? Always use 'true' in production.
		);
		$clientConfig->canonicalServerUrl = $this->wikiConfig['canonical_url'];
		$clientConfig->redirURL = $this->wikiConfig['base_url_clean'] . 'Special:OAuth/authorize?';

		$cmrToken = new \OAuthToken(
			$this->OAuthConfig['key'],
			$this->OAuthConfig['secret']
		);
		$client = new \MWOAuthClient( $clientConfig, $cmrToken );
		$client->setCallback(
			$this->proxyConfig['base_url'] . 'index.php?action=login&stage=finish&token='
				. $user->getToken( 'oauth' )
		);

		return $client;
	}


}
