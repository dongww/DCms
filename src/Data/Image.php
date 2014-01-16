<?php
/**
 * User: dongww
 * Date: 14-1-16
 * Time: 上午9:28
 */

namespace Data;


class Image
{
    public function uploadFile($file)
    {
        $toPath = __DIR__ . '/../../web/upload/';
        $filename = time() . $file->getClientOriginalName();
        $file->move($toPath, $filename);

        return $filename;
    }

    public function uploadFiles($files)
    {
        //文件名数组
        return array();
    }

    public function getPath($fileName)
    {
        return __DIR__ . '/../../web/upload/' . $fileName;
    }

    public function getUrl($fileName)
    {
        return '/upload/' . $fileName;
    }
} 