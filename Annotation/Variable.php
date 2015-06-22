<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes({
 *   @Attribute("name", type = "string")
 * })
 */
class Variable extends CopyAnnotation {

    public function getType() {
        return 'variable';
    }

}
