<?php

namespace MiaoxingTest\File\Service;

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\Test\BaseTestCase;

class FileTest extends BaseTestCase
{
    public function testSaveLocal()
    {
        $ret = File::saveLocal(__FILE__);

        $this->assertRetSuc($ret);
        $this->assertNotEmpty($ret['data']['url']);
    }
}
