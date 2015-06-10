<?php

namespace EV\CopyBundle;

use EV\CopyBundle\Helper\AccessorHelper;

use EV\CopyBundle\Metadata\ClassMetadata;
use EV\CopyBundle\Factory\ClonerFactory;

class Cloner
{

    protected $accessorHelper;
    protected $classMetadata;
    protected $clonerFactory;

    protected $originalObject;
    protected $params = array();
    protected $copyObject;

    public function __construct($originalObject, ClassMetadata $classMetadata) {
        $this->accessorHelper = new AccessorHelper();

        $this->originalObject = $originalObject;
        $this->classMetadata = $classMetadata;
    }

    public function getOriginalObject() {
        return $this->originalObject;
    }

    public function getClassMetadata() {
        return $this->classMetadata;
    }

    public function setClonerFactory(ClonerFactory $clonerFactory) {
        $this->clonerFactory = $clonerFactory;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams(array $params) {
        $this->params = $params;
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

            $this->copyObject = $this->classMetadata->getReflectionClass()->newInstanceArgs($instanceArgs);
            return true;
        }

        $this->copyObject = $this->classMetadata->getReflectionClass()->newInstance();
        return true;
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
                $getterName = $this->accessorHelper->getGetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                
                foreach($this->originalObject->$getterName() as $originalCollectionEntity) {
                    $this->copyObject->$adderName($this->clonerFactory->copy($originalCollectionEntity, $this->params));
                }
            }
            else if ( $propertyMetadata->getCopyType() === 'entity' ) {
                $getterName = $this->accessorHelper->getGetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());
                $setterName = $this->accessorHelper->getSetter($this->classMetadata->getReflectionClass(), $propertyMetadata->getReflectionProperty()->getName());

                if ( $this->originalObject->$getterName() !== null ) {
                    $this->copyObject->$setterName($this->clonerFactory->copy($this->originalObject->$getterName(), $this->params));
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