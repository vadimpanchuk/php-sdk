<?php

/**
 * Class Mailfire
 *
 * @property MailfireErrorHandler $errorHandler
 * @property MailfireRequest $request
 * @property MailfirePush $push
 * @property MailfireEmail $email
 * @property MailfireUser $user
 * @property MailfireUnsub $unsub
 * @property MailfireUnsubTypes $unsubTypes
 * @property MailfireWebPush $webpush
 * @property MailfireGoal $goal
 * @property MailfireAppPush $appPush
 * @property MailfireContent $content
 * @property MailfireRfm $rfm
 * @property MailfireProductEvent $event
 * @property MailfireClientUser $clientUser
 */
class Mailfire
{
    public $clientId;
    public $clientKey;

    public function __construct($clientId, $clientKey)
    {
        if (empty($clientId)) {
            throw new InvalidArgumentException('clientId cannot be empty');
        }
        if (empty($clientKey)) {
            throw new InvalidArgumentException('clientKey cannot be empty');
        }
        if (!is_string($clientKey)) {
            throw new InvalidArgumentException('clientKey must be a string');
        }

        $this->clientId = $clientId;
        $this->clientKey = $clientKey;

        $this->errorHandler = new MailfireErrorHandler();
        $this->request = new MailfireRequest($this);
        $this->push = new MailfirePush($this);
        $this->email = new MailfireEmail($this);
        $this->user = new MailfireUser($this);
        $this->unsub = new MailfireUnsub($this);
        $this->unsubTypes = new MailfireUnsubTypes($this);
        $this->webpush = new MailfireWebPush($this);
        $this->goal = new MailfireGoal($this);
        $this->appPush = new MailfireAppPush($this);
        $this->content = new MailfireContent($this);
        $this->rfm = new MailfireRfm($this);
        $this->event = new MailfireProductEvent($this);
        $this->clientUser = new MailfireClientUser($this);
        $this->buying = new MailfireBuying($this);
    }

}
