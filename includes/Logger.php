<?php

namespace Wikimedia\TorProxy;

class Logger
{

    private $logfile;

    public function __construct( $config )
    {
        $this->logfile = $config['filename'];
    }

    public function log( $msg )
    {
        file_put_contents($this->logfile, "$msg\n", FILE_APPEND);
    }
}
