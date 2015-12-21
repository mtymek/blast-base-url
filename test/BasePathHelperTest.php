<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use PHPUnit_Framework_TestCase;

class BasePathHelperTest extends PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $helper = new BasePathHelper('/base/');
        $this->assertEquals('/base/', $helper());
        $this->assertEquals('/base/asset.css', $helper('asset.css'));
    }

    public function testSetBasePathAltersInitialPath()
    {
        $helper = new BasePathHelper();
        $helper->setBasePath('/foo');
        $this->assertEquals('/foo/', $helper());
        $this->assertEquals('/foo/asset.css', $helper('asset.css'));
    }
}
