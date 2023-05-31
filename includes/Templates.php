<?php

namespace Wikimedia\TorProxy;
use LightnCandy;
use LightnCandy\LightnCandy as LnC;

class Templates
{

    public static function getTemplate( $name, $templateConfig )
    {
        $name = strtolower(preg_replace('![^\w]!', '', $name));
        return file_get_contents($templateConfig['templateDir'] . $name . '.tmpl');
    }

    public static function renderTemplate( $template, $data )
    {
        $phpStr = LnC::compile($template);
        $renderer = LnC::prepare($phpStr);
        return $renderer($data);
    }

}
