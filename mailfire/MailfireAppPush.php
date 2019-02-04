<?php

class MailfireAppPush extends MailfireDi
{
    /**
     * @param int $project Project id can be found at https://admin.mailfire.io/account/projects
     * @param string $token FCM token
     * @param int $platform Android or IOS
     * @param null $uid Your userID
     * @param null $mfEmailUserId
     * @return bool
     * @throws Exception
     */
    public function createPushUser($project, $token, $platform, $uid = null, $mfEmailUserId = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$token) {
            $this->errorHandler->handle(new Exception('Token must be set.'));
            return false;
        }

        if (!$platform) {
            $this->errorHandler->handle(new Exception('Platform must be set.'));
            return false;
        }

        $requestData = [
            'project' => $project,
            'token' => $token,
            'platform' => $platform
        ];

        if ($uid) {
            $requestData['uid'] = $uid;
        }

        if ($mfEmailUserId) {
            $requestData['user_id'] = $mfEmailUserId;
        }

        return $this->request->create('pushapp/user/create', $requestData);
    }

    /**
     * @param $project
     * @param $token
     * @param $uid
     * @return bool
     * @throws Exception
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

        $requestData = [
            'project' => $project,
            'token' => $token,
            'uid' => $uid
        ];

        return $this->request->create('pushapp/user/token/refresh', $requestData);
    }

    /**
     * @param $pushId
     * @return bool
     * @throws Exception
     */
    public function trackShow($pushId)
    {
        if (!$pushId) {
            $this->errorHandler->handle(new Exception('Push id must be set.'));
            return false;
        }

        return $this->request->create('pushapp/track/show', ['push_id' => $pushId]);
    }

    /**
     * @param $pushId
     * @return bool
     * @throws Exception
     */
    public function trackClick($pushId)
    {
        if (!$pushId) {
            $this->errorHandler->handle(new Exception('Push id must be set.'));
            return false;
        }

        return $this->request->create('pushapp/track/click', ['push_id' => $pushId]);
    }

    /**
     * @param $project
     * @param $uid
     * @return bool
     * @throws Exception
     */
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

        $requestData = [
            'project' => $project,
            'uid' => $uid
        ];

        return $this->request->create('pushapp/user/online', $requestData);
    }

    /**
     * @param $project
     * @param $uid
     * @param $message
     * @return bool
     * @throws Exception
     */
    public function send($project, $uid, $message)
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
            'project' => $project, 'uid' => $uid, 'message' => $message
        ]);
    }
}