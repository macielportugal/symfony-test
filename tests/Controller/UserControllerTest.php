<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\UserFixtures;

class UserControllerTest extends WebTestCase
{
    protected $id;
    protected $client;
    protected $token;

    protected function setUp()
    {
        system('php bin/console doctrine:database:create --env=test -n');
        system('php bin/console doctrine:migrations:migrate --env=test -n');
        system('php bin/console doctrine:fixtures:load --env=test -n');
        $this->client = static::createClient();
        $this->client->request(
            'POST', 
            '/api/login_check', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username": "user1@user1.com", "password": "123456"}'
        );
        $this->token = 'Bearer ' . json_decode($this->client->getResponse()->getContent(), true)['token'];
        $this->header = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $this->token
        ];
        $this->client->request('GET', '/api/user?email=user1@user1.com');
        $content = $this->client->getResponse()->getContent();        
        if (!empty($content)) {
            $content = json_decode($content, true);
            $this->id = $content['items'][0]['id'];
            $this->email = $content['items'][0]['email'];
        }
    }

    public function testList()
    {
        $this->client->request('GET', '/api/user');
        $user_list = $this->client->getResponse()->getContent();

        if ($this->client->getResponse()->getStatusCode() === 200) {
            $user_list = json_decode($user_list, true);
            $this->id = $user_list['items'][0]['id'];
        }

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testShow($checkFirstName=NULL)
    {
        $this->client->request(
            'GET',
            '/api/user/' . $this->id,
            [],
            [],
            $this->header
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertContains($this->email, $content['email']);

        if ($checkFirstName) {
            $this->assertContains($content['firstName'], $checkFirstName);
        }
    }

    public function testEdit()
    {
        $this->client->request(
            'PUT', 
            '/api/user/' . $this->id, 
            [], 
            [], 
            $this->header,
            '{
                "email": "user1@user1.com",
                "password": "123456",
                "firstName": "Symfony",
                "lastName": "Test"
            }'
        );
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->testShow('Symfony');
    }

    public function testAdd()
    {
        $this->client->request(
            'POST', 
            '/api/user/create', 
            [], 
            [], 
            $this->header,
            '{
                "email": "symfonytest@gmail.com",
                "password": "test123",
                "firstName": "Symfony",
                "lastName": "Test"
            }'
        );
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->id = $content['id'];
    }

    public function testDestroy()
    {
        $this->testAdd();
        $this->client->request(
            'DELETE', 
            '/api/user/' . $this->id, 
            [], 
            [], 
            $this->header
        );
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request(
            'GET',
            '/api/user/' . $this->id,
            [],
            [],
            $this->header
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode()); 
    }
}
