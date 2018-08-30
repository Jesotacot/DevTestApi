<?php

namespace TicketShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use TicketShopBundle\Entity\Ticket;

class ApiController extends Controller {

    public function checkAction(Request $request) {

        //Check if request is POST method
        if (!$request->isMethod('post')) {
            //Send a error message
            $response = new JsonResponse(array("error" => array("code" => 405, "message" => "Method Not Allowed")), 405);
        } else {
            //Get code
            $code = $request->request->get('code');

            if ($code == null) {
                //Send a warning message
                $response = new JsonResponse(array("code" => "value"), 200);
            } else {
                //Check if code is in valid format
                if (preg_match('/^[0-9a-z]{20}$/', $code) !== 1) {
                    //Send a error message
                    $response = new JsonResponse(array("code" => "Error: " . $code . " Value is not valid"), 400);
                } else {
                    $ticket = $this->searchTicketByCode($code);
                    //Check if ticket is in DB
                    if (empty($ticket)) {
                        $response = new JsonResponse(array("code" => "Error: " . $code . " Value is not found"), 200);
                        //Check if code is already used
                    } else if (!empty($ticket->getDateofuse())) {
                        $response = new JsonResponse(
                                array(
                            "code" => "Error: " . $code . " Value is already used",
                            "message" => "Date of use:" . $ticket->getDateofuse()->format('Y-m-d H:i:s'),
                            "order number" => $ticket->getOrdernum()
                                )
                                , 200);
                    } else {
                        //Return ticket and uuid
                        $response = new JsonResponse(
                                array(
                            "code" => $code . " OK",
                            "Ticket" => $ticket->getName(),
                            "Order Uuid" => $ticket->getOrderuuid(),
                            "Orderline Uuid" => $ticket->getOrderlineuuid(),
                                )
                                , 200);
                        $this->useTicket($ticket);
                    }
                }
            }
        }
        return $response;
    }

    /**
     * 
     * Search a ticket by his code
     * 
     * @param int $code
     * @return Ticket
     */
    private function searchTicketByCode($code) {
        $repository = $this->getDoctrine()->getRepository('TicketShopBundle:Ticket');
        $ticket = $repository->findOneByCode($code);
        return $ticket;
    }

    /**
     * 
     * Update dateofuse 
     * 
     * @param Ticket $ticket
     */
    private function useTicket(Ticket $ticket) {
        $em = $this->getDoctrine()->getManager();
        //Set dateofuse
        $ticket->setDateofuse(new \DateTime());
        //Save the ticket
        $em->persist($ticket);
        //Execute the queries
        $em->flush();
    }

}
