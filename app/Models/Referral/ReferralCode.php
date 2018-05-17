<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{

    protected $table = 'referral_codes';

    public function getTableName()
    {
        return $this->table;
    }

}