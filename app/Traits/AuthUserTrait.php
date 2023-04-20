<?php

namespace App\Traits;

/**
 * Get auth users details
 */
trait AuthUserTrait
{

    /**
     * get auth users details
     * @return mixed
     */
    public function getAuthUser(): mixed
    {
        return (new \App\Models\User)->find(auth()->user()->id);
    }

}

