<?php

/**
 * Class MailfireDi
 *
 * @property MailfirePush push
 * @property MailfireUser user
 * @property MailfireRequest request
 * @property MailfireErrorHandler errorHandler
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
