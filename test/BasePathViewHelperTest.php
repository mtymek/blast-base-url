<?php

declare(strict_types=1);

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BasePathViewHelper;
use PHPUnit\Framework\TestCase;

class BasePathViewHelperTest extends TestCase
{
    public function testViewHelperProxiesToBasePathHelper(): void
    {
        $baseHelper = new BasePathHelper('/base');

        $viewHelper = new BasePathViewHelper($baseHelper);
        $result     = $viewHelper('/styles.css');

        $this->assertEquals('/base/styles.css', $result);
    }
}
