<?php

namespace App\Serializer;

use App\Entity\Ram;


/**
 * Class RamSerializer
 * @package App\Serializer
 */
class RamSerializer
{
    /**
     * @var Ram
     */
    protected $ram;

    /**
     * RamSerializer constructor.
     * @param Ram $ram
     */
    public function __construct(Ram $ram)
    {
        $this->ram = $ram;
    }

    /**
     * @return mixed
     */
    public function serialize()
    {
        $ram['id'] = $this->ram->getId();
        $ram['server_id'] = $this->ram->getServer()->getId();
        $ram['type'] = $this->ram->getType();
        $ram['size'] = $this->ram->getSize();
        $ram['created_at'] = $this->ram->getCreatedAt()->getTimestamp();

        return $ram;
    }
}
