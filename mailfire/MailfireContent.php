<?php

class MailfireContent extends MailfireDi
{
    /**
     * @param $project
     * @param $uid
     * @param null $entityId
     * @return bool
     */
    public function trackShow($project, $uid, $entityId = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new Exception('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new Exception('Uid must be set.'));
            return false;
        }
        if (!$entityId) {
            $this->errorHandler->handle(new Exception('Entity id must be set.'));
            return false;
        }

        return $this->request->sendToApi3('pushapp/content/show', 'POST', [
            'project' => $project, 'uid' => $uid, 'entity' => $entityId
        ]);
    }
}