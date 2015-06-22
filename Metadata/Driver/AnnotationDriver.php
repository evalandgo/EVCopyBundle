<?php

namespace EV\CopyBundle\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use EV\CopyBundle\Metadata\ClassMetadata;
use EV\CopyBundle\Metadata\PropertyMetadata;
use EV\CopyBundle\Metadata\MethodMetadata;

class AnnotationDriver implements DriverInterface {

    protected $reader;
    protected $annotationsPropertyClass = array(
        'EV\CopyBundle\Annotation\Simple',
        'EV\CopyBundle\Annotation\Variable',
        'EV\CopyBundle\Annotation\Collection',
        'EV\CopyBundle\Annotation\Entity'
    );

    public function __construct(Reader $reader) {
        $this->reader = $reader;
    }

    public function loadMetadataFromObject($object) {
        if ( $object instanceof \Doctrine\Common\Persistence\Proxy ) {
            return $this->loadMetadata(get_parent_class($object));
        }

        return $this->loadMetadata(get_class($object));
    }

    public function loadMetadata($class) {

        $reflectionClass = new \ReflectionClass($class);

        $classMetadata = new ClassMetadata($reflectionClass);

        // constructor
        if ($reflectionClass->hasMethod('__construct')) {
            $reflectionMethodContruct = $reflectionClass->getMethod('__construct');
            $annotation = $this->reader->getMethodAnnotation($reflectionMethodContruct, 'EV\CopyBundle\Annotation\Construct');
            if ( $annotation !== null ) {
                $constructMethodMetadata = new MethodMetadata($reflectionMethodContruct, $annotation->getType());
                $constructMethodMetadata->setCopyOptions($annotation->getOptions());

                $classMetadata->setConstructMethodMetadata($constructMethodMetadata);
            }
        }

        // properties
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {

            foreach ($this->annotationsPropertyClass as $annotationClass) {
                $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, $annotationClass);
                if ( $annotation !== null ) {
                    $propertyMetadata = new PropertyMetadata($reflectionProperty, $annotation->getType());
                    $propertyMetadata->setCopyOptions($annotation->getOptions());

                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }

        }

        return $classMetadata;

    }

}
