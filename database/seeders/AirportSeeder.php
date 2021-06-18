<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\File;
use App\Models\Airport;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedAirportData();
        $this->updateCityCode();
    }

    protected function seedAirportData()
    {
        $airprots = File::get(base_path('misc/airports.dat'));
        $airprotArr = explode("\n", trim($airprots));

        $offset = 0;
        $airprotCount = count($airprotArr);
        $barOutput = $this->command->getOutput();
        //$outputCommand = $this->command;
        $processingBar = $barOutput->createProgressBar($airprotCount);
        $processingBar->setFormat("[seeding airports] %current%/%max% [%bar%] %percent:3s%% %memory:6s%\n");
        $processingBar->advance($offset);

        foreach($airprotArr as $airprot){
            $processingBar->advance();
            $airprot = str_replace('"', '', $airprot);
            $airprot = str_replace('\N', '', $airprot);
            $airprotInfo = explode(',', $airprot);
            list(
                $openFlightId,
                $name,
                $city,
                $country,
                $IATA,
                $ICAO,
                $latitude,
                $longitude,
                $altitude,
                $timezone,
                $DST,
                $tzDatabaseTimeZone,
                $type,
                $source
            ) = $airprotInfo;
            //echo "$name\n";
            if($country != 'Canada') continue;
            Airport::updateOrInsert(
                [
                    'open_flight_id' => $openFlightId,
                    'name' => $name,
                    'city' => $city,
                    'country' => $country,
                    'iata' => $IATA,
                    'city_code' => $IATA,
                    'icao' => $ICAO,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'altitude' => $altitude,
                    'timezone' => $timezone,
                    'dst' => $DST,
                    'tz_database_time_zone' => $tzDatabaseTimeZone,
                    'type' => $type,
                    'source' => $source
                ]
            );
        }

        $processingBar->finish();
    }

    protected function updateCityCode(){
        $cityCodeMap = [
            'YMQ' => ['YUL', 'YMY'],
            'YTO' => ['YYZ', 'YTZ', 'YKF'],
            'YEA' => ['YEG'],
        ];

        foreach($cityCodeMap as $cityCode => $airprots){
            $result = Airport::whereIn('iata', $airprots)->update(['city_code' => $cityCode]);
        }
    }

}
