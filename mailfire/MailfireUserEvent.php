<?php

class MailfireUserEvent extends MailfireDi
{

    const CREATE_EVENT_RESOURCE = 'user-event/create';

    /**
     * @param $eventName
     * @param $projectId
     * @param $email
     * @param array $data
     * @param array $relatedUsers
     * @param array $meta
     * @return bool
     */
    public function sendEvent($eventName, $projectId, $email, array $data = [], array $relatedUsers = [], array $meta = [])
    {

        $data = [
            'event_name' => $eventName,
            'project_id' => $projectId,
            'email' => $email,
            'data' => $data,
            'related_users' => $relatedUsers,
            'meta' => $meta,
        ];

        return $this->request->create(self::CREATE_EVENT_RESOURCE, $data);

    }

}
