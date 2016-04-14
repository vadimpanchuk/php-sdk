<?php

class MailfirePush extends MailfireDi
{
    const CATEGORY_SYSTEM = 1;
    const CATEGORY_TRIGGER = 2;

    private $resources = array(
        self::CATEGORY_SYSTEM => 'push/system',
        self::CATEGORY_TRIGGER => 'push/trigger',
    );

    public function send($typeId, $categoryId, $projectId, $email, $user = array(), $data = array(), $meta = array())
    {
        $user['email'] = $email;
        $data['user'] = $user;
        $params = array(
            'type_id' => $typeId,
            'category' => $categoryId,
            'client_id' => $this->clientId,
            'project_id' => $projectId,
            'data' => $data,
            'meta' => $meta
        );

        $resource = $this->resources[$categoryId];

        return $this->request->create($resource, $params);
    }

    public function getCategorySystem()
    {
        return self::CATEGORY_SYSTEM;
    }

    public function getCategoryTrigger()
    {
        return self::CATEGORY_TRIGGER;
    }
}
