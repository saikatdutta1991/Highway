<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLogin extends Model
{

    protected $table = 'social_logins';

    public function getTableName()
    {
        return $this->table;
    }




    /**
     * get user by social login id
     */
    public function getUserBySocialLoginId($socialId, $provider)
    {
        $uModel = app('App\Models\User');
        return $uModel->join(
            $this->getTableName(), 
            $this->getTableName().'.entity_id', 
            '=', 
            $uModel->getTableName().'.id'
        )
        ->where('entity_type', strtoupper('user'))
        ->where('social_login_id', $socialId)
        ->where('social_login_provider', strtoupper($provider))
        ->select($uModel->getTableName().'.*')
        ->first();
    }


    /**
     * get driver by social login id
     */
    public function getDriverBySocialLoginId($socialId, $provider)
    {
        $uModel = app('App\Models\Driver');
        return $uModel->join(
            $this->getTableName(), 
            $this->getTableName().'.entity_id', 
            '=', 
            $uModel->getTableName().'.id'
        )
        ->where('entity_type', strtoupper('driver'))
        ->where('social_login_id', $socialId)
        ->where('social_login_provider', strtoupper($provider))
        ->select($uModel->getTableName().'.*')
        ->first();
    }




}