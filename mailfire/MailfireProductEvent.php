<?php

class MailfireProductEvent extends MailfireDi
{

    const CREATE_EVENT_RESOURCE = 'user-event/create';

    /**
     * @param array $data
     * @return bool
     */
    public function send(array $data = [])
    {

        // simple validation 
        if (!is_array($data[0])) {
            return false;
        }
        if (empty($data[0]['project_id']) || empty($data[0]['event_id']) || empty($data[0]['receiver_id'])) {
            return false;
        }
        
        return $this->request->create(self::CREATE_EVENT_RESOURCE, $data);

    }

}
