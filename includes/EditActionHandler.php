<?php
namespace Wikimedia\TorProxy;

class EditActionHandler extends ActionHandler
{


    public function exec( User $user, array $request, Output &$output, Settings $config )
    {
        $proxyConfig = $config->getProxyConfig();
        $wikiConfig = $config->getWikiConfig();
        $OAuthConfig = $config->getOAuthConfig();

        if (!isset($request['title']) || $request['title'] === '' ) {
            throw new \Exception('Edit must include title param');
        }
        $title = $request['title'];
        $token = isset($request['token']) ? $request['token'] : 'none';
        $stage = isset($request['stage']) ? $request['stage'] : 'init';

        Settings::getInstance()->getLogger()->log(__METHOD__ . " edit ($stage): $title");

        if ($stage === 'save' && !$user->validateToken($token, 'Edit') ) {
            throw new \Exception('Invalid edit csrf token');
        }

        $html = '';

        if ($stage === 'init' ) {

            // get wiki text
            $wiki = new Wiki($wikiConfig, $OAuthConfig, $proxyConfig);
            $wikitext = $wiki->getWikitext($user, $title);
            if ($wikitext === false ) {
                // page doesn't exist, error out
                $html .= $output->getTemplateHtml(
                    'msgerror',
                    Array(
                    'code'=>'Page does not exist',
                    'message'=>'TorProxy does not support creating pages at this time.',
                    )
                );
            } else {
                // show form
                $html .= $output->getTemplateHtml(
                    'editform',
                    Array(
                    'title'=>$title,
                    'token'=>$user->getToken('Edit'),
                    'wikitext' => $wikitext
                    )
                );
            }

        } elseif ($stage === 'save' ) {

            $wiki = new Wiki($wikiConfig, $OAuthConfig, $proxyConfig);
            $wikitext = isset($request['wikitext']) ? $request['wikitext'] : false;
            $summary = isset($request['summary']) ? $request['summary'] : '';
            $minor = isset($request['minoredit']) ? $request['minoredit'] : false;
            $watch = isset($request['watchlist']) ? $request['watchlist'] : false;

            if ($wikitext === false ) {
                throw new Exception('Must have wikitext to save edit');
            }

            $result = $wiki->doEdit($user, $title, $wikitext, $summary, $minor, $watch);

            if (isset($result->error) ) {
                $html .= $output->getTemplateHtml(
                    'msgerror',
                    Array(
                    'code' => $result->error->code,
                    'message' => $result->error->info,
                    )
                );
            } else {
                $html .= $output->getTemplateHtml(
                    'msgsuccess',
                    Array(
                    'title' => $title,
                    'titleurl' => $wikiConfig['base_url_clean'] . urlencode($title),
                    )
                );
            }

        } else {
            throw new Exception('invalid stage');
        }

        $output->addTemplate('content', Array( 'html' => $html ));
    }

}
