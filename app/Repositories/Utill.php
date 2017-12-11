<?php

namespace App\Repositories;


/**
* Utill class
* used to write helper common utility methods
* @author  saikat
*/


class Utill
{

	/**
	* initialize the the dependencies
	*/
	public function __construct()
	{
	}



    /**
     * returns image extension from url
     * eg .jpg, .png with dot(.)
     */
    public function getImageExtensionFromUrl($url)
    {
        $size = getimagesize($url); 
        $ext = '';
        switch ($size['mime']) { 
            case "image/gif": 
                $ext = '.gif';
                break; 
            case "image/jpeg": 
                $ext = '.jpg';
                break; 
            case "image/png": 
                $ext = '.png';
                break; 
            case "image/bmp": 
                $ext = '.bmp';
                break; 
        }

        return $ext;
    }





    /**
     * download file and save to given path
     * $filename is absolute path and filename with extension
     */
    public function downloadFile($url, $filename)
    {
        //open file from url
        $file = fopen ($url, 'rb');

        //open file for write
        $newf = fopen ($filename, 'wb');

        if(!$file || !$newf) {
            return false;
        }

        //reading file and write to output file
        while(!feof($file)) {
            fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
        }

        fclose($file);
        fclose($newf);
    }




    /**
	* used to format amount to decimal two place precision
	*
	* @param numeric string|number $amount amount to be formated(3 -> 3.00)
	*
	* @return number formated as two decimal place(3.00)
	*/
	public function formatAmountDecimalTwo($amount = "")
	{
		return number_format(round($amount, 2), 2, '.', '');
    }
    




    /**
     * calculate radious(min latitude, longitude and max latititude, longitude)
     *  for a given distance and center latitude, longitude
     */
    public function getRadiousLatitudeLongitude($latitude, $longitude, $radious, $radiousUnit = 'km')
    {
        $radiousConst = $radiousUnit == "km" ? 111.045 : 69;

        $latitude = number_format($latitude, 7, '.', '');
		$longitude = number_format($longitude, 7, '.', '');

        $minLatitude      = $latitude - ($radious / $radiousConst);
        $maxLatitude      = $latitude + ($radious / $radiousConst);
        
		$minLongitude      = $longitude - ($radious / abs(cos(deg2rad($latitude)) * $radiousConst));
		$maxLongitude      = $longitude + ($radious / abs(cos(deg2rad($latitude)) * $radiousConst));
        
        return [
            number_format($minLatitude, 7, '.', ''),
            number_format($maxLatitude, 7, '.', ''),
            number_format($minLongitude, 7, '.', ''),
            number_format($maxLongitude, 7, '.', ''),
        ];
		
    }



}