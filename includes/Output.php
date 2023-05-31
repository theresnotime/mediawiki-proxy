<?php

namespace Wikimedia\TorProxy;

class Output
{

    private $html;

    private $redirect;

    private $redirectUrl;

    private $templateConfig;

    public function __construct( array $templateConfig )
    {
        $this->templateConfig = $templateConfig;
    }

    public function addTemplate( $name, $data )
    {
        $template = Templates::getTemplate($name, $this->templateConfig);
        $this->html .= Templates::renderTemplate($template, $data);
    }

    /**
     * When you need to render a template to include in another template
     */
    public function getTemplateHtml( $name, $data )
    {
        $template = Templates::getTemplate($name, $this->templateConfig);
        return Templates::renderTemplate($template, $data);
    }

    public function addHtml( $html )
    {
        $this->html .= $html;

    }

    public function setRedirect( $url )
    {
        $this->redirect = true;
        $this->redirectUrl = $url;
    }

    public function show()
    {
        if ($this->redirect ) {
            $this->outputRedirect($this->redirectUrl);
        } else {
            $this->outputHtml($this->html);
        }
    }

    public function outputRedirect( $url )
    {
        self::outputHeaders();
        header("Location: $url");
    }


    public function outputHtml( $html )
    {
        self::outputHeaders();
        $main = Templates::getTemplate('main', $this->templateConfig);
        echo Templates::renderTemplate($main, Array( 'body' => $html ));
    }

    public static function outputHeaders()
    {
        $csp = 'default-src \'self\'; object-src \'none\'; media-src \'none\'; img-src \'self\';'
        . 'style-src \'self\'; frame-ancestors \'none\'';

        header('Content-Type: text/html; charset=UTF-8');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header("Content-Security-Policy: $csp");
        header("X-Content-Security-Policy: $csp");
        header("X-WebKit-CSP: $csp");
    }

}
