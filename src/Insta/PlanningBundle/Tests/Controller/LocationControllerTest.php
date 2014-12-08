<?php

namespace Insta\PlanningBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LocationControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location');
    }

    public function testSitelist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/sites');
    }

    public function testRoomlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/rooms');
    }

    public function testSite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/site/{id}');
    }

    public function testRoom()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/room/{id}');
    }

    public function testAddsite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/site/new');
    }

    public function testAddroom()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/room/new');
    }

    public function testEditsite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/site/{id}/edit');
    }

    public function testEditroom()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/location/room/{id}/edit');
    }

}
