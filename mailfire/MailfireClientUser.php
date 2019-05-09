<?php

class MailfireClientUser extends MailfireDi
{

    public function getUserFieldsByUser($email, $projectId, $clientUserId)
    {
        $data = [
            'email' => $email,
            'project_id' => (int)$projectId,
            'client_user_id' => (int)$clientUserId,
        ];
        return $this->request->create('clientuser/create', $data);
    }

}
