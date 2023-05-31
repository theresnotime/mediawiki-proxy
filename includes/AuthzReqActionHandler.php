<?php
namespace Wikimedia\TorProxy;

class AuthzReqActionHandler extends ActionHandler
{


    public function exec( User $user, array $request, Output &$output, Settings $config )
    {
        $proxyConfig = $config->getProxyConfig();
        $wikiConfig = $config->getWikiConfig();
        $OAuthConfig = $config->getOAuthConfig();

        $reason = isset($request['authzReq_reason']) ? $request['authzReq_reason'] : null;
        $reason = preg_replace('![\[\]\{\}<>&\\\\]!i', '', $reason);

        $token = isset($request['token']) ? $request['token'] : 'none';

        if (!$user->validateToken($token, 'AuthzReqForm') ) {
            throw new \Exception('Invalid login token');
        }

        if ($user->authorized() !== 'unauthorized' ) {
            throw new \Exception('User is not unauthorized');
        }

        $wiki = new Wiki($wikiConfig, $OAuthConfig, $proxyConfig);
        $wikitext = $output->getTemplateHtml(
            'wt_notice',
            Array(
            'username' => $user->getFromSession('username'),
            'reason' => $reason
            )
        );
        Settings::getInstance()->getLogger()->log("Posting to wiki: '$wikitext'");
        $wiki->editNotificationPage($user, $wikitext);

        $user->initAuthz();

        $output->setRedirect($proxyConfig['base_url'] . 'index.php?action=home');
    }

}
