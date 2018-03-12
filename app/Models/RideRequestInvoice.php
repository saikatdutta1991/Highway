<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequestInvoice extends Model
{

    protected $table = 'ride_request_invoices';

    protected $hidden = [
        'invoice_map_image_path',
        'invoice_map_image_name'
    ];

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



    /**
     * public function transaction
     */
    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'transaction_table_id');
    }



    /**
     * returns invoice static image url
     */
    public function getStaticMapUrl()
    {
        return url($this->invoice_map_image_path.'/'.$this->invoice_map_image_name);
    }



    /**
     * returns google static image path
     */
    public function invoiceStaticImagePath($isAbsolutePath = true)
    {
        return $isAbsolutePath ? public_path('users/invoices') : 'users/invoices';
        
    }



    /**
     * save invoice google map static image
     */
    public function saveGoogleStaticMap($slat, $slng, $dlat, $dlng)
    {
        $absPath = $this->invoiceStaticImagePath();
        $path = $this->invoiceStaticImagePath(false);
        $name = app('UtillRepo')->generatePhotoName('invoice_', 'png');

        $staticMapImageUrl = app('UtillRepo')->getGoogleStaicMapImageConnectedPointsUrl([
            [$slat, $slng], [$dlat, $dlng]
        ]);

        app('UtillRepo')->downloadFile($staticMapImageUrl, $absPath.'/'.$name);
    
        return [$path, $name];

    }







    /**
     * save invoice gogole map static image
     */
    public function saveInvoiceMapImage($rideRequest, $save = true)
    {
        list($path, $name) = $this->saveGoogleStaticMap(
            $rideRequest->source_latitude, 
            $rideRequest->source_longitude, 
            $rideRequest->destination_latitude, 
            $rideRequest->destination_longitude
        );
        
        $this->invoice_map_image_path = $path;
        $this->invoice_map_image_name = $name;

        if($save) {
            $this->save();
        }

        return [$this->invoice_map_image_path, $this->invoice_map_image_name];

    }



}