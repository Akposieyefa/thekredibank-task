<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogHelper
{
    /**
     * @var ActivityLog
     */
    private ActivityLog $logModel;

    /**
     * @param ActivityLog $logModel
     */
    public function __construct(ActivityLog $logModel)
    {
        $this->logModel = $logModel;
    }

    /**
     * create system activity log
     * @param $user_id
     * @param $action
     * @param $message
     * @param $status
     * @return mixed
     */
    public function log($user_id, $action, $message, $status): mixed
    {
        return $this->logModel->create([
            'user_id' => $user_id,
            'action' => $action,
            'message' => $message,
            'status' => $status,
        ]);
    }

    /**
     * get activity log for single user
     * @param $userId
     * @return mixed
     */
    public function getUserData($userId): mixed
    {
        return $this->logModel->whereUserId($userId)->orderBy('created_at', 'DESC')->paginate(10);
    }

    /**
     * get all system activity logs
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->logModel->orderBy('created_at', 'DESC')->paginate(10);
    }

}

