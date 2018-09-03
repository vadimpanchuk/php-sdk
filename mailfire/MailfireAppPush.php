<?php

class MailfireAppPush extends MailfireDi
{
    /**
     * @param $project
     * @param $token
     * @param $uid
     * @param null $userId
     * @return bool
     */
    public function createPushUser($project, $token, $uid, $userId = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$token) {
            $this->errorHandler->handle(new Exception('Token must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }

        return $this->request->sendToApi2('pushapp/user/create', 'POST', [
            'project' => $project, 'token' => $token, 'uid' => $uid, 'user_id' => $userId,
        ]);
    }

    /**
     * @param $project
     * @param $token
     * @param $uid
     * @return bool
     */
    public function refreshToken($project, $token, $uid)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$token) {
            $this->errorHandler->handle(new Exception('Token must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }

        return $this->request->sendToApi2('pushapp/user/token/refresh', 'PUT', [
            'project' => $project, 'token' => $token, 'uid' => $uid,
        ]);
    }

    public function trackShow($project, $uid, $pushId, $created = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }
        if (!$pushId) {
            $this->errorHandler->handle(new Exception('Push id must be set.'));
            return false;
        }

        $created = $created ?: time();

        return $this->request->sendToApi2('pushapp/track/show/' . $pushId, 'POST', [
            'project' => $project, 'uid' => $uid, 'push_id' => $pushId, 'created' => $created,
        ]);
    }

    public function trackClick($project, $uid, $pushId, $created = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }
        if (!$pushId) {
            $this->errorHandler->handle(new Exception('Push id must be set.'));
            return false;
        }

        $created = $created ?: time();

        return $this->request->sendToApi2('pushapp/track/click/' . $pushId, 'POST', [
            'project' => $project, 'uid' => $uid, 'push_id' => $pushId, 'created' => $created,
        ]);
    }

    public function updateOnline($project, $uid)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }

        return $this->request->sendToApi2('pushapp/user/online', 'PUT', [
            'project' => $project, 'uid' => $uid,
        ]);
    }

    public function send($project, $uid, $title, $body, $icon, $url)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }

        return $this->request->sendToApi2('pushapp/user/send', 'POST', [
            'project' => $project, 'uid' => $uid, 'title' => $title, 'body' => $body, 'icon' => $icon, 'url' => $url
        ]);
    }
}