<?php

namespace UtilBundle\Services;


use Predis\Client;

class NotificationsManager
{
    /**
     * @var string $host
     */
    protected $host;

    /**
     * @var integer $port
     */
    protected $port;

    /**
     * @var string $scheme
     */
    protected $scheme;

    /**
     * @var Client $connection
     */
    protected $connection;

    public function __construct($host, $port, $scheme)
    {
        $this->host = $host;
        $this->port = $port;
        $this->scheme = $scheme;
    }

    /**
     * @return Client
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = new Client([
                'scheme' => $this->scheme,
                'host'   => $this->host,
                'port'   => $this->port,
            ]);
        }
        return $this->connection;
    }

    public function notify($type, $data = null)
    {
        if ($data === null) {
            $data = $type;
            $type = 'default';
        }

        $conn = $this->getConnection();
        $conn->publish('message', json_encode(array(
            'type' => $type,
            'data' => $data,
        )));
    }



}