<?php

namespace App\Service;


use Exception;
use App\Entity\Expediente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TimeStampManager
{
    private $entityManager;
    private $client;


    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    public function stamp(Expediente $expediente)
    {
        $texto = $expediente->getTexto();
        $hash = hash('sha256', $texto);

        try {
            $response = $this->client->request('POST', 'http://66.97.38.185:3030/stamp/', [
                'json' => ["file_hash" => $hash]
            ]);
            $response->toArray();
            $expediente->setHash($hash);
            $expediente->setMarcaTemporal($response['temporary_rd']);
            $expediente->SetMarcaDefinitivo(false);
        } catch (Exception $e) {
        }
    }

    public function stampDefinitivo(Expediente $expediente)
    {
        $hash = $expediente->getHash();
        $tiempo = $expediente->getMarcaTemporal();


        try {
            $response = $this->client->request('POST', 'http://66.97.38.185:3030/verify/', [
                'json' => ["file_hash" => $hash, "rd" => $tiempo]
            ]);
            $response->toArray();
            $expediente->setMarcaTemporal($response['rd']);
            $expediente->SetMarcaDefinitivo(true);
        } catch (Exception $e) {
        }
    }
}
