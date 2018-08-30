<?php

namespace TicketShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventsControllerTest extends WebTestCase
{
  public function testSelectEvents()
  {
      $client = static::createClient();
      $client->request('GET', '/events/select');
      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('Events',$client->getResponse()->getContent());
  }
  public function testForm()
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/events/select');
    //Set some values
    $form = $crawler->selectButton('Save')->form();
    $form['form[Events]']=6;
    //Submit the form
    $crawler = $client->submit($form);
    //Assert that the response is a redirect to /tickets/buy/6
    $this->assertEquals(302, $client->getResponse()->getStatusCode());
    $this->assertTrue($client->getResponse()->isRedirect('/tickets/buy/6'));
  }
}
