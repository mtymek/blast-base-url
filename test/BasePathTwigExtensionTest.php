<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BasePathTwigExtension;
use PHPUnit\Framework\TestCase;

class BasePathTwigExtensionTest extends TestCase
{
    public function testTwigExtensionProxiesToBasePathHelper()
    {
        $baseHelper = new BasePathHelper('/base');

        $viewHelper = new BasePathTwigExtension($baseHelper);
        $result = $viewHelper->render('/styles.css');
        $this->assertEquals('/base/styles.css', $result);
    }
}
