<?php

namespace EV\CopyBundle\Metadata\Driver;

interface DriverInterface
{
    public function loadClassMetadata($object);
}
