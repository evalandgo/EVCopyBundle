<?php

namespace EV\CopyBundle\Metadata;

class ClassMetadata extends CopyMetadata {

    protected $reflectionClass;
    protected $constructMethodMetadata;
    protected $propertiesMetadata = array();
    protected $methodsMetadata = array();

    public function __construct(\ReflectionClass $reflectionClass) {
        $this->reflectionClass = $reflectionClass;
    }

    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    public function setReflectionClass(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    public function getConstructMethodMetadata()
    {
        return $this->constructMethodMetadata;
    }

    public function setConstructMethodMetadata(MethodMetadata $constructMethodMetadata)
    {
        $this->constructMethodMetadata = $constructMethodMetadata;
    }

    public function getPropertiesMetadata()
    {
        return $this->propertiesMetadata;
    }

    public function addPropertyMetadata(PropertyMetadata $propertyMetadata)
    {
        $this->propertiesMetadata[] = $propertyMetadata;
    }

    public function getMethodsMetadata()
    {
        return $this->methodsMetadata;
    }

    public function addMethodMetadata(MethodMetadata $methodMetadata)
    {
        $this->methodsMetadata[] = $methodMetadata;
    }

}