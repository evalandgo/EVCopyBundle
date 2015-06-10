<?php

namespace EV\CopyBundle\Factory;

use EV\CopyBundle\Metadata\Driver\DriverInterface;

class ClonerFactory
{

    protected $driver;

    public function __construct(DriverInterface $driver) {
        $this->driver = $driver;
    }

    public function createCloner($originalObject, $params = array()) {

        $classMetadata = $this->driver->loadClassMetadata($originalObject);
        $cloner = new Cloner($originalObject, $classMetadata);
        $cloner->setParams($params);

        return $cloner;
    }

    public function copy($originalObject, $params = array()) {
        $this->createCloner($originalObject, $params)->copy();
    }

}