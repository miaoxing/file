<?php

namespace MiaoxingTest\File\Service;

use Miaoxing\File\Service\File;
use Miaoxing\File\Service\FileModel;
use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\Http;

class FileTest extends BaseTestCase
{
    public function testSaveLocal()
    {
        $ret = File::saveLocal(__FILE__);

        $this->assertRetSuc($ret);
        $this->assertNotEmpty($ret['data']['url']);
    }

    public function testSaveRemote()
    {
        $http = $this->getServiceMock(Http::class, ['__invoke', 'isSuccess', 'getResponse']);
        $http->expects($this->once())
            ->method('__invoke')
            ->willReturn($http);

        $http->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $http->expects($this->once())
            ->method('getResponse')
            ->willReturn('content');

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath'
        ]);

        $file->expects($this->once())
            ->method('generatePath')
            ->with('jpg')
            ->willReturn('public/uploads/1/date/time-random.jpg');

        $attributes = [
            'ext' => 'jpg',
            'origName' => 'test.jpg',
            'size' => 7,
            'md5' => '9a0364b9e99bb480dd25e1f0284c8555',
            'type' => 1,
            'path' => 'public/uploads/1/date/time-random.jpg',
            'url' => 'http:///uploads/1/date/time-random.jpg',
        ];
        $file->expects($this->once())
            ->method('saveModel')
            ->with($attributes)
            ->willReturn(FileModel::fromArray($attributes));

        $file->http = $http;

        $ret = $file->saveRemote('https://example.com/path/to/test.jpg');

        $this->assertSame($attributes['url'], $ret['data']['url']);
    }
}
