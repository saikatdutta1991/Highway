<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{

    protected $table = 'otp_tokens';

    public function getTableName()
    {
        return $this->table;
    }

}