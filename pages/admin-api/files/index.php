<?php

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\BaseController;
use Wei\Req;
use Wei\Upload;

return new class () extends BaseController {
    public function post(Req $req)
    {
        $ret = Upload::save([
            'name' => 'image' === $req['type'] ? '图片' : '文件',
            'exts' => 'image' === $req['type'] ? File::getAllowedImageExts() : File::getAllowedExts(),
            'dir' => File::generateDir(),
            'fileName' => File::generateFileName(),
        ]);
        if ($ret->isErr()) {
            return $ret;
        }

        return File::saveLocal($ret['file'], [
            'origName' => $ret['name'],
            'size' => $ret['size'],
        ]);
    }
};
