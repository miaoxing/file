<?php

namespace MiaoxingTest\File\Pages\Api\Admin\Files;

use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Test\BaseTestCase;

class IndexTest extends BaseTestCase
{
    public function testGet()
    {
        $ret = Tester::postAdminApi('files');

        $this->assertRetErr($ret);

        $this->assertStringContainsString('No file uploaded or the total file size is too large', $ret['message']);
    }
}
