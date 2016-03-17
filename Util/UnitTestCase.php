<?php

namespace CPASimUSante\SimupollBundle\Util;

abstract class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mock($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
