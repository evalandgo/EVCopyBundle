<?php

namespace EV\CopyBundle\Metadata;


class MethodMetadata extends CopyMetadata {

    protected $reflectionMethod;

    public function __construct(\ReflectionMethod $reflectionMethod, $copyType) {
        $this->reflectionMethod = $reflectionMethod;
        $this->copyType = $copyType;
    }

    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }

    public function setReflectionMethod(\ReflectionMethod $reflectionMethod)
    {
        $this->reflectionMethod = $reflectionMethod;
    }

}
