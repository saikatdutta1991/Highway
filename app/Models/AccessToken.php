<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{

    protected $table = 'access_tokens';

    public function getTableName()
    {
        return $this->table;
    }

}