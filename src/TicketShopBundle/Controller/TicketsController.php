<?php

namespace TicketShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use TicketShopBundle\Entity\Ticket;
use TicketShopBundle\Utils\DevTestAPIClient;

class TicketsController extends Controller
{
  const MAILER_USER='correo@gmail.com';
  const MAILER_PASSWORD='password';

  public function buyTicketsAction(Request $request, $id_event)
  {
    //Create an API client
    $client = new DevTestAPIClient();

    //Get the selected events
    $event = $client->getEvent($id_event);

    if($event===false){
      return $this->render('TicketShopBundle:Tickets:error.html.twig');
    }

    //Get all event tickets from the API
    $tickets= $client->getAllEventTickets($id_event);

    if($tickets===false){
      return $this->render('TicketShopBundle:Tickets:error.html.twig');
    }

    //Create a form to obtain the user data
    $builder = $this->createFormBuilder()->add('Name',TextType::class,array('label'=>'Name:'))
    ->add('Lastname',TextType::class,array('label'=>'Lastname:'))
    ->add('DocumentId',TextType::class,array('label'=>'DocumentId:'))
    ->add('Zipcode',TextType::class,array('label'=>'Zipcode:'))
    ->add('Email',TextType::class,array('label'=>'Email:'));

    foreach ($tickets as $ticket) {
      $builder->add($ticket->id,IntegerType::class,array('label'=>$ticket->name.' Quantity: ','attr'=>array('min'=>0)));
    }

    $builder->add('Save', SubmitType::class, array('label' => 'Save'));

    $form=$builder->getForm();

    //Detect if the form was sent
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      //Get the user data
      $name = $form->get('Name')->getData();
      $lastname = $form->get('Lastname')->getData();
      $documentid = $form->get('DocumentId')->getData();
      $zipcode= $form->get('Zipcode')->getData();
      $email = $form->get('Email')->getData();

      foreach ($tickets as $ticket) {
        $order[$ticket->id] = $form->get($ticket->id)->getData();
      }

      //Place the order
      $result = $client->placeOrder($name,$lastname,$documentid,$zipcode,$order);

      if($result===false){
        return $this->render('TicketShopBundle:Tickets:error.html.twig');
      }
      //Generate the random code and saves in DB the tickets
      $em=$this->getDoctrine()->getManager();

      foreach ($result->lines as  $orderline) {
        $ticket = new Ticket();
        $ticket->setName($orderline->ticket->name);
        $ticket->setCode($this->generateCode(20));
        $ticket->setQuantity($orderline->quantity);
        $ticket->setOrdernum($result->id);
        $ticket->setOrderuuid($result->uuid);
        $ticket->setOrderlineuuid($orderline->uuid);

        $codes[$ticket->getName()]=$ticket->getCode();
        //Save the ticket
        $em->persist($ticket);
        //Execute the queries
        $em->flush();
      }

      // Create the Transport
      $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                                      ->setEncryption('tls')
                                      ->setStreamOptions(array('ssl'=>array('verify_peer'=>FALSE,'verify_peer_name'=>FALSE)))
                                      ->setUsername(self::MAILER_USER)
                                      ->setPassword(self::MAILER_PASSWORD);
      // Create the Mailer using your created Transport
       $mailer = \Swift_Mailer::newInstance($transport);

       //Create the email body
       $body= $this->renderView(
         'TicketShopBundle:Tickets:email.txt.twig',
         array('name' => $name,'lastname'=>$lastname,'zipcode'=>$zipcode,'documentid'=>$documentid,'codes'=>$codes)
       );

       //Create a message
      $message = (new \Swift_Message())
          ->setSubject('Order Confirmation')
          ->setFrom('jesotacot@gmail.com')
          ->setTo($email)
          ->setBody($body);

        //Send the message
        $mailer->send($message);

        //Show the tickets and the form
        return $this->render('TicketShopBundle:Tickets:confirmTickets.html.twig');
      }

      //Show the tickets and the form
      return $this->render('TicketShopBundle:Tickets:buyTickets.html.twig', array(
        'event'=> $event,
        'tickets'=>$tickets,
        'form'=>$form->createView()
      ));
  }
  /**
   *
   * Generate a ramdom code
   *
   * @param int $length
   */
  private function generateCode($length)
  {
    $code='';
    $chars="abcdefghijklmnopqrstuvwxyz0123456789";
    $max=strlen($chars)-1;

    for($i=0;$i < $length;$i++){
      $code.=$chars[rand(0,$max)];
    }
    return $code;
  }
}
