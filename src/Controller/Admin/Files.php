<?php

namespace Miaoxing\File\Controller\Admin;

use Miaoxing\File\Service\File;

class Files extends \Miaoxing\Plugin\BaseController
{
    protected $controllerName = '文件管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy,delete' => '删除',
        'audit' => '审核',
        'imageUpload' => '图片上传',
    ];

    protected $hidePermission = true;

    protected $exts = ['txt', 'xml', 'pdf', 'zip', 'doc', 'ppt', 'docx', 'pptx', 'xls', 'xlsx'];

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $files = wei()->file()->curApp()->andWhere(['type' => File::TYPE_FILE]);

                // 分页
                $files->limit($req['rows'])->page($req['page']);

                // 排序
                $files->desc('id');

                // 搜索
                if ($req['search']) {
                    $files->andWhere('originalName LIKE ?', '%' . $req['search'] . '%');
                }

                if ($req['categoryId']) {
                    $files->andWhere('categoryId=?', $req['categoryId']);
                }

                wei()->event->trigger('beforeFileFind', [$files, $req]);

                $data = [];
                foreach ($files->findAll() as $file) {
                    $category = $file->getCategory();
                    $data[] = $file->toArray() + [
                            'name' => $file['originalName'],
                            'categoryName' => $category ? $category['name'] : '',
                        ];
                }

                return $this->suc([
                    'message' => '读取列表成功',
                    'data' => $data,
                    'page' => $req['page'],
                    'rows' => $req['rows'],
                    'records' => $files->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function createAction($req)
    {
        $upload = wei()->upload;
        $result = $upload([
            'name' => '文件',
            'exts' => $this->exts,
            'dir' => wei()->upload->getDir() . '/files/' . date('Ymd'),
        ]);

        if (!$result) {
            return $this->err($upload->getFirstMessage());
        }

        $req['file'] = $upload->getFile();
        $ret = wei()->file->upload($req['file']);
        if ($ret['fileId']) {
            $file = wei()->file()->curApp()->findOneById($ret['fileId']);
            $file->save([
                'categoryId' => $req['categoryId'],
                'startTime' => $req['startTime'],
                'endTime' => $req['endTime'],
                'type' => File::TYPE_FILE,
            ]);
        }

        return $this->ret($ret);
    }

    public function editAction($req)
    {
        $file = wei()->file()->findId($req['id']);
        $file['file'] = $file['path'];

        return get_defined_vars();
    }

    public function updateAction($req)
    {
        wei()->file()->curApp()->findId($req['id'])->save([
            'categoryId' => (int) $req['categoryId'],
            'startTime' => (string) $req['startTime'],
            'endTime' => (string) $req['endTime'],
        ]);

        return $this->suc();
    }

    public function destroyAction($req)
    {
        wei()->file()->findOneById($req['id'])->destroy();

        return $this->suc();
    }

    public function deleteAction($req)
    {
        return $this->suc();
    }

    public function auditAction($req)
    {
        $file = wei()->file()->curApp()->findOneById($req['id']);
        $ret = wei()->audit->audit($file, $req['pass'], $req['description']);

        return $this->ret($ret);
    }

    /**
     * 图片上传返回链接
     * @param $req
     * @return array
     */
    public function imageUploadAction($req)
    {
        $upload = wei()->upload;
        $dir = wei()->upload->getDir() . '/' . $this->app->getId() . '/' . date('Ymd');
        $result = $upload([
            'name' => '图片',
            'exts' => ['gif', 'png', 'jpg', 'jpeg', 'bmp'],
            'dir' => $dir,
            'fileName' => time() . rand(1, 10000),
        ]);

        if (!$result) {
            return $this->err($upload->getFirstMessage());
        }

        // TODO 由upload服务处理
        // 允许其他用户访问,如nginx用户
        chmod($dir, 0777);

        $req['file'] = $upload->getFile();
        $ret = wei()->file->upload($req['file']);
        if ($ret['fileId']) {
            $file = wei()->file()->curApp()->findOneById($ret['fileId']);
            $file->save([
                'type' => File::TYPE_IMAGE,
            ]);
        }

        return $this->ret($ret);
    }

    public function videoUploadAction()
    {
        // 1. 上传到服务器
        $upload = wei()->upload;
        $result = $upload([
            'name' => '视频',
            'exts' => ['mp4', 'mov'],
            'fileName' => date('YmdHis'),
            'dir' => wei()->upload->getDir() . '/videos/' . date('Ymd'),
        ]);
        if (!$result) {
            return $this->err($upload->getFirstMessage());
        }

        // 2. 保存上传信息
        $req['file'] = $upload->getFile();
        $ret = wei()->file->upload($req['file']);

        return $ret;
    }
}
