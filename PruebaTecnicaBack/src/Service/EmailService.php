<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendWelcomeEmail(string $email, string $nombre, string $apellido): bool
    {
        try {
            $subject = "¡Bienvenido al sistema!";
            $message = "Hola {$nombre} {$apellido},\n\nBienvenido(a) a nuestro sistema de gestion de empleados.";  

            $response = $this->client->request('POST', 'http://127.0.0.1:8000/send-email', [
                'json' => [
                    'recipient' => $email,  
                    'subject' => $subject,
                    'message' => $message,
                    'nombre' => $nombre,   
                    'apellido' => $apellido 
                ]
            ]);

           
            if ($response->getStatusCode() === 200) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            
            return false;
        }
    }
}
