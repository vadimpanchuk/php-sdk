<?php

class MailfireErrorHandler
{
    const MODE_ERROR = 1;
    const MODE_EXCEPTION = 2;
    
    private $mode;

    public function __construct($mode = self::MODE_ERROR)
    {
        $this->setErrorMode($mode);
    }

    public function handle(Exception $e)
    {
        if ($this->mode === self::MODE_EXCEPTION) {
            throw $e;
        } else {
            $template = ':time Mailfire: [:type] :message in :file in line :line';
            $logMessage = strtr($template, array(
                ':time' => date('Y-m-d H:i:s'),
                ':type' => $e->getCode(),
                ':message' => $e->getMessage(),
                ':file' => $e->getFile(),
                ':line' => $e->getLine()
            ));
            error_log($logMessage);
        }
    }

    public function setErrorMode($mode)
    {
        $this->mode = $mode;
    }
}

