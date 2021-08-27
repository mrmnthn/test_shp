<?php
namespace App\Service;

use App\mockData\Data;

class FlightService
{
    const MIN_DEPARTURE_VAL = 1;

    public function getBestFlight($from, $to)
    {
        $res = [];
        $directFlight = $this->getDirectFlight($from, $to);

        if ($directFlight) {
            $res = [
                'from' => $this->getAirportNameById($from),
                'to' => $this->getAirportNameById($to),
                'stopovers' => 0,
                'price' => $directFlight['price'],
            ];
            return $res;
        }

        $departureFlights = $this->departureFlightsById($from);
        $stopOverFlight = $this->getPathWithStepOver($to, $departureFlights);

        if ($stopOverFlight) {
            $bestFlightPathWithStopovers = $this->getBestFlightPathWithStopovers($stopOverFlight);
            $res = [
                'from' => $this->getAirportNameById($from),
                'to' => $this->getAirportNameById($to),
                'stopovers' => $this->countStopover($bestFlightPathWithStopovers),
                'price' => $this->stepOverFlightTotalPrice($bestFlightPathWithStopovers),
            ];
            return $res;
        }

        $res = [
            'from' => $this->getAirportNameById($from),
            'to' => $this->getAirportNameById($to),
            'stopovers' => 0,
            'price' => 0,
        ];

        return $res;
    }

    /**
     * From the list of flight with stopovers, return the best price
     */
    private function getBestFlightPathWithStopovers($stopOverFlight)
    {
        $stopoversOrderByPrice = [];
        foreach ($stopOverFlight as $key => $stopovers) {
            $totalPrice = array_sum(array_column($stopovers, 'price'));
            $stopoversOrderByPrice[$totalPrice] = $stopovers;
        }
        return $this->getBestPriceFromGroup($stopoversOrderByPrice);

    }
    
    /**
     * Count the number of stopovers, excluding the departure airport
     */
    private function countStopover($stopovers)
    {
        return (count($stopovers) - self::MIN_DEPARTURE_VAL);
        
    }

    /**
     * Given an id, return the airport name
     */
    private function getAirportNameById($id)
    {
        foreach (Data::AIRPORTS as $airport) {
            if ($airport['id'] == $id) {
                return $airport['name'];
            }
        }
    }

    /**
     * Compute the fligths total price
     */
    private function stepOverFlightTotalPrice($stepOverFlights)
    {
        return array_sum(array_column($stepOverFlights, 'price'));
    }

    /**
     * Return the best path with max 2 stopovers
     */
    private function getPathWithStepOver($to, $departuresGroup)
    {
        foreach ($departuresGroup as $departureFlight) {
            $currentTo = $departureFlight['code_arrival'];
            foreach (Data::FLIGHTS as $stopoverFlight) {
                if ($departureFlight['code_arrival'] == $stopoverFlight['code_departure']) {
                    $stopovers[$currentTo] = ['dep' => $departureFlight, 'stopovers' => [$stopoverFlight]];
                }
            }
        }

        if (! empty($stopovers)) {
            foreach ($stopovers as $key => $path) {
                foreach ($path['stopovers'] as $stopover) {
                    if ($stopover['code_arrival'] == $to) {
                        $completePath = array_values($stopovers[$key]['stopovers']);
                        $completePath[] = $stopovers[$key]['dep'];
                        return $completePath;
                    }
                    foreach (Data::FLIGHTS as $stopoverFlight) {
                        if ($stopover['code_arrival'] == $stopoverFlight['code_departure']) {
                            $stopovers[$key]['stopovers'][] = $stopoverFlight;
                        }
                    }
                }
            }

            foreach ($stopovers as $key => $path) {
                foreach ($path['stopovers'] as $stopover) {
                    if ($stopover['code_arrival'] == $to) {
                        $completePath[$key] = array_values($stopovers[$key]['stopovers']);
                        $completePath[$key][] = $stopovers[$key]['dep'];
                    }
                }
            }
            return $completePath;
        }

        return false;
    }

    /**
     * Return a direct flight
     */
    private function getDirectFlight($dep, $arr)
    {
        $directFlights = [];
        foreach (Data::FLIGHTS as $flight) {
            if ($flight['code_departure'] == $dep && $flight['code_arrival'] == $arr) {
                $directFlights[] = $flight;
            }
        }

        return $this->getBestPriceFromGroup($directFlights);
    }

    /**
     * return the departure flights by airport id
     */
    private function departureFlightsById($id)
    {
        $filteredFlights = [];

        foreach (Data::FLIGHTS as $flight) {
            if ($flight['code_departure'] != $id) {
                continue;
            }
            $filteredFlights[] = $flight;
        }
        return $filteredFlights;
    }

    /**
     * return the best price given a group of flights
     */
    private function getBestPriceFromGroup($flightGroup)
    {
        asort($flightGroup);
        return current($flightGroup);
    }
}
