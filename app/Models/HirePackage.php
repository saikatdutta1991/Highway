<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Utill;
use Carbon\Carbon;

class HirePackage extends Model
{

    protected $table = 'driver_hiring_packages';
    protected $appends = ["night_charge_start", "night_charge_end"];

    public static function table()
    {
        return "driver_hiring_packages";
    }


    public function getNightChargeStartAttribute()
    {
        return $this->night_hours == "" ? "" : Utill::getHoursList()[ explode("-", $this->night_hours)[0] ];
    }

    public function getNightChargeEndAttribute()
    {
        return $this->night_hours == "" ? "" : Utill::getHoursList()[ explode("-", $this->night_hours)[1] ];
    }


    /** calculate package cost */
    public function calculateFare($sdatetime, $edatetime, $timezone = "Asia\Kolkata")
    {
        $sdatetime = Carbon::parse($sdatetime);
        $edatetime = Carbon::parse($edatetime);
        $minutes = $sdatetime->diffInMinutes($edatetime);

        $ridefare = $this->charge;
        $minutes = $minutes - ($this->hours * 60) - $this->grace_time; // removing package hours and grace minutes from total minutes
        $minutes = $minutes < 0 ? 0 : $minutes; // if minutes less 0 then make 0 

        /** calculte per hour charge */
        $hours = ceil($minutes / 60); // if 1.5 hours then make 2 hours
        $ridefare += $hours * $this->per_hour_charge;
        $ridefare = Utill::formatAmountDecimalTwoWithoutRound($ridefare);

        /** calculate night charge */
        $nightCharge = 0;
        if($this->shouldNightChargeApply($this->night_hours, $sdatetime) || $this->shouldNightChargeApply($this->night_hours, $edatetime)) {
            $nightCharge = $this->night_charge;
        }

        $nightCharge = Utill::formatAmountDecimalTwoWithoutRound($nightCharge);
        
        return [$ridefare, $nightCharge];
        
    }


    /** should night charge apply */
    protected function shouldNightChargeApply($hours, $datetime)
    {
        
        list($starthour, $stophour) = explode("-", $hours);
        $givenhour = $datetime->format("H");
        $givenminute = $datetime->format("i");


        if($givenhour == $starthour) {
            return true;
        } else if($givenhour == $stophour && $givenminute >= 0) {
            return false;
        } 



        $isNightCharge = false;
        $counter = $starthour;
        while(true) {

            if($counter == $givenhour) {
                $isNightCharge = true;
                break;
            }

            if($counter == $stophour) {
                $isNightCharge = false;
                break;
            }

            $counter += 1;
            $counter = $counter == 24 ? 0 : $counter;

        }
       

        return $isNightCharge;

    }









}