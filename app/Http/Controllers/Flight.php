<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Utilities\TripBuilder;

class Flight extends Controller{

    /**
     * @bodyParam departure_location string required an airport or city code identified by IATA. Example: YHZ 
     * @bodyParam destination_location string required an airport or city code identified by IATA. Example: YVR 
     * @bodyParam departure_date date required Must be a valid date. Must be a date after now. Example: 2021-07-01
     * @bodyParam return_date date Must be a valid date. Must be a date after or equal to departure_date. Example: 2021-07-10
     * @bodyParam restrict_airlines string restrict to preferrd airlines. Example: AC,F8 
     * @bodyParam page_size number Must be at least 1, default value 20. Example: 20 
     * @bodyParam page_number number Must be at least 1, default value 1. Example: 1 
     * @bodyParam sort_by enum Must be one of duration, price, or stops, default value: duration. Example: duration 
     * @bodyParam maxmum_stops number Must be between 0 and 5, default value 2. Example: 2 
     * @bodyParam keep_going_forward boolean if true, every further flight should get closer to the destination, otherwise may not, default value true. Example: 1 
     */
    public function search(Request $request){
        $request->headers->set('accept', 'application/json');
        $validated = $request->validate([
            'departure_location' => 'required|regex:/^[A-Z]{3}$/',
            'destination_location' => 'required|regex:/^[A-Z]{3}$/',
            'departure_date' => 'required|date|after:now',
            'return_date' => 'date|after_or_equal:departure_date',
            'restrict_airlines' => 'regex:/^([A-Z0-9]{2},){0,}[A-Z0-9]{2}$/',
            'page_size' => 'integer|min:1',
            'page_number' => 'integer|min:1',
            'sort_by' => 'in:duration,price,stops',
            'maxmum_stops' => 'integer|between:0,5',
            'keep_going_forward' => 'boolean',
        ]);

        $routes = TripBuilder::search(
            $request->input('departure_location'),
            $request->input('destination_location'),
            $request->input('departure_date'),
            $request->input('return_date', ''),
            $request->input('restrict_airlines', ''),
            $request->input('page_size', 20),
            $request->input('page_number', 1),
            $request->input('sort_by', 'duration'),
            $request->input('maxmum_stops', 2),
            $request->boolean('keep_going_forward', true)
        );

        return response()->json($routes);
    }
}
