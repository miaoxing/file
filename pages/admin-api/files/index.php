<?php

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\BaseController;
use Wei\Req;
use Wei\Upload;

return new class () extends BaseController {
    public function post(Req $req)
    {
        $ret = Upload::save([
            'exts' => 'image' === $req['type'] ? File::getAllowedImageExts() : File::getAllowedExts(),
            'path' => File::generatePath(),
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
