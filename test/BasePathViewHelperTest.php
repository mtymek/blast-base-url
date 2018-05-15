<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BasePathViewHelper;
use PHPUnit\Framework\TestCase;

class BasePathViewHelperTest extends TestCase
{
    public function testViewHelperProxiesToBasePathHelper()
    {
        $baseHelper = new BasePathHelper('/base');

        $viewHelper = new BasePathViewHelper($baseHelper);
        $result = $viewHelper('/styles.css');
        $this->assertEquals('/base/styles.css', $result);
    }
}
