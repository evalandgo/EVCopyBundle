<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Entity extends CopyAnnotation {

    public function getType() {
        return 'entity';
    }

}