<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('alpha_single_name', function($attribute, $value)
        {
            //return preg_match('/^[\pL\s]+$/u', $value);
            return preg_match("/^[A-Za-z0-9' ]+$/u", $value);
        });

        Validator::extend('alpha_numeric_spaces', function($attribute, $value)
        {
            //return preg_match('/^[\pL\s]+$/u', $value);
            return preg_match('/^[A-Za-z0-9 ]+$/u', $value);
        });

        Validator::extend('address', function($attribute, $value)
        {
            //return preg_match('/^[\pL\s]+$/u', $value);
            return preg_match('/^[A-Za-z0-9 \-,.\#]+$/u', $value);
        });

        Validator::extend('postalcode', function($attribute, $value) {
           return preg_match('/^[A-Za-z0-9 ]+$/u', $value); 
        });

        Validator::extend('findId', function($attribute, $id, $parameters) {
            $tableName = isset($parameters[0]) ? $parameters[0] : $attribute;
            if(isset($parameters[1])) {
                if($parameters[1] == $id) {
                    return true;
                }
            }
            $q=DB::table($tableName)->where('id', $id);
            for($i=2;$i<count($parameters);$i++) {
                if($i%2 == 0) {
                    $q->where($parameters[$i],$parameters[$i+1]);
                } 
            }
            if($q->count()>0) {
                return true;
            }
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
