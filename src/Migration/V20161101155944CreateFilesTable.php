<?php

namespace Miaoxing\File\Migration;

use Wei\Migration\BaseMigration;

class V20161101155944CreateFilesTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('files')
            ->bigId()
            ->uBigInt('app_id')
            ->string('orig_name', 64)
            ->string('path')
            ->uTinyInt('type')->comment('文件类型,1是图片,2是文档,3是音频,4是视频')
            ->string('ext', 8)->comment('扩展名')
            ->uInt('size')
            ->uSmallInt('width')
            ->uSmallInt('height')
            ->char('md5', 32)
            ->string('url')
            ->datetime('orig_created_at')
            ->timestamps()
            ->userstamps()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('files');
    }
}
