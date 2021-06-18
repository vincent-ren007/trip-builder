<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Airline;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airlines = File::get(base_path('misc/airlines.dat'));
        $airlineArr = explode("\n", trim($airlines));
        array_shift($airlineArr);

        $offset = 0;
        $airlineCount = count($airlineArr);
        $barOutput = $this->command->getOutput();
        //$outputCommand = $this->command;
        $processingBar = $barOutput->createProgressBar($airlineCount);
        $processingBar->setFormat("[seeding airlines] %current%/%max% [%bar%] %percent:3s%% %memory:6s%\n");
        $processingBar->advance($offset);

        foreach($airlineArr as $airline){
            $processingBar->advance();
            $airline = str_replace('"', '', $airline);
            $airline = str_replace('\N', '', $airline);
            $airlineInfo = explode(',', $airline);
            list(
                $openFlightId,
                $name,
                $alias,
                $IATA,
                $ICAO,
                $callsign,
                $country,
                $active
            ) = $airlineInfo;
            if($country != 'Canada') continue;
            Airline::updateOrInsert(
                [
                    'open_flight_id' => $openFlightId,
                    'name' => $name,
                    'alias' => $alias,
                    'iata' => $IATA,
                    'icao' => $ICAO,
                    'callsign' => $callsign,
                    'country' => $country,
                    'active' => $active
                ]
            );
        }

        $processingBar->finish();
    }
}
