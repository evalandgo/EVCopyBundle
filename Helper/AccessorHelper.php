<?php

namespace EV\CopyBundle\Helper;

class AccessorHelper {

    public function __construct() {

    }

    public function getGetter(\ReflectionClass $reflectionClass, $propertyName) {

        $getterName = 'get'.ucfirst($propertyName);

        if ( $reflectionClass->hasMethod($getterName) ) {
            return $getterName;
        }

        throw new \Exception('Getter not found for this property : '.$propertyName);

    }

    public function getSetter(\ReflectionClass $reflectionClass, $propertyName) {

        $setterName = 'set'.ucfirst($propertyName);

        if ( $reflectionClass->hasMethod($setterName) ) {
            return $setterName;
        }

        throw new \Exception('Setter not found for this property : '.$propertyName);

    }

    public function getAdder(\ReflectionClass $reflectionClass, $propertyName) {

        if ( $reflectionClass->hasMethod('add'.ucfirst(substr($propertyName, 0, -1))) ) {
            return 'add'.ucfirst(substr($propertyName, 0, -1));
        }
        else if ( $reflectionClass->hasMethod('add'.ucfirst($propertyName)) ) {
            return 'add'.ucfirst($propertyName);
        }

        throw new \Exception('Adder not found for this property : '.$propertyName);

    }

}
