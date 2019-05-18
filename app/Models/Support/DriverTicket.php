<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;

class UserTicket extends Model
{

    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const RESOLVED = 'resolved';

    protected $table = 'driver_support_tickets';

    public static function tablename()
    {
        return 'coupon_codes';
    }

}
