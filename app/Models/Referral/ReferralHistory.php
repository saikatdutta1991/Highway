<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Model;

class ReferralHistory extends Model
{

    protected $table = 'referral_histories';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * relation with reffered users
     */
    public function referredUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'referred_id');
    }



}