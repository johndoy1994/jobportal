<?php

namespace App\Models;

use App\Models\UserAddress;
use App\Repos\API\PublicRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class UserAddress extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['latitude','longitude'];

    public function getCoordinates() {        
        return [$this->latitude,$this->longitude];
    }

    public function city() {
    	return $this->belongsTo('\App\Models\City');
    }
    
    public function state() {

    	$city = $this->city;

    	if($city && $city->status == 0) {
            if($city->State && $city->State->status == 0) {
                return $city->State;
            }
    	}

    	return null;
    }

    public function country() {

    	$state = $this->state();

    	if($state && $state->status == 0) {
            if($state->Country && $state->Country->status == 0) {
    		  return $state->Country;
            }
    	}

    	return null;

    }

    public function getCityId() {
        if($this->city) {
            return $this->city->id;
        }
        return 0;
    }

    public function getStateId() {
        if($this->state()) {
            return $this->state()->id;
        }
        return 0;
    }

    public function getCountryId() {
        if($this->country()) {
            return $this->country()->id;
        }
        return 0;
    }

    public function getCityName() {
        if($this->city) {
            return $this->city->getName();
        }
        return "";
    }

    public function getStateName() {
        if($this->state()) {
            return $this->state()->getName();
        }
        return "";
    }

    public function getCountryName() {
        if($this->country()) {
            return $this->country()->getName();
        }
        return "";
    }

    public function getFullLine($includeMiles=false) {
        $line = "";

        if(strlen($this->street)>0) {
            $line = $this->street;
        }

        if(strlen($this->getCityName())>0) {
            if(strlen($this->street)>0) {
                $line .= ", ";
            }
            $line .= $this->getCityName();
        }

        if(strlen($this->getStateName())>0) {
            $line .= ", ". $this->getStateName();
        }

        if(strlen($this->getCountryName())>0) {
            $line .= ", ". $this->getCountryName();
        }

        if(strlen($this->postal_code)>0) {
            $line .= " - ".$this->postal_code;
        }

        if($includeMiles && $this->miles>0) {
            $line .= " + ".$this->miles ." miles";
        }

        return $line;
    }

    protected static function boot() {
        parent::boot();
        static::saved(function($userAddress) {
            list($success,$point,$realAddress) = PublicRepo::getGeoLocationPoint($userAddress->getFullLine());
            $updated = UserAddress::where('id',$userAddress->id)->update([
                'latitude'=>$point[0],
                'longitude'=>$point[1]
            ]);
            Log::info("userAddressSaved", [$success, $point,$realAddress, $updated]);
        });
    }

}