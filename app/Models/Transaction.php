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



    /**
     * parent transaction relation
     */
    public function childTransactions()
    {
        return $this->hasMany('App\Models\Transaction', 'trans_parent_id');
    }



    /**
     * parent transaciton relation
     */
    public function parentTransaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'trans_parent_id');
    }



}