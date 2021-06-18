<?php

namespace App\Utilities;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;

use \DateTime;
use \DateInterval;
use \DateTimeZone;


class TripBuilder {

    protected static $validRoutes = [];
    protected static $maxmumStops;
    protected static $keepGoingForward;
    protected static $restrictAirlines;

    public static function search($departureLocation, $destinationLocation, $departureDate,
        $returnDate = null, $restrictAirlines = '', $pageSize = 20, $pageNumber = 1, $sortBy = 'duration',
        $maxmumStops = 1, $keepGoingForward = true){

        self::$maxmumStops = $maxmumStops;
        self::$keepGoingForward = $keepGoingForward;
        self::$restrictAirlines = $restrictAirlines ? explode(',', $restrictAirlines) : [];

        $departureAirports = Airport::where('city_code', $departureLocation)
            ->orWhere('iata', $departureLocation)
            ->pluck('iata')
            ->toArray();
        $destinationAirports = Airport::where('city_code', $destinationLocation)
            ->orWhere('iata', $destinationLocation)
            ->pluck('iata')
            ->toArray();

        self::$validRoutes = [];
        self::buildRoutes($departureAirports, $destinationAirports);
        $validOnwardRoutes = self::$validRoutes;
        $validOnwardRoutes = self::calculateFlightDatetime($validOnwardRoutes, $departureDate); 
        if(!$returnDate){
            $formattedRoutes = self::formatRoutes($validOnwardRoutes, $sortBy, $pageSize, $pageNumber, $returnDate);
            return $formattedRoutes;
        }

        self::$validRoutes = [];
        self::buildRoutes($destinationAirports, $departureAirports);
        $validReturnRoutes = self::$validRoutes;
        $validReturnRoutes = self::calculateFlightDatetime($validReturnRoutes, $returnDate); 

        $comboRoutes = self::generateCombo($validOnwardRoutes, $validReturnRoutes);

        $formattedRoutes = self::formatRoutes($comboRoutes, $sortBy, $pageSize, $pageNumber, $returnDate);
        return $formattedRoutes;
    }

    protected static function formatRoutes($routes, $sortBy, $pageSize, $pageNumber, $isRoundTrip = null){
        $formattedRoutes = [];
        foreach($routes as $route){
            $durationInSeconds = self::getRouteDuration($route, $isRoundTrip);
            $formattedRoutes[] = [
                'stops' => count($route) - ($isRoundTrip ? 2 : 1),
                'price' => array_sum(array_column($route,'price')),
                'duration' => self::secondsToTime($durationInSeconds),
                'duration_in_seconds' => $durationInSeconds,
                'flights' => $route
            ];
        }

        $sortBy == 'duration' && usort($formattedRoutes,
            function($a, $b){return $a['duration_in_seconds'] - $b['duration_in_seconds'];});
        $sortBy == 'price' && usort($formattedRoutes,
            function($a, $b){return $a['price'] - $b['price'];});
        $sortBy == 'stops' && usort($formattedRoutes,
            function($a, $b){return $a['stops'] - $b['stops'];});

        $chunkedRoutes = array_chunk($formattedRoutes, $pageSize);
        $pageNumber > count($chunkedRoutes) && $pageNumber = count($chunkedRoutes);
        $pageNumber < 1 && $pageNumber = 1;
        return [
            'page_number' => $pageNumber,
            'page_size' => $pageSize,
            'total_routes' => count($formattedRoutes),
            'total_pages' => count($chunkedRoutes),
            'trips' => $chunkedRoutes ? $chunkedRoutes[$pageNumber - 1] : []
        ];
    }

    protected static function getRouteDuration($routes, $isRoundTrip){
        if(!$routes) return '';
        $onwardDepartureDatetime = ''; 
        $onwardArrivalTime = ''; 
        $returnDepartureDatetime = '';
        $returnArrivalTime = '';
        foreach($routes as $flight){
            if((!isset($flight['direction']) || $flight['direction'] == 'onward')){
                !$onwardDepartureDatetime && $onwardDepartureDatetime = $flight->departure_timestamp;
                $onwardArrivalTime = $flight->arrival_timestamp;
            }
            if(isset($flight['direction']) && $flight['direction'] == 'return'){
                !$returnDepartureDatetime && $returnDepartureDatetime = $flight->departure_timestamp;
                $returnArrivalTime = $flight->arrival_timestamp;
            }
        }

        $onwardRouteDuration = $onwardArrivalTime - $onwardDepartureDatetime;
        if(!$isRoundTrip) return $onwardRouteDuration;

        $returnRouteDuration = $returnArrivalTime - $returnDepartureDatetime;
        return $onwardRouteDuration + $returnRouteDuration;
    }

    protected static function buildRoutes($departureAirports, $destinationAirports, $routes = []){
        $FlightQuery = Flight::whereIn('departure_airport', $departureAirports);
        self::$restrictAirlines && $FlightQuery->whereIn('airline', self::$restrictAirlines);
        $possibleFlights = $FlightQuery->select(['airline', 'flight_number', 'departure_airport',
            'departure_time', 'arrival_airport', 'arrival_time', 'duration', 'price'])
            ->get();
        foreach($possibleFlights as $possibleFlight){
            $isLastFlight = in_array($possibleFlight->arrival_airport, $destinationAirports);
            if($isLastFlight){
                // found a valid route;
                $validRoute = $routes;
                $validRoute[] = $possibleFlight;
                self::$validRoutes[] = $validRoute;
            }elseif(!$isLastFlight && self::$maxmumStops == count($routes)){
                // reach the maxmum stops and did not find a valid route, discard this route. 
                continue;
            }elseif(self::$keepGoingForward 
                && !self::isGoingForward($departureAirports, $possibleFlight->arrival_airport, $destinationAirports)){
                // not keep going forward, discard this route 
                continue;
            }elseif(self::notGoSomewhereHaveComeFrom($possibleFlight->arrival_airport, $routes)){
                // keep searching next node;
                $nextRouts = $routes;
                $nextRouts[] = $possibleFlight;
                self::buildRoutes([$possibleFlight->arrival_airport], $destinationAirports, $nextRouts);
            } 
        }
    }

    protected static function notGoSomewhereHaveComeFrom($airport, $routes){
        foreach($routes as $route){
            if($route->departure_airport == $airport || $route->arrival_airport == $airport){
                return false;
            }
        }
        return true;
    }

    protected static function isGoingForward($currentAirports, $nextAirport, $destinationAirports){
        $currentAirportCoordinates = Airport::where('iata', $currentAirports[0])
            ->select(['latitude', 'longitude'])
            ->first();
        $nextAirportCoordinates = Airport::where('iata', $nextAirport)
            ->select(['latitude', 'longitude'])
            ->first();
        $destinationAirportCoordinates = Airport::where('iata', $destinationAirports[0])
            ->select(['latitude', 'longitude'])
            ->first();

        return self::getDistance(
            $currentAirportCoordinates->latitude,
            $currentAirportCoordinates->longitude,
            $destinationAirportCoordinates->latitude,
            $destinationAirportCoordinates->longitude)
        > 
        self::getDistance(
            $nextAirportCoordinates->latitude,
            $nextAirportCoordinates->longitude,
            $destinationAirportCoordinates->latitude,
            $destinationAirportCoordinates->longitude);
    }

    protected static function generateCombo($onwardRoutes, $returnRoutes){
        $comboRoutes = [];
        foreach($onwardRoutes as $onwardRoute){
            $onwardRoute = array_map(function($v){$v['direction'] = 'onward'; return $v;}, $onwardRoute);
            $lastArrivalFlightTimestamp = $onwardRoute[count($onwardRoute) - 1]->arrival_timestamp;
            foreach($returnRoutes as $returnRoute){
                $firstReturnFlightTimestamp = $returnRoute[0]->departure_timestamp;
                if($firstReturnFlightTimestamp <= $lastArrivalFlightTimestamp) continue;
                $returnRoute = array_map(function($v){$v['direction'] = 'return'; return $v;}, $returnRoute);
                $comboRoutes[] = array_merge($onwardRoute, $returnRoute);
            }
        }
        return $comboRoutes;
    }

    protected static function calculateFlightDatetime($routes, $departureDate){
        foreach($routes as $routsIndex => $route){
            $currentRouteDepartureDate = $departureDate;
            foreach($route as $flightIndex => $flight){
                $currentAirportTimezone = Airport::where('iata', $flight->departure_airport)
                    ->value('tz_database_time_zone');
                $dateTime = new DateTime("$currentRouteDepartureDate {$flight->departure_time}:00",
                    new DateTimeZone($currentAirportTimezone));
                $routes[$routsIndex][$flightIndex]['departure_datetime'] = $dateTime->format('Y-m-d H:i:sP');
                $routes[$routsIndex][$flightIndex]['departure_timestamp'] = $dateTime->getTimestamp();

                $dateTime->add(new DateInterval(self::convertDuration($flight->duration)));
                $arrivalAirportTimezone = Airport::where('iata', $flight->arrival_airport)
                    ->value('tz_database_time_zone');
                $dateTime->setTimezone(new DateTimeZone($arrivalAirportTimezone));
                $routes[$routsIndex][$flightIndex]['arrival_datetime'] = $dateTime->format('Y-m-d H:i:sP');
                $routes[$routsIndex][$flightIndex]['arrival_timestamp'] = $dateTime->getTimestamp();

                if(isset($route[$flightIndex + 1])){
                    $nextFlightDepartureTime = $route[$flightIndex +1]->departure_time;
                    if($dateTime->format('H:i') > $nextFlightDepartureTime){
                        $currentRouteDepartureDate = $dateTime->add(new DateInterval('P1D'))->format('Y-m-d');
                    }else{
                        $currentRouteDepartureDate = $dateTime->format('Y-m-d');
                    }
                }
            }
        }
        return $routes; 
    }

    protected static function convertDuration($duration){
        sscanf($duration, "%d:%d:%d", $hours, $minutes, $seconds);
        $newDuration = 'PT';
        $hours && $newDuration .= "{$hours}H";
        $minutes && $newDuration .= "{$minutes}M";
        return $newDuration;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    protected static function getDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    protected static function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days %h hours %i minutes');
    }

}
