<?php

namespace App\Providers;

use App\Observers\PersonalAccessTokenObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //### for Observe PersonalAccessToken
        PersonalAccessToken::observe(PersonalAccessTokenObserver::class);

        //### for Bootstrap styled pagination button bar
        Paginator::useBootstrap();

        //### for Validate Multiple Date Formats
        Validator::extend('date_multi_format', function($attribute, $value, $formats) {
            foreach($formats as $format) {
              $parsed = date_parse_from_format($format, $value);
              if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) return true;
            }
            return false;
        }, 'date format of :attribute is invalid');

        //### for Validate Sequential array
        Validator::extend('array_seq', function($attribute, $value, $formats) {
            return is_array($value) && !($this->isAssoc($value));
        }, 'The :attribute field must be an sequential array');

        //### for Validate Associative array
        Validator::extend('array_assoc', function($attribute, $value, $formats) {
            return $this->isAssoc($value);
        }, 'The :attribute field must be an associative array');

        //### for Validate Column Name
        Validator::extend('column_name', function($attribute, $value, $formats) {
            if (!is_array($value)) return Schema::hasColumn($formats[0], $value);

            if ($this->isAssoc($value)) {
                $names = array_keys($value);
            } else {
                $names = $value;
            }

            foreach ($names as $name) {
                if (!Schema::hasColumn($formats[0], $name)) {
                    return false;
                }
            }

            return true;
        }, 'Invalid column name(s) found');
    }

    private function isAssoc($arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
