<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flights = [
            ['AC', 7958, 'YTZ', '10:15', 'YUL', '11:25', '01:10', '70'],
            ['AC', 7968, 'YTZ', '15:15', 'YUL', '16:25', '01:10', '70'],

            ['AC', 7961, 'YUL', '11:30', 'YTZ', '12:40', '01:10', '70'],
            ['AC', 7963, 'YUL', '12:30', 'YTZ', '13:40', '01:10', '70'],
            
            ['F8', 121, 'YUL', '09:35', 'YYZ', '11:00', '01:25', '85'],
            ['AC', 403, 'YUL', '08:00', 'YYZ', '09:23', '01:23', '83'],
            ['WS', 3523, 'YUL', '10:00', 'YYZ', '11:31', '01:31', '91'],

            ['F8', 120, 'YYZ', '07:30', 'YUL', '08:50', '01:20', '80'],
            ['AC', 400, 'YYZ', '07:00', 'YUL', '08:16', '01:16', '76'],
            ['WS', 3524, 'YYZ', '11:00', 'YUL', '12:28', '01:28', '88'],

            ['F8', 223, 'YYZ', '12:00', 'YVR', '14:00', '05:00', '300'],
            ['AC', 121, 'YYZ', '19:00', 'YVR', '20:51', '04:51', '291'],
            ['WS', 705, 'YYZ', '09:00', 'YVR', '11:09', '05:09', '309'],
        
            ['WO', 428, 'YYZ', '08:00', 'YHZ', '11:01', '02:01', '121'],
            ['AC', 604, 'YYZ', '08:30', 'YHZ', '11:37', '02:07', '127'],
            ['WS', 248, 'YYZ', '10:00', 'YHZ', '13:04', '02:04', '124'],

            ['WO', 429, 'YHZ', '11:50', 'YYZ', '13:12', '02:22', '142'],
            ['WS', 273, 'YHZ', '15:00', 'YYZ', '16:20', '02:20', '140'],
            ['AC', 611, 'YHZ', '15:55', 'YYZ', '17:19', '02:24', '144'],

            ['F8', 224, 'YVR', '15:00', 'YYZ', '22:30', '04:30', '270'],
            ['WS', 700, 'YVR', '06:00', 'YYZ', '13:27', '04:27', '267'],
            ['WS', 704, 'YVR', '08:00', 'YYZ', '15:30', '04:30', '270'],


            ['AC', 301, 'YUL', '08:05', 'YVR', '10:27', '05:22', '322'],
            ['WS', 543, 'YUL', '17:00', 'YVR', '19:35', '05:35', '335'],
            ['AC', 303, 'YUL', '09:25', 'YVR', '11:59', '05:34', '334'],
            ['TS', 772, 'YUL', '18:55', 'YVR', '21:30', '05:35', '335'],
            ['AC', 311, 'YUL', '18:55', 'YVR', '21:12', '05:17', '317'],

            ['AC', 301, 'YVR', '08:40', 'YUL', '16:05', '04:25', '265'],
            ['WS', 542, 'YVR', '08:49', 'YUL', '16:34', '04:45', '285'],
            ['WS', 564, 'YVR', '23:25', 'YUL', '07:18', '04:53', '293'],
            ['AC', 304, 'YVR', '08:15', 'YUL', '15:54', '04:39', '279'],

            ['AC', 668, 'YUL', '20:20', 'YHZ', '22:51', '01:31', '91'],
            ['AC', 660, 'YUL', '08:05', 'YHZ', '10:36', '01:31', '91'],
            ['WS', 3451, 'YUL', '09:45', 'YHZ', '12:29', '01:44', '104'],
            ['WS', 3453, 'YUL', '18:50', 'YHZ', '21:37', '01:47', '107'],
            ['AC', 7556, 'YUL', '13:15', 'YHZ', '15:45', '01:30', '90'],

            ['F8', 828, 'YHZ', '16:25', 'YUL', '17:10', '01:45', '105'],
            ['AC', 661, 'YHZ', '06:35', 'YUL', '07:10', '01:35', '95'],
            ['WS', 3450, 'YHZ', '08:00', 'YUL', '08:57', '01:57', '117'],
        ];


        $offset = 0;
        $flightCount = count($flights);
        $barOutput = $this->command->getOutput();
        $processingBar = $barOutput->createProgressBar($flightCount);
        $processingBar->setFormat("[seeding flights] %current%/%max% [%bar%] %percent:3s%% %memory:6s%\n");
        $processingBar->advance($offset);

        foreach($flights as $flight){
            list(
                $airline,
                $flightNumber,
                $departureAirport,
                $departureTime,
                $arrivalAirport,
                $arrivalTime,
                $duration,
                $price
            ) = $flight;

            Flight::updateOrCreate(
                ['airline' => $airline, 'flight_number' => $flightNumber],
                [
                    'departure_airport' => $departureAirport,
                    'departure_time' => $departureTime,
                    'arrival_airport' => $arrivalAirport,
                    'arrival_time' => $arrivalTime,
                    'duration' => $duration,
                    'price' => $price
                ]
            );

            $processingBar->advance();
        }
    }
}
