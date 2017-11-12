<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    const ACTIVATED = 'ACTIVATED';

    protected $table = 'users';

    protected $hidden = ['password'];

    public function getTableName()
    {
        return $this->table;
    }


    public function fullMobileNumber()
    {
        return $this->full_mobile_number = $this->country_code.$this->mobile_number;
    }




    public function reasonNotActivated()
    {
        return 'Admin has deactivated you account';
    }



}