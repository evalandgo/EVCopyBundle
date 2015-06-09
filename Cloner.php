<?php

namespace EV\CopyBundle;

use EV\CopyBundle\Metadata\Driver\DriverInterface;
use EV\CopyBundle\Helper\AccessorHelper;

class Cloner
{

    protected $driver;
    protected $accessorHelper;
    protected $classMetadata;

    protected $originalObject;
    protected $params = array();
    protected $copyObject;

    // TODO : ajouter une foctory

    public function __construct(DriverInterface $driver, AccessorHelper $accessorHelper) {
        $this->driver = $driver;
        $this->accessorHelper = $accessorHelper;
    }

    public function getOriginalObject() {
        return $this->originalObject;
    }

    public function setOriginalObject($originalObject) {
        $this->originalObject = $originalObject;
        $this->classMetadata = $this->driver->loadClassMetadata($originalObject);
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams(array $params) {
        $this->params = $params;
    }

    public function getClassMetadata() {
        return $this->classMetadata;
    }

    protected function createCopyObject() {
        if ( $this->classMetadata->getConstructMethodMetadata() !== null ) {
            $instanceArgs = array();
            foreach($this->classMetadata->getConstructMethodMetadata()->getCopyOptions()['variables'] as $key => $param) {
                if ( isset($this->params[$param]) ) {
                    $instanceArgs[$param] = $this->params[$param];
                }
                else {
                    throw new \Exception($param.' param is expected');
                }
            }

            return $this->classMetadata->getReflectionClass()->newInstanceArgs($instanceArgs);
        }

        $this->copyObject = $this->classMetadata->getReflectionClass()->newInstance();
    }

    protected function copyProperties() {
        foreach($this->classMetadata->getPropertiesMetadata() as $propertyMetadata) {
            if ( $propertyMetadata->getCopyType() === 'simple' ) {
                $getterName = $this->accessorHelper->getGetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                $setterName = $this->accessorHelper->getSetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                $this->copyObject->$setterName($this->originalObject->$getterName());
            }
            else if ( $propertyMetadata->getCopyType() === 'variable' ) {
                $setterName = $this->accessorHelper->getSetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                $this->copyObject->$setterName($this->params[$propertyMetadata->getCopyOptions()['name']]);
            }
            else if ( $propertyMetadata->getCopyType() === 'collection' ) {
                $adderName = $this->accessorHelper->getAdder($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());

                foreach($this->originalObject->$getterName() as $originalCollectionEntity) {
                    $this->copyObject->$adderName($this->copy($originalCollectionEntity, $this->params));
                }
            }
            else if ( $propertyMetadata->getCopyType() === 'entity' ) {
                $getterName = $this->accessorHelper->getGetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                $setterName = $this->accessorHelper->getSetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());

                if ( $this->originalObject->$getterName() !== null ) {
                    $this->copyObject->$setterName($this->copy($this->originalObject->$getterName(), $params));
                }
            }
            else {
                throw new \Exception($propertyMetadata->getCopyType().' type is not expected');
            }
        }
    }

    public function copy() {

        $this->createCopyObject();

        $this->copyProperties();

        return $this->copyObject;
    }

}