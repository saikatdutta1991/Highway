<?php

namespace App\Repositories;


/**
* Utill class
* used to write helper common utility methods
* @author  saikat
*/

use App\Models\Setting;


class Utill
{

    /**
     * google static map base url
     */
    const GOOGLE_STATIC_MAP_BASE_URL = 'https://maps.googleapis.com/maps/api/staticmap';



	/**
	* initialize the the dependencies
	*/
	public function __construct(Setting $setting)
	{
        $this->setting = $setting;
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
     * generate photo name
     */
    public function generatePhotoName($prefix, $ext)
    {
        $ext = '.'.str_replace('.', '', $ext);
        return $prefix.'_'.md5(uniqid(mt_rand(), true)).'_'.time().$ext;
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




    /**
     * returns latitude, longitude validation regex
     */
    public function regexLatLongValidate()
    {
        return ['/^(\+|-)?[0-9]+[.][0-9]+$/', '/^(\+|-)?[0-9]+[.][0-9]+$/'];
    }



    /**
     * generate url for google static map url 
     * where two points will be connected with path
     */
    public function getGoogleStaicMapImageConnectedPointsUrl($points = [[12.891551,77.632795],[12.955839,77.714680]], $size = [200,200], $markerColors = ['green', 'red'], $markerLabels = ['S', 'D'], $scale = 2, $mapType = 'roadmap')
    {
        list($width, $height) = $size;
        list($marker1Color, $marker2Color) = $markerColors;
        list($point1, $point2) = $points;
        list($label1, $label2) = $markerLabels;
        $apiKey = $this->setting->get('google_maps_api_key');
        $url = self::GOOGLE_STATIC_MAP_BASE_URL;
        $queryString = http_build_query([
            'key' => $apiKey,
            'size' => $width.'x'.$height,
            'maptype' => $mapType,
            'markers' => 'label:'.$label1.'|color:'.$marker1Color.'|size:mid|'.implode(',', $point1).'&markers=label:'.$label2.'|color:'.$marker2Color.'|size:mid|'.implode(',', $point2),
            'path' => 'color:0x0000ff|weight:5|'.implode(',', $point1).'|'.implode(',', $point2),
        ]);

        return $url.'?'.urldecode($queryString);

    }




    /**
     * get base64 image
     * need to pass path or url
     * if url path then pass $isFIle
     */
    public function getBase64Image($path, $isFile = true)
    {
        try {

            if($isFile) { 
                $type = 'image/'.pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
            } else {
                $data = file_get_contents($path);
                $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
                $type = $fileInfo->buffer($data);
            }
            
            $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
            return $base64;

        } catch(\Exception $e) {
            \Log::info('PHOTO_BASE64');
            \Log::info($e->getMessage());
            return '';
        }
    }




    /**
     * validate and returns timezone
     */
    public function getTimezone($timezone)
    {
        return !in_array($timezone, config('timezones') ?: []) ? $this->setting->get('default_user_driver_timezone') : $timezone; 
    }


}