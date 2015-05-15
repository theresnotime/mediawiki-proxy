<?php
namespace Wikimedia\TorProxy;

class LoginActionHandler extends ActionHandler {


	public function exec( User $user, array $request, Output &$output ) {

		

		/*
		$html = $output->getTemplateHtml( 'heading', Array( 'text'=>'Use Tor to edit Wikipedia.' ) );

		$html .= $output->getTemplateHtml( 'paragraph', Array( 'header'=>false, 'body' => "Intro paragraph" ) );

		$html .= $output->getTemplateHtml( 'paragraph', Array( 'header'=>'How it works', 'body' => "zxcv" ) );

		$html .= $output->getTemplateHtml( 'paragraph', Array( 'header'=>'Why privacy?', 'body' => "zxcv" ) );

		$output->addTemplate( 'content', Array( 'html' => $html ) );
		*/

	}

	protected function requireLoggedIn() {
		return false;
	}


}
