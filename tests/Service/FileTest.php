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
                ],
            ]),
        ]);

        $ret = $file->write('public/test.jpg');
        $this->assertSame('https://test.com/test.jpg', $ret['url']);
    }

    public function testGetFileName()
    {
        $file = new File();

        $name = $file->getFileName('https://test.com/path/name.jpg?a=b');

        $this->assertSame('name.jpg', $name);
    }
}
