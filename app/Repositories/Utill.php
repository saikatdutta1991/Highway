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




}