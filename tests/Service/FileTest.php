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
        $http = $this->createHttpMock();

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath',
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

    public function testSaveRemoteWithQuery()
    {
        $http = $this->createHttpMock();

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath',
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

        $ret = $file->saveRemote('https://example.com/path/to/test.jpg?id=123.gif');

        $this->assertSame($attributes['url'], $ret['data']['url']);
    }

    public function testSaveRemoteWithCustomPath()
    {
        $http = $this->createHttpMock();

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath',
        ]);

        $file->expects($this->never())
            ->method('generatePath');

        $attributes = [
            'ext' => 'jpg',
            'origName' => 'test.jpg',
            'size' => 7,
            'md5' => '9a0364b9e99bb480dd25e1f0284c8555',
            'type' => FileModel::TYPE_IMAGE,
            'path' => 'public/uploads/1/custom/path.jpg',
            'url' => 'http:///uploads/1/custom/path.jpg',
        ];
        $file->expects($this->once())
            ->method('saveModel')
            ->with($attributes)
            ->willReturn(FileModel::fromArray($attributes));

        $file->http = $http;

        $ret = $file->saveRemote('https://example.com/path/to/test.jpg', [
            'path' => 'custom/path.jpg',
        ]);

        $this->assertSame($attributes['url'], $ret['data']['url']);
    }

    public function testSaveRemoteWithCustomExtension()
    {
        $http = $this->createHttpMock();

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath',
        ]);

        $file->expects($this->once())
            ->method('generatePath')
            ->with('jpeg')
            ->willReturn('public/uploads/1/date/time-random.jpeg');

        $attributes = [
            'ext' => 'jpeg',
            'origName' => 'test.jpg',
            'size' => 7,
            'md5' => '9a0364b9e99bb480dd25e1f0284c8555',
            'type' => 1,
            'path' => 'public/uploads/1/date/time-random.jpeg',
            'url' => 'http:///uploads/1/date/time-random.jpeg',
        ];
        $file->expects($this->once())
            ->method('saveModel')
            ->with($attributes)
            ->willReturn(FileModel::fromArray($attributes));

        $file->http = $http;

        $ret = $file->saveRemote('https://example.com/path/to/test.jpg', [
            'ext' => 'jpeg',
        ]);

        $this->assertSame($attributes['url'], $ret['data']['url']);
    }

    public function testSaveRemoteWithoutExtension()
    {
        $http = $this->createHttpMock();

        $file = $this->getServiceMock(File::class, [
            'saveModel',
            'generatePath',
        ]);

        $file->expects($this->once())
            ->method('generatePath')
            ->with(null)
            ->willReturn('public/uploads/1/date/time-random');

        $attributes = [
            'ext' => '',
            'origName' => 'test',
            'size' => 7,
            'md5' => '9a0364b9e99bb480dd25e1f0284c8555',
            'type' => FileModel::TYPE_OTHERS,
            'path' => 'public/uploads/1/date/time-random',
            'url' => 'http:///uploads/1/date/time-random',
        ];
        $file->expects($this->once())
            ->method('saveModel')
            ->with($attributes)
            ->willReturn(FileModel::fromArray($attributes));

        $file->http = $http;

        $ret = $file->saveRemote('https://example.com/path/to/test');

        $this->assertSame($attributes['url'], $ret['data']['url']);
    }

    protected function createHttpMock()
    {
        $http = $this->getServiceMock(Http::class, ['__invoke', 'isSuccess', 'getResponse']);

        $http->expects($this->once())
            ->method('__invoke')
            ->willReturn($http);

        $http->expects($this->any())
            ->method('isSuccess')
            ->willReturn(true);

        $http->expects($this->any())
            ->method('getResponse')
            ->willReturn('content');

        return $http;
    }
}
