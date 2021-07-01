<?php

namespace MiaoxingTest\File\Service;

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Req;

class FileTest extends BaseTestCase
{
    public function testWriteRetContainsFullUrl()
    {
        $file = new File([
            'req' => new Req([
                'fromGlobal' => false,
                'servers' => [
                    'HTTPS' => 'on',
                    'HTTP_HOST' => 'test.com',
                ]
            ])
        ]);

        $ret = $file->write('public/test.jpg');
        $this->assertSame('https://test.com/test.jpg', $ret['url']);
    }
}
