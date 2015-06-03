<?php

namespace EV\CopyBundle\Metadata;


class PropertyMetadata extends CopyMetadata {

    protected $reflectionProperty;

    public function __construct(\ReflectionProperty $reflectionProperty, $copyType) {
        $this->reflectionProperty = $reflectionProperty;
        $this->copyType = $copyType;
    }

    public function getReflectionProperty()
    {
        return $this->reflectionProperty;
    }

    public function setReflectionProperty(\ReflectionProperty $reflectionProperty)
    {
        $this->reflectionProperty = $reflectionProperty;
    }

}