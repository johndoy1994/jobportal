<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicSearch extends Model
{
    protected $fillable = ['term'];

    public function json() {
    	$json = json_decode($this->term, true);
    	if(is_array($json)) {
    		return $json;
    	}
    	return [];
    }

    public function getKeywords() {
    	$json = $this->json();
    	if(isset($json["keywords"])) {
    		return ucwords($json["keywords"]);
    	}
    	return "";
    }

    public function getLocation() {
    	$json = $this->json();
    	if(isset($json["location"])) {
    		return ucwords($json["location"]);
    	}
    	return "";
    }

    public function getParams() {
    	return [
    		"keywords" => $this->getKeywords(),
    		"location" => $this->getLocation()
    	];
    }

    public function getDisplayString() {
    	$keywords = $this->getKeywords();
    	$location = $this->getLocation();

    	$string = "";

    	if(!empty($keywords)) {
    		$string .= $keywords;
    	}	

    	if(!empty($location)) {
    		$string .= " in ".$location;
    	}

    	return $string;
    }

}
