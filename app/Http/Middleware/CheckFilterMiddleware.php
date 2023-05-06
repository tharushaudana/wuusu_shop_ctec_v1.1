<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CheckFilterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $tablename = $role;

        $filter = $request->query('filter');

        if (is_null($filter)) {
            $request->merge(['addfilter' => FilterQuery::make(null)]);
            return $next($request);
        }

        //### Validate for Json
        $request->merge(['filter' => $filter]);
        $request->validate([
            'filter' => ['json']
        ]);

        $json = json_decode($filter, true);

        /**
         * @example  JSON
         * -------------
         *   {
         *       "in":{
         *           "supplier_id":[1,2]
         *       },
         *       "equal":{
         *           "product_id":1
         *       },
         *       "like": {
         *           "title": "%word%"  
         *       },
         *       "date":{
         *           "created_at":["2023-03-26", "2023-03-28"]
         *       },
         *       "datepast": ["validuntil", "moreone"],
         *       "datenotpast": ["validuntil", "moreone"]
         *   }
         * -------------
         */

        //### Validate Filter Params
        $validator = Validator::make($json, [
            'in' => 'array_assoc|column_name:'.$tablename,
            'in.*' => 'array_seq',
            'in.*.*' => 'regex:/^[-a-z0-9\s]+$/i', // - or a-z or 0-9
            //---
            'equal' => 'array_assoc|column_name:'.$tablename,
            'equal.*' => 'nullable|regex:/^[-a-z0-9\s]+$/i', // - or a-z or 0-9
            //---
            'like' => 'array_assoc|column_name:'.$tablename,
            'like.*' => 'regex:/^[%a-z0-9\s]+$/i', // % or a-z or 0-9
            //---
            'date' => 'array_assoc|column_name:'.$tablename,
            'date.*' => 'array|array_seq|min:1|max:2',
            'date.*.0' => 'date_multi_format:Y-m-d,Y-m,Y',
            'date.*.1' => 'nullable|date_multi_format:Y-m-d,Y-m,Y|after:date.*.0',
            //---
            'datepast' => 'array_seq',
            'datepast.*' => 'regex:/^[a-z\s]+$/i|column_name:'.$tablename, // a-z only
            //---
            'datenotpast' => 'array_seq',
            'datenotpast.*' => 'regex:/^[a-z\s]+$/i|column_name:'.$tablename, // a-z only
        ]);

        if ($validator->fails()) {
            return response()->error("Given filter data are invalid!", 400, $validator->errors());
        }

        //### Make filter query
        $request->merge(['addfilter' => FilterQuery::make($json)]);

        return $next($request);
    }
}

class FilterQuery {
    private $json = null;

    function __construct($json) {
        $this->json = $json;
    }
    
    function makeFilterQuery(&$query) {
        $this->make_filter_in($query);
        $this->make_filter_equal($query);
        $this->make_filter_like($query);
        $this->make_filter_date($query);
    }

    private function make_filter_in(&$query) {
        if (!isset($this->json['in'])) return;

        foreach ($this->json['in'] as $column => $values) { // ex: {'supplier_id' : [1,3,10]}
            $query->whereIn($column, $values);
        }
    }

    private function make_filter_equal(&$query) {
        if (!isset($this->json['equal'])) return;

        foreach ($this->json['equal'] as $column => $value) { // ex: {'supplier_id' : 1, 'product_id' : 10}
            $query->where($column, $value);
        }
    }

    private function make_filter_like(&$query) {
        if (!isset($this->json['like'])) return;

        foreach ($this->json['like'] as $column => $value) { // ex: {'title' : '%test%'}
            $query->where($column, 'like', $value);
        }
    }

    private function make_filter_date(&$query) {
        if (!isset($this->json['date'])) return;

        foreach ($this->json['date'] as $column => $values) { // ex: {'created_at' : ['2023-03-26', '2023-04-25']}
            if (count($values) == 1) {
                $this->__make_date_query($query, $column, $values[0], '=');
            } else if (count($values) == 2) {
                $this->__make_date_query($query, $column, $values[0], '>=');
                if (!is_null($values[1])) {
                    $this->__make_date_query($query, $column, $values[1], '<=');
                }
            }
        } 
    }

    private function __make_date_query(&$query, $column, $datestr, $operator) {
        if ($this->date_format_is($datestr, 'Y-m-d')) {
            $query->whereDate($column, $operator, $datestr);
        } else if ($this->date_format_is($datestr, 'Y-m')) {
            $query->whereYear($column, $operator, date("Y", strtotime($datestr)));
            $query->whereMonth($column, $operator, date("m", strtotime($datestr)));
        } else if ($this->date_format_is($datestr, 'Y')) {
            $query->whereYear($column, $operator, $datestr);
        }
    }

    private function date_format_is($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    static function make($json) {
        return array(new FilterQuery($json), 'makeFilterQuery');
    }
}
