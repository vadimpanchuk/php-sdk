<?php

class MailfireEmail extends MailfireDi
{
    const CHECK_EMAIL_RESOURCE = 'email/check';
    const VALIDATE_EMAIL_RESOURCE = 'email/check/send';

    /**
     * @param string $email
     * @param bool $sanitize
     * @return bool
     */
    public function check($email, $sanitize = true)
    {
        return $this->request->create(self::CHECK_EMAIL_RESOURCE, array(
            'email' => $email,
            'sanitize' => $sanitize));
    }

    /**
     * @param int $projectId
     * @param string $email
     * @param int $typeId
     * @return bool
     */
    public function validate($projectId, $email, $typeId)
    {
        return $this->request->create(self::VALIDATE_EMAIL_RESOURCE, array(
            'project' => $projectId,
            'email' => $email,
            'type' => $typeId
        ));
    }

    /**
     * @param int $mailId
     * @return bool
     * @throws Exception
     */
    public function trackClickByMailId($mailId)
    {
        if (filter_var($mailId, FILTER_VALIDATE_INT) === false ) {
            $this->errorHandler->handle(new Exception('$mailId is not an integer'));
            return false;
        }
        return $this->request->create('trackemail/click/' . $mailId);
    }
}
