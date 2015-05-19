<?php
namespace Wikimedia\TorProxy;

class LoginActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output ) {

		global $TorProxyOAuthConfig; // TODO: fix

		$stage = isset( $request['stage'] ) ? $request['stage'] : 'none';
		$token = isset( $request['token'] ) ? $request['token'] : 'none';

		if ( !$user->validateToken( $token, 'login' )
			&& !$user->validateToken( $token, 'oauth' )
		) {
			throw new \Exception( 'Invalid login token' );
		}

		$config = new \MWOAuthClientConfig(
			'http://localhost/w/index.php?title=Special:OAuth', // url to use
			false, // do we use SSL? (we should probably detect that from the url)
			false // do we validate the SSL certificate? Always use 'true' in production.
		);
		$config->canonicalServerUrl = 'http://localhost';
		// Optional clean url here (i.e., to work with mobile), otherwise the
		// base url just has /authorize& added
		$config->redirURL = 'http://localhost/wiki/Special:OAuth/authorize?';
		$cmrToken = new \OAuthToken(
			$TorProxyOAuthConfig['key'],
			$TorProxyOAuthConfig['secret']
		);
		$client = new \MWOAuthClient( $config, $cmrToken );
		$client->setCallback(
			'http://localhost/torproxy/index.php?action=login&stage=finish&token='
				. $user->getToken( 'oauth' )
		);

		if ( $stage === 'init' ) {
			$url = $this->LoginInit( $user, $request, $client );
			$output->setRedirect( $url );
		} elseif( $stage === 'finish' ) {
			$this->LoginFinish( $user, $request, $client );
			$output->setRedirect( 'http://localhost/torproxy/index.php?action=home' ); //TODO: baseurl
		} else {
			throw new \Exception( 'Invalid login stage' );
		}


	}

	private function LoginInit( User $user, $request, $client ) {
		list( $redir, $requestToken ) = $client->initiate();
		$user->storeInSession(
			'oauthreqtoken',
			"{$requestToken->key}:{$requestToken->secret}"
		);
		return $redir;
	}


	private function LoginFinish( User $user, $request, $client ) {
		$verifyCode = $request['oauth_verifier'];
		$recKey = $request['oauth_token'];
		list( $requestKey, $requestSecret ) =
			explode( ':', $user->getFromSession( 'oauthreqtoken' ) );
		$requestToken = new \OAuthToken( $requestKey, $requestSecret );
		$user->deleteFromSession( 'oauthreqtoken' );

		//check for csrf
		if ( $requestKey !== $recKey ) {
			throw new \Exception( "CSRF detected" );
		}

		$accessToken = $client->complete( $requestToken,  $verifyCode );

		session_regenerate_id();
		$identity = $client->identify( $accessToken );
		$user->storeInSession( 'oauthtoken', "{$accessToken->key}:{$accessToken->secret}" );
		$user->storeInSession( 'username', $identity->username );
		$user->storeInSession( 'wikiid', $identity->sub );
		$user->setWikiId( $identity->sub );

	}

	protected function requireLoggedIn() {
		return false;
	}


}
