<?php

class MailfireRfm extends MailfireDi
{
    /**
     * @param $data array
     * @return bool
     */
    public function set($data)
    {
        return $this->request->sendToApi2('rfm/set', 'POST', $data);
    }
}