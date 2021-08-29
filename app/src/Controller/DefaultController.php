<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FlightService;

class DefaultController extends AbstractController
{
    private $flightService;
    private $response;

    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
        $this->response = new Response();
        $this->response->headers->set('Content-Type', 'application/json');
        $this->response->headers->set('Access-Control-Allow-Origin', '*');
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
        $flights = $this->flightService->getFlights();
        $this->response->setContent(json_encode($flights));

        return $this->response;
    }

    /**
     * This method is used to fill the multiselect option form
     * @Route("/api/airports", name="airports")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllAirportsNameAndId()
    {
        $airports = $this->flightService->getAllAirportsNameAndId();
        $this->response->setContent(json_encode($airports));

        return $this->response;
    }

    /**
     * @Route("/api/bestflights", name="bestflights")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBestFlights(Request $request)
    {
        $resultFlight = [];
        $params = $request->query->all();
        
        if (empty($params) || empty($params['fromVal']) || empty($params['toVal'])) {
            $this->response->setContent(json_encode($resultFlight));
            return $this->response;
        }
        $from = $params['fromVal'];
        $to = $params['toVal'];

        if ($from === $to){
            $this->response->setContent(json_encode($resultFlight));
            return $this->response;
        }

        $resultFlight = $this->flightService->getBestFlight($from, $to);
        $this->response->setContent(json_encode($resultFlight));
        return $this->response;
    }

}
