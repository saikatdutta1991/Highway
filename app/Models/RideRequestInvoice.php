<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequestInvoice extends Model
{

    protected $table = 'ride_request_invoices';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * generate invoice reference id
     */
    public function generateInvoiceReference()
    {
        $date = date('Y_m_d');
        return $this->invoice_reference = strtoupper(uniqid('#invoice_'.$date.'_').rand(1000,9999));
    }



}