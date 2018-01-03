<?php

namespace MiaoxingTest\File\Service;

class CdnTest extends \Miaoxing\Plugin\Test\BaseTestCase
{
    public function testUploadImagesFromHtmlWithEmptyHtml()
    {
        $this->assertEmpty(wei()->cdn->uploadImagesFromHtml(''));
    }

    public function testUploadImagesFromHtmlWithSpaceSrc()
    {
        $logger = $this->getServiceMock('logger', ['info']);

        $logger->expects($this->once())
           ->method('info')
            ->with('Empty url: <img src=" ">');

        $html = wei()->cdn->uploadImagesFromHtml('<p><img src=" "></p>');
        $this->assertEquals('<p><img src=" "></p>', $html);
    }

    public function testUploadImagesFromHtmlWithInvalidExt()
    {
        unset(wei()->cdn->logger);

        $logger = $this->getServiceMock('logger', ['info']);

        $logger->expects($this->once())
            ->method('info')
            ->with('Ignore invalid image extension', ['url' => 'a.php']);

        $html = wei()->cdn->uploadImagesFromHtml('<p><img src="a.php"></p>');
        $this->assertEquals('<p><img src="a.php"></p>', $html);
    }

    public function testUploadImagesFromHtmlWithValidHosts()
    {
        wei()->cdn->setOption('hosts', [
            'test.com',
        ]);

        $html = wei()->cdn->uploadImagesFromHtml('<p><img src="http://test.com/a.jpg"></p>');
        $this->assertEquals('<p><img src="http://test.com/a.jpg"></p>', $html);
    }

    public function testUploadImagesFromHtmlWithCdnHost()
    {
        unset(wei()->cdn->logger);

        $logger = $this->getServiceMock('logger', ['info']);

        $logger->expects($this->once())
            ->method('info')
            ->with('Replace content image', [
                'from' => 'a.jpg',
                'to' => 'http://test.com/a.jpg',
            ]);

        wei()->cdn->setOption('cdnHost', 'http://test.com/');

        $html = wei()->cdn->uploadImagesFromHtml('<p><img src="a.jpg"></p>');
        $this->assertEquals('<p><img src="http://test.com/a.jpg"></p>', $html);
    }

    public function testUploadImagesFromHtmlWithDownload()
    {
        $cdn = $this->getServiceMock('cdn', ['upload']);

        $cdn->expects($this->once())
            ->method('upload')
            ->with('http://example.com/example.jpg')
            ->willReturn('http://test.com/test/test.jpg');

        $html = $cdn->uploadImagesFromHtml('<p><img src="http://example.com/example.jpg"></p>');

        $this->assertEquals('<p><img src="http://test.com/test/test.jpg"></p>', $html);
    }
}
