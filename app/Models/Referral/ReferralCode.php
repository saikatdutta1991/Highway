<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{

    protected $table = 'referral_codes';


    const ENABLED = 'ENABLED';
    const DISABLED = 'DISABLED';

    public function getTableName()
    {
        return $this->table;
    }

}