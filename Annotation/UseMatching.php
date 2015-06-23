<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes({
 *   @Attribute("name", type = "string")
 * })
 */
class UseMatching extends CopyAnnotation {

    public function getType() {
        return 'use_matching';
    }

}
