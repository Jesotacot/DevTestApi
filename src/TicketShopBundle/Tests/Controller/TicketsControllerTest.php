<?php

namespace TicketShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketsControllerTest extends WebTestCase
{
  public function testbuyTickets()
  {
      $client = static::createClient();
      $client->request('GET', '/tickets/buy/1');
      $this->assertEquals(200, $client->getResponse()->getStatusCode());
      $this->assertContains('Ticket',$client->getResponse()->getContent());
  }
  public function testForm()
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/tickets/buy/1');
    //Set some values
    $form = $crawler->selectButton('Save')->form();
    $form['form[Name]']='Juan';
    //Submit the form
    $crawler = $client->submit($form);
    //Assert that the response is a redirect to /tickets/buy/6
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }
}
