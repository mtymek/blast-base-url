<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use PHPUnit\Framework\TestCase;

class BasePathHelperTest extends TestCase
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
