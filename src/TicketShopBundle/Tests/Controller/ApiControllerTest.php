<?php

namespace TicketShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TicketShopBundle\Entity\Ticket;

class ApiControllerTest extends WebTestCase
{
  public function testPostInvalidMethod()
  {
    //Create a Guzzle Client
    $client = new \GuzzleHttp\Client(array('base_uri' => 'http://localhost:8000'));
    try{
      //Request with invalid method
      $response = $client->get('http://localhost:8000/api/v1/check');
    }catch(\Exception $e){
      //Test status code
      $this->assertEquals(405, $e->getResponse()->getStatusCode());
      //Test error message
      $data = json_decode($e->getResponse()->getBody(true), true);
      $this->assertArrayHasKey('error',$data);
      $this->assertArrayHasKey('code',$data['error']);
      $this->assertArrayHasKey('message',$data['error']);
      $this->assertEquals($data['error']['code'],405);
      $this->assertEquals($data['error']['message'],"Method Not Allowed");
    }
  }
  public function testPostInvalidValue()
  {
    //Invalid Code
    $code='12345';
    //Create a Guzzle Client
    $client = new \GuzzleHttp\Client(array('base_uri' => 'http://localhost:8000'));
    $form = array('code'=>$code);
    try{
      $response = $client->post('http://localhost:8000/api/v1/check', array('form_params'=>$form));
    }catch(\Exception $e){
      //Test status code
      $this->assertEquals(400, $e->getResponse()->getStatusCode());
      //Test error message
      $data = json_decode($e->getResponse()->getBody(true), true);
      $this->assertArrayHasKey('code',$data);
      $this->assertEquals($data['code'], "Error: " . $code . " Value is not valid");
    }
  }

  public function testPostNotFoundValue()
  {
    //code not found
    $code='12345123451234512345';
    $client = static::createClient();
    $em=$client->getContainer()->get('doctrine')->getManager();
    //Delete Ticket tablet
    $connection = $em->getConnection();
    $platform   = $connection->getDatabasePlatform();
    $connection->executeUpdate($platform->getTruncateTableSQL('Ticket', true));
    //Create a Guzzle Client
    $client = new \GuzzleHttp\Client(array('base_uri' => 'http://localhost:8000'));
    $form = array('code'=>$code);
    $response = $client->post('http://localhost:8000/api/v1/check', array('form_params'=>$form));
    //Test status code
    $this->assertEquals(200, $response->getStatusCode());
    //Test error message
    $data = json_decode($response->getBody(true), true);
    $this->assertArrayHasKey('code',$data);
    $this->assertEquals($data['code'], "Error: " . $code . " Value is not found");
  }
  public function testPostOkValue()
  {
    //Valid code
    $code='12345qwertasdfgzxcvb';
    $client = static::createClient();
    $em=$client->getContainer()->get('doctrine')->getManager();
    //Delete Ticket tablet
    $connection = $em->getConnection();
    $platform   = $connection->getDatabasePlatform();
    $connection->executeUpdate($platform->getTruncateTableSQL('Ticket', true));
    //Create a new ticket
    $ticket= new Ticket();
    $ticket->setName('Ticket 1');
    $ticket->setCode($code);
    $ticket->setQuantity(1);
    $ticket->setOrdernum(1);
    $ticket->setOrderuuid('55555555-4444-3333-2222-111111111111');
    $ticket->setOrderlineuuid('11111111-2222-3333-4444-555555555555');
    //Save the ticket
    $em->persist($ticket);
    //Execute the queries
    $em->flush();

    //Create a Guzzle Client
    $client = new \GuzzleHttp\Client(array('base_uri' => 'http://localhost:8000'));
    $form = array('code'=>$ticket->getCode());
    $response = $client->post('http://localhost:8000/api/v1/check', array('form_params'=>$form));
    //Test status code
    $this->assertEquals(200, $response->getStatusCode());
    //Test message
    $data = json_decode($response->getBody(true), true);
    $this->assertArrayHasKey('code',$data);
    $this->assertArrayHasKey('Ticket',$data);
    $this->assertArrayHasKey('Order Uuid',$data);
    $this->assertArrayHasKey('Orderline Uuid',$data);

    $this->assertEquals($data['code'],$code . " OK");
    $this->assertEquals($data['Ticket'],$ticket->getName());
    $this->assertEquals($data['Order Uuid'],$ticket->getOrderuuid());
    $this->assertEquals($data['Orderline Uuid'],$ticket->getOrderlineuuid());
  }
  public function testPostUsedValue()
  {
    //Used Code
    $code='12345qwertasdfgzxcvb';
    $client = static::createClient();
    $em=$client->getContainer()->get('doctrine')->getManager();
    //Delete Ticket tablet
    $connection = $em->getConnection();
    $platform   = $connection->getDatabasePlatform();
    $connection->executeUpdate($platform->getTruncateTableSQL('Ticket', true));
    //Create a new ticket
    $ticket= new Ticket();
    $ticket->setName('Ticket 1');
    $ticket->setCode($code);
    $ticket->setQuantity(1);
    $ticket->setOrdernum(1);
    $ticket->setOrderuuid('55555555-4444-3333-2222-111111111111');
    $ticket->setOrderlineuuid('11111111-2222-3333-4444-555555555555');
    $ticket->setDateofuse(new \DateTime());
    //Save the ticket
    $em->persist($ticket);
    //Execute the queries
    $em->flush();

    //Create a Guzzle Client
    $client = new \GuzzleHttp\Client(array('base_uri' => 'http://localhost:8000'));
    $form = array('code'=>$code);
    $response = $client->post('http://localhost:8000/api/v1/check', array('form_params'=>$form));
    //Test status code
    $this->assertEquals(200, $response->getStatusCode());

    //Search code in DB
    $client = static::createClient();
    $repository=$client->getContainer()->get('doctrine')->getRepository('TicketShopBundle:Ticket');
    $ticket = $repository->findOneByCode($code);

    //Test error message
    $data = json_decode($response->getBody(true), true);
    $this->assertArrayHasKey('code',$data);
    $this->assertArrayHasKey('message',$data);
    $this->assertArrayHasKey('order number',$data);
    $this->assertEquals($data['code'],"Error: " . $code . " Value is already used");
    $this->assertEquals($data['message'],"Date of use:" . $ticket->getDateofuse()->format('Y-m-d H:i:s'));
    $this->assertEquals($data['order number'],$ticket->getOrdernum());
  }
}
