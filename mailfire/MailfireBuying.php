<?php

class MailfireBuying extends MailfireDi
{
    /**
     * @param $email
     * @return bool
     * @throws Exception
     */
    public function getBuyingDecision($email)
    {
        if (!$email) {
            $this->errorHandler->handle(new Exception('Email must be set.'));
            return false;
        }

        $requestData = [
            'email' => $email
        ];

        return $this->request->create('buying/email', $requestData);
    }
}