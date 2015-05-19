<?php
namespace Wikimedia\TorProxy;

class HomeActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {
		Settings::getInstance()->getLogger()->log( 'Checking authz stage of user: (' . $user->getWikiId() . ') \'' . $user->authorized() . '\'' );
		switch( $user->authorized() ) {
			case 'unauthorized':
				// Show directions and authz form
				$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'Get started!' ) );
				$html .= $output->getTemplateHtml( 'paragraph', Array( 'header'=>false, 'body' => "By using this proxy, you agree to follow the guidelines for editing Wikipedia. We may revoke your use of this proxy at any time." ) );
				$html .= $output->getTemplateHtml( 'authzform', Array( 'token' => $user->getToken('AuthzReqForm') ) );
				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			case 'waiting':
				// Show directions and queue stats
				$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'We\'re approving your request... check back soon.' ) );

				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			case 'authorized':
				// Show welcome and search
				$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'You\'re all set to edit!' ) );

				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			default:
				throw new \Exception( 'invalide authz stage' );
		}


	}

}
