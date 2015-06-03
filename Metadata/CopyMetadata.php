<?php

namespace EV\CopyBundle\Metadata;


abstract class CopyMetadata {

    protected $copyType;
    protected $copyOptions;

    public function getCopyType()
    {
        return $this->copyType;
    }

    public function setCopyType($copyType)
    {
        $this->copyType = $copyType;
    }

    public function getCopyOptions()
    {
        return $this->copyOptions;
    }

    public function setCopyOptions($copyOptions)
    {
        $this->copyOptions = $copyOptions;
    }

}