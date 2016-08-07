<?php

namespace plugins\file\controllers\admin;

use plugins\category\controllers\admin\Category;

class FileCategories extends Category
{
    protected $controllerName = '文件栏目管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
    ];
}
