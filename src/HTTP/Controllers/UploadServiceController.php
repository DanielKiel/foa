<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 22.05.17
 * Time: 21:01
 */

namespace Dion\Foa\HTTP\Controllers;


use App\Http\Controllers\Controller;
use Dion\Foa\Models\Object;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UploadServiceController extends Controller
{
    public function upload(Request $request)
    {
        $photos = [];

        foreach ($request->file() as $file) {
            $photo = foa_upload()->upload('Photos', $file);
            array_push($photos, $photo);
        }

        return new JsonResponse($photos, 200);
    }

    public function get($filename)
    {
        $file = foa_objects()->search('', [
            'objectType = Photos',
            'originalFilename = ' . $filename
        ], false)->first();

        if (! $file instanceof Object) {
            return new JsonResponse(['errors' => [
                'could not load file'
            ]], 404);
        }

        $file = Storage::disk('local')->get($file->filename);

        return (new Response($file, 200))
            ->header('Content-Type', $file->mime);
    }
}