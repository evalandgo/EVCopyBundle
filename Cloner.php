<?php

namespace EV\CopyBundle;

use EV\CopyBundle\Metadata\Driver\DriverInterface;

class Cloner
{

    protected $driver;

    public function __construct(DriverInterface $driver) {
        $this->driver = $driver;
    }

    // TODO : refactor
    public function copy($originalObject, $params = array()) {

        // get metadata
        $classMetadata = $this->driver->loadClassMetadata($originalObject);
        //var_dump($classMetadata);

        // création de la copie
        if ( $classMetadata->getConstructMethodMetadata() !== null ) {
            $instanceArgs = array();
            foreach($classMetadata->getConstructMethodMetadata()->getCopyOptions()['variables'] as $key => $param) {
                if ( isset($params[$param]) ) {
                    $instanceArgs[$param] = $params[$param];
                }
                else {
                    throw new \Exception($param.' param is expected');
                }
            }

            $copyObject = $classMetadata->getReflectionClass()->newInstanceArgs($instanceArgs);
        }
        else {
            $copyObject = $classMetadata->getReflectionClass()->newInstance();
        }

        // parcours des metadata
        foreach($classMetadata->getPropertiesMetadata() as $propertyMetadata) {
            // copie
            // TODO : vérifier si les méthodes existent
            if ( $propertyMetadata->getCopyType() === 'simple' ) {
                $getterName = 'get'.ucfirst($propertyMetadata->getReflectionProperty()->getName());
                $setterName = 'set'.ucfirst($propertyMetadata->getReflectionProperty()->getName());
                $copyObject->$setterName($originalObject->$getterName());
            }
            else if ( $propertyMetadata->getCopyType() === 'variable' ) {
                $setterName = 'set'.ucfirst($propertyMetadata->getReflectionProperty()->getName());
                $copyObject->$setterName($params[$propertyMetadata->getCopyOptions()['name']]);
            }
            else if ( $propertyMetadata->getCopyType() === 'collection' ) {
                $getterName = 'get'.ucfirst($propertyMetadata->getReflectionProperty()->getName());

                // TODO : refactor this shit
                if ( $classMetadata->getReflectionClass()->hasMethod('add'.ucfirst(substr($propertyMetadata->getReflectionProperty()->getName(), 0, -1))) ) {
                    $adderName = 'add'.ucfirst(substr($propertyMetadata->getReflectionProperty()->getName(), 0, -1));
                }
                else if ( $classMetadata->getReflectionClass()->hasMethod('add'.ucfirst($propertyMetadata->getReflectionProperty()->getName())) ) {
                    $adderName = 'add'.ucfirst($propertyMetadata->getReflectionProperty()->getName());
                }
                else {
                    throw new \Exception('Adder not found for this property : '.$propertyMetadata->getReflectionProperty()->getName());
                }

                foreach($originalObject->$getterName() as $originalCollectionEntity) {
                    $copyObject->$adderName($this->copy($originalCollectionEntity, $params));
                }
            }
            else if ( $propertyMetadata->getCopyType() === 'entity' ) {
                $getterName = 'get'.ucfirst($propertyMetadata->getReflectionProperty()->getName());
                $setterName = 'set'.ucfirst($propertyMetadata->getReflectionProperty()->getName());

                if ( $originalObject->$getterName() !== null ) {
                    $copyObject->$setterName($this->copy($originalObject->$getterName(), $params));
                }

            }
            else {
                throw new \Exception($propertyMetadata->getCopyType().' type is not expected');
            }
        }


        return $copyObject;
    }



}