<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FlightService;
use App\mockData\Data;

class DefaultController extends AbstractController
{
    private $flightService;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
        
    }

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

        $response->setContent(json_encode(Data::FLIGHTS));

        return $response;
    }

    /**
     * @Route("/api/airports", name="airports")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAirports()
    {

        $res = [];
        foreach (Data::AIRPORTS as $airport) {
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

        $resultFlight = [];
        $params = $request->query->all();
        if (empty($params) || empty($from = $params['fromVal']) || empty($to = $params['toVal'])) {
            $response->setContent(json_encode($resultFlight));
            return $response;
        }

        if ($from === $to){
            $response->setContent(json_encode($resultFlight));
            return $response;
        }

        $resultFlight = $this->flightService->getBestFlight($from, $to);
        $response->setContent(json_encode($resultFlight));
        return $response;
    }

}
