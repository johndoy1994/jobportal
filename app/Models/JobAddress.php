<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAddress extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function city() {
        return $this->hasOne('\App\Models\City', 'id', 'city_id');
    }

    public function state() {
        if($this->city) {
            if($this->city->State) {
                return $this->city->State;
            }
        }

        return null;
    }

    public function country() {
        if($this->state()) {
            if($this->state()->Country) {
                return $this->state()->Country;
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

    public function getStreetName() {
        return ucwords($this->street);
    }

    public function getPostalCode() {
        return ucwords($this->postal_code);
    }

    public function getStreet() {
        return ucwords($this->street);
    }

    public function getFullAddress() {
    	$city = $this->city;
    	if($city && $this->State() && $this->State()->Country) {
            $street = trim($this->getStreet());
            if(strlen($street) > 0) {
                $street = $street.", ";
            } else {
                $street = "";
            }
    		return $street.$city->getName().', '.$city->State->getName().', '.$city->State->Country->getName().' - '.$this->postal_code;
    	} else {
    		return $this->street.' - '.$this->postal_code;
    	}
    }

}
