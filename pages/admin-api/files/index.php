<?php

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\BaseController;
use Wei\Req;

return new class () extends BaseController {
    public function post(Req $req)
    {
        /** @var \Wei\Upload $upload */
        $upload = wei()->upload;
        $result = $upload([
            'name' => 'image' === $req['type'] ? '图片' : '文件',
            'exts' => 'image' === $req['type'] ? File::getImageExts() : File::getAllExts(),
            'dir' => File::getUploadDir(),
            'fileName' => File::getUploadName(),
        ]);
        if (!$result) {
            return err($upload->getFirstMessage());
        }

        return File::upload($upload->getFile());
    }
};
