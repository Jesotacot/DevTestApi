<?php

namespace TicketShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TicketShopBundle\Utils\DevTestAPIClient;

class DevTestAPIClientTest extends WebTestCase
{
  public function testCreateClient()
  {
      $client = new DevTestAPIClient();
      //Test Auth Key Value
      $this->assertEquals(DevTestAPIClient::DEFAULT_API_KEY, $client->getAuthKey());
      //Test Guzzle Client
      $this->assertInstanceOf(\GuzzleHttp\Client::class, $client->getGuzzleClient());
      $client = new DevTestAPIClient('12345');
      $this->assertEquals('12345', $client->getAuthKey());
  }


  public function testGetTokens()
  {
      $client = new DevTestAPIClient();
      $tokens=$client->getTokens();

      //Test if the tokens has the expected attributes
      $this->assertObjectHasAttribute('access_token',$tokens);
      $this->assertObjectHasAttribute('refresh_token',$tokens);
      //Test Access Token and Refresh Token values
      $this->assertEquals($tokens->access_token,$client->getAccessToken());
      $this->assertEquals($tokens->refresh_token,$client->getRefreshToken());

      $client = new DevTestAPIClient('12345');
      $tokens=$client->getTokens();
      $this->assertEquals($tokens,false);
  }

  public function  testGetAllEvents()
  {
    $client = new DevTestAPIClient();
    $events=$client->getAllEvents();

    //Test if the events has the expected attributes
    foreach($events as $event){
        $this->assertObjectHasAttribute('id',$event);
        $this->assertObjectHasAttribute('name',$event);
        $this->assertObjectHasAttribute('date',$event);
    }

    //Cause an exception with an incorrect api_key
    $client = new DevTestAPIClient('12345');
    $events=$client->getAllEvents();
    $this->assertEquals($events,false);
  }

  public function  testGetEvent()
  {
    $client = new DevTestAPIClient();
    $event=$client->getEvent(1);

    //Test if the event has the expected attributes
    $this->assertObjectHasAttribute('id',$event);
    $this->assertObjectHasAttribute('name',$event);
    $this->assertObjectHasAttribute('date',$event);

    //Cause an exception with an incorrect id
    $event=$client->getEvent(11);
    $this->assertEquals($event,false);

    //Cause an exception with an incorrect id
    $event=$client->getEvent('a');
    $this->assertEquals($event,false);

    //Cause an exception with an incorrect api_key
    $client = new DevTestAPIClient('12345');
    $event=$client->getEvent(1);
    $this->assertEquals($event,false);
  }

  public function testGetAllEventTickets(){

    $client = new DevTestAPIClient();
    $tickets=$client->getAllEventTickets(1);

    //Test if the tickets has the expected attributes
    foreach($tickets as $ticket){
        $this->assertObjectHasAttribute('id',$ticket);
        $this->assertObjectHasAttribute('name',$ticket);
        $this->assertObjectHasAttribute('description',$ticket);
        $this->assertObjectHasAttribute('price',$ticket);
    }

    //Cause an exception with an incorrect id
    $tickets=$client->getAllEventTickets(11);
    $this->assertEquals($tickets,false);

    //Cause an exception with an incorrect id
    $tickets=$client->getAllEventTickets('a');
    $this->assertEquals($tickets,false);

    //Cause an exception with an incorrect api_key
    $client = new DevTestAPIClient('12345');
    $tickets=$client->getAllEventTickets(1);
    $this->assertEquals($tickets,false);
  }

  public function testGetAllOrders(){

    $client = new DevTestAPIClient();
    $orders=$client->getAllOrders();

    //Test if the orders has the expected attributes
    foreach($orders as $order){
      $this->assertObjectHasAttribute('id',$order);
      $this->assertObjectHasAttribute('uuid',$order);
      $this->assertObjectHasAttribute('name',$order);
      $this->assertObjectHasAttribute('lastname',$order);
      $this->assertObjectHasAttribute('document_id',$order);
      $this->assertObjectHasAttribute('zipcode',$order);
      $this->assertObjectHasAttribute('lastname',$order);
      foreach ($order->lines as $orderline ) {
        $this->assertObjectHasAttribute('id',$orderline);
        $this->assertObjectHasAttribute('uuid',$orderline);
        $this->assertObjectHasAttribute('ticket',$orderline);
        $this->assertObjectHasAttribute('id',$orderline->ticket);
        $this->assertObjectHasAttribute('name',$orderline->ticket);
        $this->assertObjectHasAttribute('description',$orderline->ticket);
        $this->assertObjectHasAttribute('price',$orderline->ticket);
        $this->assertObjectHasAttribute('quantity',$orderline);
      }
      $this->assertObjectHasAttribute('created_at',$order);
    }

    //Cause an exception with an incorrect api_key
    $client = new DevTestAPIClient('12345');
    $orders=$client->getAllOrders();
    $this->assertEquals($orders,false);
  }

  public function testPlaceOrder(){
    $client = new DevTestAPIClient();
    $order=$client->placeOrder('Juan','Garcia','98765432W','28001',array('21'=>1,'22'=>2));

    //Test if the order has the expected attributes
    $this->assertObjectHasAttribute('id',$order);
    $this->assertObjectHasAttribute('uuid',$order);
    $this->assertObjectHasAttribute('name',$order);
    $this->assertObjectHasAttribute('lastname',$order);
    $this->assertObjectHasAttribute('document_id',$order);
    $this->assertObjectHasAttribute('zipcode',$order);
    $this->assertObjectHasAttribute('lastname',$order);
    foreach ($order->lines as $orderline ) {
      $this->assertObjectHasAttribute('id',$orderline);
      $this->assertObjectHasAttribute('uuid',$orderline);
      $this->assertObjectHasAttribute('ticket',$orderline);
      $this->assertObjectHasAttribute('id',$orderline->ticket);
      $this->assertObjectHasAttribute('name',$orderline->ticket);
      $this->assertObjectHasAttribute('description',$orderline->ticket);
      $this->assertObjectHasAttribute('price',$orderline->ticket);
      $this->assertObjectHasAttribute('quantity',$orderline);
    }
    $this->assertObjectHasAttribute('created_at',$order);

    //Cause an exception with an incorrect api_key
    $client = new DevTestAPIClient('12345');
    $order=$client->getAllOrders();
    $this->assertEquals($order,false);
  }
  
}
