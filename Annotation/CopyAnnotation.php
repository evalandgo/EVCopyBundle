<?php
/**
 * Created by PhpStorm.
 * User: Micka
 * Date: 29/05/2015
 * Time: 14:38
 */

namespace EV\CopyBundle\Annotation;


abstract class CopyAnnotation {

    protected $options;

    public function __construct($options = null) {
        $this->options = $options;
    }

    public function getOptions() {
        return $this->options;
    }

    abstract public function getType();

}