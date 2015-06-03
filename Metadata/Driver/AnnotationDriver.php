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

    public function loadClassMetadata($object) {

        $reflectionObject = new \ReflectionObject($object);

        $classMetadata = new ClassMetadata($reflectionObject);

        // constructor
        if ($reflectionObject->hasMethod('__construct')) {
            $reflectionMethodContruct = $reflectionObject->getMethod('__construct');
            $annotation = $this->reader->getMethodAnnotation($reflectionMethodContruct, 'EV\CopyBundle\Annotation\Construct');
            if ( $annotation !== null ) {
                $constructMethodMetadata = new MethodMetadata($reflectionMethodContruct, $annotation->getType());
                $constructMethodMetadata->setCopyOptions($annotation->getOptions());

                $classMetadata->setConstructMethodMetadata($constructMethodMetadata);
            }
        }

        // properties
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {

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