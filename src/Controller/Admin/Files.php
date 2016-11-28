<?php

namespace Miaoxing\File\Controller\Admin;

use Miaoxing\File\Service\File;

class Files extends \miaoxing\plugin\BaseController
{
    protected $controllerName = '文件管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
        'audit' => '审核',
    ];

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
                    $cn = $file['categoryId'] ? wei()->category()->findOneById($file['categoryId'])['name'] : '';
                    $data[] = $file->toArray() + [
                            'name' => $file['originalName'],
                            'categoryName' => $cn,
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
            'postMaxSize' => 20 * 1024 * 1024,
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

    public function auditAction($req)
    {
        $file = wei()->file()->curApp()->findOneById($req['id']);
        $ret = wei()->audit->audit($file, $req['pass'], $req['description']);

        return $this->ret($ret);
    }
}
