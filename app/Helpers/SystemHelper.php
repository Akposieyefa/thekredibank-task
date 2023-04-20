<?php

namespace App\Helpers;

class SystemHelper
{
    /**
     * clean string coming from database
     * @param $string
     * @return mixed
     */
    public function cleanStringHelper($string): mixed
    {
        if (is_null($string)) {
            return "";
        }else {
            return $string;
        }
    }

    /**
     * generate temporary slug from system
     * @param $param
     * @return string
     */
    public function systemSlugHelper($param): string
    {
        $cleanParam = str_replace(' ', '-', $param);
        return $cleanParam."-".strtotime(now());
    }

}
