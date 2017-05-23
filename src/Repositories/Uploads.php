<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 23.05.17
 * Time: 08:17
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\UploadInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use \Image;

class Uploads implements UploadInterface
{
    private $thumbs = [];

    public function __construct()
    {
        $this->thumbs = config('foa.upload.thumbs', []);
    }

    public function upload(string $objectType, $file)
    {
        $extension = $file->getClientOriginalExtension();
        $mime = $file->getClientMimeType();
        $originalFilename = $file->getClientOriginalName();
        $fileName = $file->getFilename().'.'.$extension;

        $directory = 'public/' . uniqid();

        Storage::putFileAs($directory, $file, $fileName);

        $thumbs = [];
        foreach ($this->thumbs as $name => $thumbConfig) {
            $img = Image::make($file);
            $img->resize( array_get($thumbConfig, 'width', null) , array_get($thumbConfig, 'height', null), function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbPath = sys_get_temp_dir() . '/' .$name . '_' . $fileName;
            $thumbName = $name . '_' . $fileName;

            $img->save($thumbPath , 60);
            Storage::disk('local')->put($directory . '/' . $thumbName,  File::get($thumbPath));

            array_push($thumbs, $thumbName);
        }

        return foa_objects()->insert([
            'objectType' => $objectType,
            'mime' => $mime,
            'originalFilename' => $originalFilename,
            'filename' => $fileName,
            'directory' => $directory,
            'thumbs' => $thumbs
        ]);
    }

}