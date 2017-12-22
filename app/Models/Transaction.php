<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $table = 'transactions';

    //tsanction status
    const SUCCESS = 'SUCCESS';
    const FAILED = 'FAILED';

    public function getTableName()
    {
        return $this->table;
    }

}