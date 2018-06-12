<?php

/**
 * Class MailfireDi
 *
 * @property MailfireRfm rfm
 * @property MailfirePush push
 * @property MailfireUser user
 * @property MailfireRequest request
 * @property MailfireErrorHandler errorHandler
 * @property int|string clientId
 * @property int|string clientKey
 *
 */
class MailfireDi
{
    public $di = null;

    public function __construct($di)
    {
        $this->di = $di;
    }

    public function __get($propertyName)
    {
        if (isset($this->di->{$propertyName})) {
            $this->{$propertyName} = &$this->di->{$propertyName};
            return $this->{$propertyName};
        } else {
            return null;
        }
    }
}
