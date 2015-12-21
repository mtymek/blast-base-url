<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BasePathViewHelper;
use PHPUnit_Framework_TestCase;

class BasePathViewHelperTest extends PHPUnit_Framework_TestCase
{
    public function testViewHelperProxiesToBasePathHelper()
    {
        $baseHelper = new BasePathHelper('/base');

        $viewHelper = new BasePathViewHelper($baseHelper);
        $result = $viewHelper('/styles.css');
        $this->assertEquals('/base/styles.css', $result);
    }
}
