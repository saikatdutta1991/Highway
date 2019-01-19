<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{

    protected $table = 'contents';
    public $timestamps = false;
    
    /**
     * get table name statically
     */
    public static function table()
    {
        return 'contents';
    }

}