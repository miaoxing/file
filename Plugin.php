<?php

namespace miaoxing\file;

class Plugin extends \miaoxing\plugin\BasePlugin
{
    protected $name = '文件管理';

    protected $description = '提供文件上传,下载,入库等功能';

    public function onAdminNavGetNavs(&$navs, &$categories, &$subCategories)
    {
        if(wei()->app->getNamespace() == 'plst') {
            $subCategories['app-file'] = [
                'parentId' => 'app',
                'name' => '文件管理',
                'icon' => 'fa fa-file',
                'sort' => 1000,
            ];

            $navs[] = [
                'parentId' => 'app-file',
                'url' => 'admin/files',
                'name' => '文件管理',
            ];
        }
    }

    /**
     * 保存,将图片地址转换为虚拟地址
     *
     * @param string|array $image
     */
    public function onPreImageSave(&$image)
    {
        $image = wei()->cdn->convertToVirtualUrl($image);
    }

    /**
     * 保存前,将数据中多个字段的图片地址转换为虚拟地址
     *
     * @param array $data
     * @param array $keys
     */
    public function onPreImageDataSave(&$data, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = wei()->cdn->convertToVirtualUrl($data[$key]);
            }
        }
    }

    /**
     * 将图片地址转换为真实地址
     *
     * @param string|array $image
     */
    public function onPostImageLoad(&$image)
    {
        $image = wei()->cdn->convertToRealUrl($image);
    }

    /**
     * 将数据中多个字段的图片地址转换为真实地址
     *
     * @param array $data
     * @param array $keys
     */
    public function onPostImageDataLoad(&$data, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = wei()->cdn->convertToRealUrl($data[$key]);
            }
        }
    }
}
