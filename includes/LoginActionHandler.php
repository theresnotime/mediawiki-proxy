<?php
namespace Wikimedia\TorProxy;

use Exception;
use MWOAuthClient;
use MWOAuthClientConfig;
use OAuthToken;

class LoginActionHandler extends ActionHandler
{


    public function exec( User $user, array $request, Output &$output, Settings $config )
    {
        $OAuthConfig = $config->getOAuthConfig();
        $wikiConfig = $config->getWikiConfig();
        $proxyConfig = $config->getProxyConfig();

        $stage = isset($request['stage']) ? $request['stage'] : 'none';
        $token = isset($request['token']) ? $request['token'] : 'none';

        if (!$user->validateToken($token, 'login')
            && !$user->validateToken($token, 'oauth')
        ) {
            throw new Exception('Invalid login token');
        }

        $clientConfig = new MWOAuthClientConfig(
            $wikiConfig['base_url'] . 'index.php?title=Special:OAuth', // url to use
            false, // do we use SSL? (we should probably detect that from the url)
            false // do we validate the SSL certificate? Always use 'true' in production.
        );
        $clientConfig->canonicalServerUrl = $wikiConfig['canonical_url'];
        $clientConfig->redirURL = $wikiConfig['base_url_clean'] . 'Special:OAuth/authorize?';

        $cmrToken = new OAuthToken(
            $OAuthConfig['key'],
            $OAuthConfig['secret']
        );
        $client = new MWOAuthClient($clientConfig, $cmrToken);
        $client->setCallback(
            $proxyConfig['base_url'] . 'index.php?action=login&stage=finish&token='
            . $user->getToken('oauth')
        );

        if ($stage === 'init' ) {
            $url = $this->LoginInit($user, $request, $client);
            $output->setRedirect($url);
        } elseif($stage === 'finish' ) {
            $this->LoginFinish($user, $request, $client);
            $output->setRedirect($proxyConfig['base_url'] . 'index.php?action=home');
        } else {
            throw new Exception('Invalid login stage');
        }


    }

    private function LoginInit( User $user, $request, $client )
    {
        list( $redir, $requestToken ) = $client->initiate();
        $user->storeInSession(
            'oauthreqtoken',
            "{$requestToken->key}:{$requestToken->secret}"
        );
        return $redir;
    }


    private function LoginFinish( User $user, $request, $client )
    {
        $verifyCode = $request['oauth_verifier'];
        $recKey = $request['oauth_token'];
        list( $requestKey, $requestSecret ) =
        explode(':', $user->getFromSession('oauthreqtoken'));
        $requestToken = new OAuthToken($requestKey, $requestSecret);
        $user->deleteFromSession('oauthreqtoken');

        //check for csrf
        if ($requestKey !== $recKey ) {
            throw new Exception("CSRF detected");
        }

        $accessToken = $client->complete($requestToken,  $verifyCode);

        session_regenerate_id();
        $identity = $client->identify($accessToken);
        $user->storeInSession('oauthtoken', "{$accessToken->key}:{$accessToken->secret}");
        $user->storeInSession('username', $identity->username);
        $user->storeInSession('wikiid', $identity->sub);
        $user->setWikiId($identity->sub);

    }

    protected function requireLoggedIn()
    {
        return false;
    }


}
