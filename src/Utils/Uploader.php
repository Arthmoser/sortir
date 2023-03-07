<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{

    public function upload(UploadedFile $file, string $directory, string $name= " ")
    {

        $newFileName = $name . "-" . uniqid() . "-" . $file->guessExtension();
        $file->move('', $newFileName);

        return $newFileName;
    }

}