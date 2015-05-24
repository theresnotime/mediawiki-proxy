<?php
namespace Wikimedia\TorProxy;

class HomeActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {
		Settings::getInstance()->getLogger()->log( 'Checking authz stage of user: (' . $user->getWikiId() . ') \'' . $user->authorized() . '\'' );
		$wikiConfig = $config->getWikiConfig();
		switch( $user->authorized() ) {
			case 'unauthorized':
				// Show directions and authz form
				$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'Get started!' ) );
				$html .= $output->getTemplateHtml( 'paragraph', Array( 'header'=>false, 'body' => "By using this proxy, you agree to follow the guidelines for editing Wikipedia. We may revoke your use of this proxy at any time." ) );
				$html .= $output->getTemplateHtml(
					'authzform',
					Array(
						'token' => $user->getToken('AuthzReqForm'),
						'noticeurl' => '<a href="'.$wikiConfig['base_url'].'index.php?title='.rawurlencode($wikiConfig['notification_page']).'">'.htmlspecialchars( $wikiConfig['notification_page'] ).'</a>',
					)
				);
				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			case 'waiting':
				// Show directions and queue stats
				$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'We\'re approving your request... check back soon.' ) );
				$queue = $user->getUserStats();
				$waiting = $queue[User::WAITING];
				$authorized = $queue[User::AUTHORIZED];
				$html .= $output->getTemplateHtml(
					'paragraph',
					Array(
						'header'=>'Users Waiting',
						'body' => $waiting
					)
				);
				$html .= $output->getTemplateHtml(
					'paragraph',
					Array(
						'header'=>'Users Approved',
						'body' => $authorized
					)
				);

				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			case 'authorized':
				// Show welcome and search
				$html = $output->getTemplateHtml(
					'heading',
					Array( 'text'=>'You\'re all set to edit!' )
				);
				$html .= $output->getTemplateHtml(
					'paragraph',
					Array(
						'header'=>'Search',
						'body' => 'Search for a page by title'
					)
				);

				$html .= $output->getTemplateHtml(
					'searchform',
					Array(
						'token' => $user->getToken('Search'),
					)
				);

				$output->addTemplate( 'content', Array( 'html' => $html ) );
				break;
			default:
				throw new \Exception( 'invalide authz stage' );
		}


	}

}
