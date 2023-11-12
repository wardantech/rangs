<?php
// app/Services/ImageUploadService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
// use Intervention\Image\Facades\Image;
use Image;

class ImageUploadService
{
    public function uploadImages(array $files, $destinationPath, $resizeQuality = 60)
    {
        $uploadedFileNames = [];

        foreach ($files as $key => $file) {
            $uploadedFile = $this->processImage($file, $destinationPath, $resizeQuality);
            $uploadedFileNames[] = $uploadedFile;
        }

        return $uploadedFileNames;
    }

    private function processImage(UploadedFile $file, $destinationPath, $resizeQuality)
    {
        $newImage = Image::make($file);
        $name = date('m-d-Y_H-i-s') . '-' . $file->getClientOriginalName();
        $newImage->save($destinationPath . $name, $resizeQuality);

        return $name;
    }
}
