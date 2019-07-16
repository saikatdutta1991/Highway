<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use App\Repositories\Utill;

class FakeLocation extends Model
{
    protected $table = 'fake_locations';

    public static function tablename()
    {
        return 'fake_locations';
    }


    /** get fake drivers */
    public static function fakeDriversWithService($latitude, $longitude, $radius, $radiusUnit = 'km', $servicename)
    {
        /** if fake location service not enabled, return empty array */
        if(Setting::get('fake_location_enabled') !== 'on') {
            return [];
        }
     
        list($minLat, $maxLat, $minLong, $maxLong) = Utill::getRadiousLatitudeLongitude($latitude, $longitude, $radius, $radiusUnit);

		$locations = FakeLocation::where(function ($query) use ($minLat, $maxLat, $minLong, $maxLong) {
            $query->whereBetween(self::tablename().'.latitude', [$minLat, $maxLat])
                ->whereBetween(self::tablename().'.longitude', [$minLong, $maxLong]);  
        })->get();

        $drivers = [];
        foreach($locations as $index => $location) {
            $drivers[] = [
                'id' => -$index,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'vehicle_type' => $servicename
            ];
        }

        return $drivers;
    }


}