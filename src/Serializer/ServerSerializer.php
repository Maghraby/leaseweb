<?php

namespace App\Serializer;

use App\Entity\Server;


/**
 * Class ServerSerializer
 * @package App\Serializer
 */
class ServerSerializer
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * ServerSerializer constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return mixed
     */
    public function serialize()
    {
        $server['id'] = $this->server->getId();
        $server['asset_id'] = $this->server->getAssetId();
        $server['name'] = $this->server->getName();
        $server['brand'] = $this->server->getBrand();
        $server['price'] = $this->server->getPrice();
        $server['created_at'] = $this->server->getCreatedAt()->getTimestamp();
        $server['rams_count'] = $this->server->getRams()->count();

        return $server;
    }
}
