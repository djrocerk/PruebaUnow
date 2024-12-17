<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class AuthControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        // Initialize the client and the EntityManager
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testRegisterSuccess(): void
    {
        // Prepare the data to be sent in the request
        $data = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        // Make the POST request to the /api/register route
        $this->client->request(
            'POST',
            '/api/register',
            [], 
            [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Assert that the response status code is 200 (successful registration)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Assert that the response contains the expected JSON data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Usuario registrado exitosamente', $responseData['message']);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('email', $responseData['user']);
        $this->assertEquals('testuser@example.com', $responseData['user']['email']);

        // Assert that the user is saved in the database
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail('testuser@example.com');
        $this->assertNotNull($user);
        $this->assertEquals('testuser@example.com', $user->getEmail());
    }

    public function testRegisterFailureMissingData(): void
    {
        $data = [
            'email' => 'testuser@example.com',
            // Missing password
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [], 
            [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Assert that the response status code is 400 (Bad Request)
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Assert that the response contains the expected error message
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Faltan datos en la solicitud', $responseData['message']);
    }

    public function testLoginSuccess(): void
    {
        // First, we need to register a user to be able to login
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword(password_hash('password123', PASSWORD_BCRYPT));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Prepare the data for login
        $data = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        // Make the POST request to the /api/login route
        $this->client->request(
            'POST',
            '/api/login',
            [], 
            [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Assert that the response status code is 200 (login successful)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Assert that the response contains a token
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('email', $responseData['user']);
        $this->assertEquals('testuser@example.com', $responseData['user']['email']);
        $this->assertArrayHasKey('token', $responseData);
    }

    public function testLoginFailureInvalidCredentials(): void
    {
        // Prepare data for login with wrong credentials
        $data = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'wrongpassword',
        ];

        $this->client->request(
            'POST',
            '/api/login',
            [], 
            [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Assert that the response status code is 401 (Unauthorized)
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Assert that the response contains the expected error message
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Credenciales inválidas', $responseData['message']);
    }

    protected function tearDown(): void
    {
        // Clean up any resources if needed (e.g., flush the database)
        parent::tearDown();
    }
}