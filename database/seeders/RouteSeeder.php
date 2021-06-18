<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\File;
use App\Models\Airport;
use App\Models\Route;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = File::get(base_path('misc/routes.dat'));
        $routeArr = explode("\n", trim($routes));

        $offset = 0;
        $routeCount = count($routeArr);
        $barOutput = $this->command->getOutput();
        //$outputCommand = $this->command;
        $processingBar = $barOutput->createProgressBar($routeCount);
        $processingBar->setFormat("[seeding routes] %current%/%max% [%bar%] %percent:3s%% %memory:6s%\n");
        $processingBar->advance($offset);

        foreach($routeArr as $route){
            $processingBar->advance();
            $route = str_replace('"', '', $route);
            $route = str_replace('\N', '', $route);
            $routeInfo = explode(',', $route);
            list(
                $airline,
                $airlineId,
                $sourceAirport,
                $sourceAirportId,
                $destinationAirport,
                $destinationAirportId,
                $codeshare,
                $stops,
                $equipment,
            ) = $routeInfo;
            //echo "$name\n";
            $validSourceAirportId = Airport::where('open_flight_id', $sourceAirportId)->value('id');
            $validDestinationAirportId = Airport::where('open_flight_id', $destinationAirportId)->value('id');
            if(!$validSourceAirportId || !$validDestinationAirportId) continue;
            if($stops != 0) continue;

            Route::updateOrInsert(
                [
                    'airline' => $airline,
                    'airline_id' => $airlineId,
                    'source_airport' => $sourceAirport,
                    'source_airport_id' => $sourceAirportId,
                    'destination_airport' => $destinationAirport,
                    'destination_airport_id' => $destinationAirportId,
                    'codeshare' => $codeshare,
                    'stops' => $stops,
                    'equipment' => $equipment
                ]
            );
        }

        $processingBar->finish();
    }
}
