<?php

namespace EV\CopyBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 * @Attributes({
 *   @Attribute("variables", type = "array")
 * })
 */
class Construct extends CopyAnnotation {

    public function getType() {
        return 'construct';
    }

}
