<?php

namespace EV\CopyBundle\Factory;

use EV\CopyBundle\Metadata\Driver\DriverInterface;
use EV\CopyBundle\Cloner;

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
        $cloner->setClonerFactory($this);

        return $cloner;
    }

    public function copy($originalObject, $params = array()) {
        return $this->createCloner($originalObject, $params)->copy();
    }

}