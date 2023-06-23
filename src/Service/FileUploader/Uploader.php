<?php

namespace App\Service\FileUploader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader
{

    public function __construct(
        private readonly SluggerInterface      $slugger,
        private readonly ParameterBagInterface $parameterBag,
    )
    {
    }

    public function upload(UploadedFile $file, $targetDirectory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->parameterBag->get('kernel.project_dir') . "/public" . $targetDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

}