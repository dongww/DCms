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
        $filename = uniqid() . '.' . $file->guessClientExtension();
        $file->move($toPath, $filename);

        return $filename;
    }

    public function uploadFiles($files)
    {
        $fileNames = array();
        foreach ($files as $file) {
            if($file){
                $fileNames[] = $this->uploadFile($file);
            }else{
                continue;
            }

        }
        //文件名数组
        return $fileNames;
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