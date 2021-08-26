<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    const MAX_STEPOVER_COUNT = 2;
    const AIRPORTS = [
        [
            'id' => 1,
            'name' => 'London Heathrow',
            'code' => 'LHR',
            'lat' => 51.47330517282574,
            'lng' => -0.4668831877433046,
        ],
        [
            'id' => 2,
            'name' => 'Rome',
            'code' => 'FCO',
            'lat' => 41.79352675673652,
            'lng' => 12.245967968498032,
        ],
        [
            'id' => 3,
            'name' => 'Florence',
            'code' => 'FLR',
            'lat' => 43.80847125596117,
            'lng' => 11.20109788538418,
        ],
        [
            'id' => 4,
            'name' => 'Palermo',
            'code' => 'PMO',
            'lat' => 38.18257906912988,
            'lng' => 13.100383129085099,
        ],
        [
            'id' => 5,
            'name' => 'London Gatwick',
            'code' => 'LGW',
            'lat' => 51.15366880896917,
            'lng' => -0.18217018960238596,
        ],
        [
            'id' => 6,
            'name' => 'Milan Linate Airport',
            'code' => 'LIN',
            'lat' => 45.45224371588605,
            'lng' => 9.276297269807706,
        ],
    ];

    const FLIGHTS = [
        [
            'code_departure' => 1,
            'code_arrival' => 2,
            'price' => 29
        ],
        [
            'code_departure' => 2,
            'code_arrival' => 4,
            'price' => 66
        ],
        [
            'code_departure' => 1,
            'code_arrival' => 3,
            'price' => 530
        ],
        [
            'code_departure' => 3,
            'code_arrival' => 2,
            'price' => 89
        ],
        [
            'code_departure' => 2,
            'code_arrival' => 3,
            'price' => 56
        ],
        [
            'code_departure' => 5,
            'code_arrival' => 1,
            'price' => 12
        ],

        [
            'code_departure' => 2,
            'code_arrival' => 3,
            'price' => 60
        ],
        [
            'code_departure' => 3,
            'code_arrival' => 5,
            'price' => 70
        ],
        [
            'code_departure' => 3,
            'code_arrival' => 4,
            'price' => 70
        ],
        [
            'code_departure' => 4,
            'code_arrival' => 6,
            'price' => 25
        ],

        [
            'code_departure' => 1,
            'code_arrival' => 2,
            'price' => 45
        ],
        [
            'code_departure' => 2,
            'code_arrival' => 6,
            'price' => 80
        ],
        [
            'code_departure' => 1,
            'code_arrival' => 2,
            'price' => 58
        ],
        [
            'code_departure' => 2,
            'code_arrival' => 3,
            'price' => 58
        ],
        [
            'code_departure' => 3,
            'code_arrival' => 6,
            'price' => 10
        ],
        [
            'code_departure' => 1,
            'code_arrival' => 5,
            'price' => 58
        ],
    ];

    /**
     * @Route("/{reactRouting}", name="home", defaults={"reactRouting": null})
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/api/flights", name="flights")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFlights()
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode(self::FLIGHTS));

        return $response;
    }

    /**
     * @Route("/api/airports", name="airports")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAirports()
    {

        $res = [];
        foreach (self::AIRPORTS as $airport) {
            $res[] = [
                'text' => $airport['name'],
                'value' => $airport['id']
            ];
        }

        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode($res));

        return $response;
    }

    /**
     * @Route("/api/bestflights", name="bestflights")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBestFlights(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $res = [];
        $params = $request->query->all();
        if (
            empty($params)
            || empty($from = $params['fromVal'])
            || empty($to = $params['toVal'])
        ) {
            $response->setContent(json_encode($res));
            return $response;
        }
        $departureFlights = $this->departureFlightsById($from);
        $arrivalFlights = $this->arrivalFlightsById($to);
        $assoc = $this->getArrayIntersectAssocRecursive($departureFlights, $arrivalFlights);

        if ($assoc) {
            $bestPrice = $this->getBestPriceFromGroup($assoc);
            $res = [
                'from' => $from,
                'to' => $to,
                'stopover' => 0,
                'price' => $bestPrice['price'],
            ];
            $response->setContent(json_encode($res));
            return $response;
        }

        $stepOverFlight = $this->getPathWithStepOver($departureFlights, $arrivalFlights);

        if (count($stepOverFlight) > self::MAX_STEPOVER_COUNT) {
            $res = [
                'from' => $from,
                'to' => $to,
                'stopover' => count($stepOverFlight),
                'price' => 0,
            ];
            $response->setContent(json_encode($res));
            return $response;
        }

        $res = [
            'from' => $this->airportNameById($from),
            'to' => $this->airportNameById($to),
            'stopover' => count($stepOverFlight),
            'price' => $this->stepOverFlightTotalPrice($stepOverFlight),
        ];

        $response->setContent(json_encode($res));
        return $response;
    }

    private function airportNameById($id)
    {
        foreach(self::AIRPORTS as $airport){
            if ($airport['id'] == $id) {
                return $airport['name'];
            }
        }
    }

    private function stepOverFlightTotalPrice($stepOverFlights)
    {
        return array_sum(array_column($stepOverFlights, 'price'));
    }

    private function getPathWithStepOver($arrayDep, $arrayArr)
    {
        $res = [];
        foreach ($arrayDep as $dep) {
            foreach ($arrayArr as $arr) {
                if ($dep['code_arrival'] != $arr['code_departure']) {
                    continue;
                }
                $res[] = $arr;
                $res[] = $dep;
            }
        }
        return $res;
    }

    private function getArrayIntersectAssocRecursive(&$value1, &$value2)
    {
        if (!is_array($value1) || !is_array($value1)) {
            return $value1 === $value2;
        }

        $intersectKeys = array_intersect(array_keys($value1), array_keys($value2));

        $intersectValues = [];
        foreach ($intersectKeys as $key) {
            if ($this->getArrayIntersectAssocRecursive($value1[$key], $value2[$key])) {
                $intersectValues[$key] = $value1[$key];
            }
        }

        return $intersectValues;
    }

    /**
     * 
     */
    private function departureFlightsById($id)
    {
        $filteredFlights = [];

        foreach (self::FLIGHTS as $flight) {
            if ($flight['code_departure'] != $id) {
                continue;
            }
            $filteredFlights[] = $flight;
        }

        $distinctResult = [];
        foreach ($filteredFlights as $flight) {
            $currentKey = $flight['code_arrival'];
            if(empty($distinctResult)){
                $distinctResult[$currentKey] = $flight;
            }

            if (array_key_exists($currentKey, $distinctResult)
                && $flight['price'] >= $flight['price']
            ) {
                continue;
            }
            $distinctResult[$currentKey] = $flight;

        }
        return $distinctResult;
    }


    private function arrivalFlightsById($id)
    {
        $filteredFlights = [];

        foreach (self::FLIGHTS as $flight) {
            if ($flight['code_arrival'] != $id) {
                continue;
            }
            $filteredFlights[] = $flight;
        }

        $distinctResult = [];
        foreach ($filteredFlights as $flight) {
            $currentKey = $flight['code_departure'];
            if(empty($distinctResult)){
                $distinctResult[$currentKey] = $flight;
            }

            if (array_key_exists($currentKey, $distinctResult)
                && $flight['price'] >= $flight['price']
            ) {
                continue;
            }
            $distinctResult[$currentKey] = $flight;

        }
        return $distinctResult;
    }

    private function getBestPriceFromGroup($array)
    {
        asort($array);
        return current($array);
    }
}
