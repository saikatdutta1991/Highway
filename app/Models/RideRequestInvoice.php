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
     * save invoice gogole map static image
     */
    public function saveInvoiceMapImage($rideRequest, $save = true)
    {
        $absPath = $this->invoiceStaticImagePath();
        $path = $this->invoiceStaticImagePath(false);
        $name = app('UtillRepo')->generatePhotoName('invoice_', 'png');

        $staticMapImageUrl = app('UtillRepo')->getGoogleStaicMapImageConnectedPointsUrl([
            [$rideRequest->source_latitude, $rideRequest->source_longitude],
            [$rideRequest->destination_latitude, $rideRequest->destination_longitude]
        ]);

        app('UtillRepo')->downloadFile($staticMapImageUrl, $absPath.'/'.$name);
        
        $this->invoice_map_image_path = $path;
        $this->invoice_map_image_name = $name;

        if($save) {
            $this->save();
        }

        return [$this->invoice_map_image_path, $this->invoice_map_image_name];

    }



}