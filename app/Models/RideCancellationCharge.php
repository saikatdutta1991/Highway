<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideCancellationCharge extends Model
{
    const NOT_APPLIED = 'NOT_APPLIED';
    const APPLIED = 'APPLIED';
    protected $table = 'user_ride_cancellation_charges';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * calculate user cancellation charge
     */
    public function calculateCancellationCharge($userid)
    {
        $charge = $this->where('user_id', $userid)->where('status', self::NOT_APPLIED)->sum('cancellation_charge');
        return app('UtillRepo')->formatAmountDecimalTwoWithoutRound($charge);
    }

    /**
     * make all cancellation charges applied of specific user
     */
    public function clearCharges($userid)
    {
        $this->where('user_id', $userid)->where('status', self::NOT_APPLIED)->update(['status' => self::APPLIED]);
    }
    



}