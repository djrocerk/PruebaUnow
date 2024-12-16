<?php
// tests/Controller/EmpleadoControllerTest.php
namespace App\Tests\Controller;

use App\Entity\Empleado;
use App\Entity\User;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EmpleadoControllerTest extends WebTestCase
{
    private $entityManager;
    private $tokenStorage;
    private $jwtManager;
    private $emailService;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->jwtManager = $this->createMock('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface');
        $this->emailService = $this->createMock(EmailService::class);

        $this->user = new User();
        $this->user->setEmail('testuser@example.com');
        $this->user->setPassword('securepassword');
    }

    public function testCreateEmpleadoSuccess()
    {
        $this->tokenStorage->method('getToken')->willReturn($this->createMock(TokenInterface::class));

        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/empleados/create', [], [], [], json_encode([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1980-01-01',
            'puesto_trabajo' => 'Desarrollador',
            'email' => 'juan.perez@example.com'
        ]));

        $this->entityManager->method('getRepository')->willReturn($this->createMock('Doctrine\ORM\EntityRepository'));

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->emailService->expects($this->once())
            ->method('sendWelcomeEmail')
            ->with('juan.perez@example.com', 'Juan', 'Pérez');

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_CREATED);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('Empleado creado exitosamente, hemos enviado un correo de bienvenida', $responseContent);
    }

    public function testCreateEmpleadoUnauthorized()
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/empleados/create', [], [], [], json_encode([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1980-01-01',
            'puesto_trabajo' => 'Desarrollador',
            'email' => 'juan.perez@example.com'
        ]));

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('No autorizado', $responseContent);
    }

    public function testCreateEmpleadoMissingData()
    {
        $this->tokenStorage->method('getToken')->willReturn($this->createMock(TokenInterface::class));

        $client = static::createClient();
        $client->request(Request::METHOD_POST, '/api/empleados/create', [], [], [], json_encode([
            'nombre' => 'Juan',
            'apellido' => 'Pérez'
        ]));

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('Faltan datos en la solicitud', $responseContent);
    }

    public function testUpdateEmpleadoSuccess()
    {
        $this->tokenStorage->method('getToken')->willReturn($this->createMock(TokenInterface::class));

        $empleado = new Empleado();
        $empleado->setNombre('Juan');
        $empleado->setApellido('Pérez');
        $empleado->setFechaNacimiento(new \DateTime('1980-01-01'));
        $empleado->setPuestoTrabajo('Desarrollador');
        $empleado->setEmail('juan.perez@example.com');

        $this->entityManager->method('getRepository')->willReturn($this->createMock('Doctrine\ORM\EntityRepository'));
        $this->entityManager->getRepository(Empleado::class)->method('find')->willReturn($empleado);

        $client = static::createClient();
        $client->request(Request::METHOD_PUT, '/api/empleados/update/1', [], [], [], json_encode([
            'nombre' => 'Juan Carlos',
            'apellido' => 'Pérez'
        ]));

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('Empleado actualizado exitosamente', $responseContent);
    }

    public function testUpdateEmpleadoUnauthorized()
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $client = static::createClient();
        $client->request(Request::METHOD_PUT, '/api/empleados/update/1', [], [], [], json_encode([
            'nombre' => 'Juan Carlos',
            'apellido' => 'Pérez'
        ]));

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('No autorizado', $responseContent);
    }

    // Test for successfully deleting an employee
    public function testDeleteEmpleadoSuccess()
    {
        $this->tokenStorage->method('getToken')->willReturn($this->createMock(TokenInterface::class));

        $empleado = new Empleado();
        $empleado->setId(1);
        $empleado->setNombre('Juan');
        $empleado->setApellido('Pérez');
        $empleado->setFechaNacimiento(new \DateTime('1980-01-01'));
        $empleado->setPuestoTrabajo('Desarrollador');
        $empleado->setEmail('juan.perez@example.com');

        $this->entityManager->method('getRepository')->willReturn($this->createMock('Doctrine\ORM\EntityRepository'));
        $this->entityManager->getRepository(Empleado::class)->method('find')->willReturn($empleado);

        $client = static::createClient();
        $client->request(Request::METHOD_DELETE, '/api/empleados/delete/1');

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('Empleado eliminado exitosamente', $responseContent);
    }

    public function testDeleteEmpleadoUnauthorized()
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $client = static::createClient();
        $client->request(Request::METHOD_DELETE, '/api/empleados/delete/1');

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringContainsString('No autorizado', $responseContent);
    }

    public function testListEmpleadosSuccess()
    {
        $this->tokenStorage->method('getToken')->willReturn($this->createMock(TokenInterface::class));

        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/api/empleados/list');

        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
    }
}
