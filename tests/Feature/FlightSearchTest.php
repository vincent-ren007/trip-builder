<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FlightSearchTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function test_input_validation(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => '',
            'destination_location' => '',
            'departure_date' => '2001-07-01',
            'return_date' => '2000-07-01',
            'restrict_airlines' => 'UNKNOW',
            'page_size' => 0,
            'page_number' => 0,
            'sort_by' => 'departure_date',
            'maxmum_stops' => 100,
            'keep_going_forward' => null,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'departure_location',
                     'destination_location',
                     'departure_date',
                     'return_date',
                     'restrict_airlines',
                     'page_size',
                     'page_number',
                     'sort_by',
                     'maxmum_stops',
                     'keep_going_forward',
                 ]);
    }


    /**
     *
     * @return void
     */
    public function test_maxmum_stop(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => 'YUL',
            'destination_location' => 'YVR',
            'departure_date' => date("Y-m-d", strtotime('+1 week')),
            'page_size' => 99999,
            'maxmum_stops' => 0
        ]);

        $response->assertStatus(200)
                 ->assertJsonMissing(["stops" => 1])
                 ->assertJsonMissing(["stops" => 2])
                 ->assertJsonMissing(["stops" => 3])
                 ->assertJsonMissing(["stops" => 4])
                 ->assertJsonMissing(["stops" => 5]);
    }


    /**
     *
     * @return void
     */
    public function test_keep_going_forward_on(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => 'YUL',
            'destination_location' => 'YVR',
            'departure_date' => date("Y-m-d", strtotime('+1 week')),
            'page_size' => 99999,
            'keep_going_forward' => false
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(["arrival_airport" => "YHZ"]);
    }


    /**
     *
     * @return void
     */
    public function test_keep_going_forward_off(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => 'YUL',
            'destination_location' => 'YVR',
            'departure_date' => date("Y-m-d", strtotime('+1 week')),
            'page_size' => 99999,
            'keep_going_forward' => true 
        ]);

        $response->assertStatus(200)
                 ->assertJsonMissing(["arrival_airport" => "YHZ"]);
    }


    /**
     *
     * @return void
     */
    public function test_minimal_input(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => 'YUL',
            'destination_location' => 'YVR',
            'departure_date' => date("Y-m-d", strtotime('next week')),
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     "page_number",
                     "page_size",
                     "total_routes",
                     "total_pages",
                     "trips"
                 ]);
    }


    /**
     *
     * @return void
     */
    public function test_maximal_input(){

        $response = $this->post('/api/flight/search', [
            'departure_location' => 'YUL',
            'destination_location' => 'YVR',
            'departure_date' => date("Y-m-d", strtotime('+1 week')),
            'return_date' => date("Y-m-d", strtotime('+2 week')),
            'restrict_airlines' => 'AC,F8',
            'page_size' => 30,
            'page_number' => 1,
            'sort_by' => 'duration',
            'maxmum_stops' => 2,
            'keep_going_forward' => true,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     "page_number",
                     "page_size",
                     "total_routes",
                     "total_pages",
                     "trips"
                 ]);
    }


}
