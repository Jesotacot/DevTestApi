<?php

namespace TicketShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use TicketShopBundle\Utils\DevTestAPIClient;

class EventsController extends Controller {

    public function selectEventAction(Request $request) {

        //Create an API client
        $client = new DevTestAPIClient();

        //Get all events from the API
        $events = $client->getAllEvents();

        if($events===false){
          return $this->render('TicketShopBundle:Events:error.html.twig');
        }
        //Create the options to choose
        foreach ($events as $event) {
            $select[$event->id] = $event->name;
        }

        //Create a form
        $form = $this->createFormBuilder()
                ->add('Events', ChoiceType::class, array('choices' => array('Events' => $select)))
                ->add('Save', SubmitType::class, array('label' => 'Save'))
                ->getForm();

        //Detect if the form was sent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          //Get the selected events
            $value = $form->get('Events')->getData();
            //Redirect to show the tickets
            return $this->redirectToRoute('ticket_shop_buy_tickets', array('id_event' => $value));
        }

        //Show the events and the form
        return $this->render('TicketShopBundle:Events:selectEvent.html.twig', array(
                    'form' => $form->createView(),
                    'events' => $events
        ));
    }

}
