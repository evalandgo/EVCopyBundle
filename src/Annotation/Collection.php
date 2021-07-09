<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Collection extends CopyAnnotation {

    public function getType() {
        return 'collection';
    }

}
