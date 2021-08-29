<?php
namespace App\Service;

use App\mockData\Data;

class FlightService
{
    const MIN_DEPARTURE_VAL = 1;

    /**
     * Given from and to coordinates return the best price flight if exist
     */
    public function getBestFlight($from, $to)
    {
        $flightResult = [
            'from' => $this->getAirportNameById($from),
            'to' => $this->getAirportNameById($to),
        ];

        $directFlight = $this->getDirectFlight($from, $to);

        if ($directFlight) {
            $flightResult['stopovers'] = 0;
            $flightResult['price'] =  $directFlight['price'];

            return $flightResult;
        }

        $stopOverFlight = $this->getPathWithStopOver($from, $to);

        if ($stopOverFlight) {
            $bestFlightPathWithStopovers = $this->getBestFlightPathWithStopovers($stopOverFlight);
            $flightResult['stopovers'] = $this->countStopover($bestFlightPathWithStopovers);
            $flightResult['price'] = $this->stopOverFlightTotalPrice($bestFlightPathWithStopovers);

            return $flightResult;
        }

        $flightResult['stopovers'] = 0;
        $flightResult['price'] = 0;

        return $flightResult;
    }

    /**
     * Return all Flights
     */
    public function getFlights()
    {
        return Data::FLIGHTS;
    }

    /**
     * Get all airports for multiselect input form
     */
    public function getAllAirportsNameAndId()
    {
        $airports = [];
        foreach (Data::AIRPORTS as $airport) {
            $airports[] = [
                'text' => $airport['name'],
                'value' => $airport['id']
            ];
        }

        return $airports;
    }

    /**
     * Given the flight's list with stopovers, return the best price
     */
    private function getBestFlightPathWithStopovers($stopOverFlight)
    {
        $stopoversOrderByPrice = [];
        foreach ($stopOverFlight as $stopovers) {
            $totalPrice = array_sum(array_column($stopovers, 'price'));
            $stopoversOrderByPrice[$totalPrice] = $stopovers;
        }

        return $this->getBestPriceFromGroup($stopoversOrderByPrice);
    }

    /**
     * Count the number of stopovers, excluding departure airport
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
    private function stopOverFlightTotalPrice($stepOverFlights)
    {
        return array_sum(array_column($stepOverFlights, 'price'));
    }

    /**
     * Return the best path with max of 2 stopovers
     */
    private function getPathWithStopOver($from, $to)
    {
        // find all flight where departure code = from
        $departuresFlights = $this->departureFlightsById($from);
        $stopovers = [];
        $completePath = [];
        //search first stopover
        foreach ($departuresFlights as $departureFlight) {
            $currentTo = $departureFlight['code_arrival'];
            foreach (Data::FLIGHTS as $stopoverFlight) {
                if ($departureFlight['code_arrival'] == $stopoverFlight['code_departure']) {
                    $stopovers[$currentTo] = ['dep' => $departureFlight, 'stopovers' => [$stopoverFlight]];
                }
            }
        }

        //if first stopover arrival airport is not = to, search for a second stopover
        if (!empty($stopovers)) {
            foreach ($stopovers as $key => $path) {
                foreach ($path['stopovers'] as $stopover) {
                    if ($stopover['code_arrival'] == $to) {
                        $completePath[$key] = array_values($stopovers[$key]['stopovers']);
                        $completePath[$key][] = $stopovers[$key]['dep'];
                        return $completePath;
                    }
                    foreach (Data::FLIGHTS as $stopoverFlight) {
                        if ($stopover['code_arrival'] == $stopoverFlight['code_departure']) {
                            $stopovers[$key]['stopovers'][] = $stopoverFlight;
                        }
                    }
                }
            }
            //if second stopover arrival airport is = to, return the list of all possible path
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
                $directFlights[$flight['price']] = $flight;
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
        ksort($flightGroup);
        return current($flightGroup);
    }
}
