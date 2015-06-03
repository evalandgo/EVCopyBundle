<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Simple extends CopyAnnotation {

    public function getType() {
        return 'simple';
    }

}
