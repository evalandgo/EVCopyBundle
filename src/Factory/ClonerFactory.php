<?php

namespace EV\CopyBundle\Factory;

use EV\CopyBundle\Metadata\Driver\DriverInterface;
use EV\CopyBundle\Memory\MatchingMemory;
use EV\CopyBundle\Cloner;

class ClonerFactory
{

    protected $driver;
    protected $matchingMemory;

    public function __construct(DriverInterface $driver, MatchingMemory $matchingMemory) {
        $this->driver = $driver;
        $this->matchingMemory = $matchingMemory;
    }

    protected function createCloner($originalObject, $params = array()) {

        $classMetadata = $this->driver->loadMetadataFromObject($originalObject);

        $cloner = new Cloner($originalObject, $classMetadata);
        $cloner->setParams($params);
        $cloner->setMatchingMemory($this->matchingMemory);
        $cloner->setClonerFactory($this);

        return $cloner;
    }

    /**
     * @param object $originalObject
     * @param array $params
     * @return object
     */
    public function copy($originalObject, $params = array()) {
        $copy = $this->createCloner($originalObject, $params)->copy();

        $this->matchingMemory->clear();

        return $copy;
    }

    /**
     * Method used by the Cloner
     *
     * @param object $originalObject
     * @param array $params
     * @return object
     */
    public function recursiveCopy($originalObject, $params = array()) {
        return $this->createCloner($originalObject, $params)->copy();
    }

}
