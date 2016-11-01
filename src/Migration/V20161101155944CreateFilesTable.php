<?php

namespace Miaoxing\File\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161101155944CreateFilesTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->scheme->table('files')
            ->id()
            ->int('appId')
            ->int('categoryId')
            ->string('originalName', 64)
            ->string('path')
            ->tinyInt('type', 1)->comment('文件类型,1是图片,2是文件,3是音频,4是视频')
            ->string('ext', 8)->comment('扩展名')
            ->int('size')
            ->smallInt('width')
            ->smallInt('height')
            ->char('md5', 32)
            ->string('url')
            ->int('createUser')
            ->int('updateUser')
            ->datetime('startTime')
            ->datetime('endTime')
            ->bool('passed')
            ->bool('audit')
            ->timestamps()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->scheme->dropIfExists('files');
    }
}
