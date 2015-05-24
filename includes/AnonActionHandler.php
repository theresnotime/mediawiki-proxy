<?php
namespace Wikimedia\TorProxy;

class AnonActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output, Settings $config ) {

		$wiki = $config->getWikiConfig();

		$html = $output->getTemplateHtml(
			'heading',
			array( 'text'=>'Use Tor to edit Wikipedia.' )
		);

		$html .= $output->getTemplateHtml(
			'paragraph',
			array(
				'header'=>false, 'body' => "Tor exit node IP addresses are blocked by Wikipedia due to excessive spam from Tor. However, there are legitimate reasons to use Tor for editing Wikipedia. This proxy allows a group of trusted users to edit English Wikipedia from Tor." )
		);

		$html .= $output->getTemplateHtml(
			'paragraph',
			Array( 'header'=>'How it works', 'body' => "You will first need to login to Wikipedia using your global identity, and authorize the TorProxy app to edit as you, using OAuth. After you request authorization to use this proxy, the proxy admin will need to approve your request. Your request will be posted publicly (via this proxy) to {$wiki['notification_page']}. Once you are authorized, you can use this proxy to edit an article at any time." )
		);

		$html .= $output->getTemplateHtml(
			'paragraph',
			Array( 'header'=>'Why privacy?', 'body' => "" )
		);

		$output->addTemplate( 'content', Array( 'html' => $html ) );

	}

	protected function requireLoggedIn() {
		return false;
	}


}
