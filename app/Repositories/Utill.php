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
     * generate random caps caracters
     */
    public function randomChars($limit = 9)
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $res = "";
        for ($i = 0; $i < $limit; $i++) {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return $res;
    } 



    /**
     * download file and save to given path
     * but process asynchronously with job queue
     */
    public function downloadFileAsync($url, $filename)
    {
        \App\Jobs\DownloadFile::dispatch($url, $filename);
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
     * formate amount to two decimal places without rount value
     */
    public function formatAmountDecimalTwoWithoutRound($amount = "")
    {
        return number_format($amount, 2, '.', '');
    }
    




    /**
     * calculate radious(min latitude, longitude and max latititude, longitude)
     *  for a given distance and center latitude, longitude
     */
    public function getRadiousLatitudeLongitude($latitude, $longitude, $radius, $radiusUnit = 'km')
    {
        $radiusConst = $radiusUnit == "km" ? 111.045 : 69;

        $latitude = number_format($latitude, 7, '.', '');
		$longitude = number_format($longitude, 7, '.', '');

        $minlng      = $longitude - ($radius / abs(cos(deg2rad($latitude)) * $radiusConst));
		$maxlng      = $longitude + ($radius / abs(cos(deg2rad($latitude)) * $radiusConst));
		$minlat      = $latitude - ($radius / $radiusConst);
		$maxlat      = $latitude + ($radius / $radiusConst);
        
        return [
            number_format($minlat, 7, '.', ''),
            number_format($maxlat, 7, '.', ''),
            number_format($minlng, 7, '.', ''),
            number_format($maxlng, 7, '.', ''),
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




    /**
     * returns pagination details 
     * takes route name and lengthAwarepaginator(laravel) object 
     */
    public function createPagination($route, $lengthAwarepaginator)
	{
		$paging = [
            "total" => $lengthAwarepaginator->total(),
            "more_pages" => $lengthAwarepaginator->hasMorePages(),
            "prevous_page_url" => "",
            "next_page_url"    => ""
        ];

        $cur_page = $lengthAwarepaginator->currentPage();

        $last_page = $lengthAwarepaginator->lastPage();

        $next_page_url = ($last_page > $cur_page) ? url($route.'?page='.($cur_page+1)) : "";
        $paging['next_page_url'] = $next_page_url;

        if ($cur_page > 1) {
            $paging['prevous_page_url'] = url($route.'?page='.($cur_page-1));
        } else {
            $paging['prevous_page_url'] = "";
        }

        return $paging;
	}




    /**
     * change datetime string to UTC
     */
    public function timestampStringToUTC($datetimeString, $timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $datetimeString, $timezone)->timezone('UTC');
    }



    /**
     * change datetime string to UTC with input datetime format
     */
    public function strtoutc($datetimeString, $timezone, $format = 'Y-m-d H:i:s')
    {
        return \Carbon\Carbon::createFromFormat($format, $datetimeString, $timezone)->timezone('UTC');
    }


    /**
     * returns utc timestamp range for a particular date when date is in local
     * dateString format is Y-m-d
     */
    public function utcDateRange($dateString, $timezone)
    {
        $timezone = $this->getTimezone($timezone);
        try {

            return [
                $this->timestampStringToUTC($dateString.' 00:00:00', $timezone)->toDateTimeString(),
                $this->timestampStringToUTC($dateString.' 24:00:00', $timezone)->toDateTimeString()
            ];

        } catch(\Exception $e) {
            return false;
        }
        
    }






    /**
     * calculate timestamp string difference in minute
     */
    public function getDiffMinute($fromTime, $toTime)
    {
        $fromTime = strtotime($fromTime);
        $toTime = strtotime($toTime);
    
        return $minute = round(abs($toTime - $fromTime) / 60);
    }





    /**
     * delete and write new file
     */
    public static function writeFile($file, $content)
    {
        /** delete previous file if exists */                
        if(file_exists($file)) {
            unlink($file);
        }

        /** write file */
        file_put_contents($file, $content);

        return true;
    }



    /**
     * returns app hash
     */
    public static function appHashSms($devicetype, $apptype)
    {
        return Setting::get("{$devicetype}_{$apptype}_apphash_sms");
    }


    /**
     * returns localization message string
     */
    public static function transMessage($key, $data = [])
    {
        return __($key, $data);
    }



}