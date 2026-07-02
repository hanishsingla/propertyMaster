<?php

namespace App\Service;

use App\Entity\Property\Property;
use App\Entity\Property\PropertyImage;
use App\Repository\Property\PropertyImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PropertyImageService
{
    private const MAX_IMAGES = 15;
    private const MAX_BYTES = 5 * 1024 * 1024;
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Uploader $uploader,
        private readonly PropertyImageRepository $imageRepository,
    ) {
    }

    /**
     * @param UploadedFile[] $files
     */
    public function addImages(Property $property, array $files): Property
    {
        if ([] === $files) {
            throw new BadRequestHttpException('No files were uploaded.');
        }

        $existing = $property->getImages()->count();
        if ($existing + count($files) > self::MAX_IMAGES) {
            throw new BadRequestHttpException(sprintf('A property may have at most %d images.', self::MAX_IMAGES));
        }

        $order = $existing;
        foreach ($files as $file) {
            $this->assertValidFile($file);
            $filename = $this->uploader->upload($file, '/uploads/properties/'.$property->getId());

            $image = (new PropertyImage())
                ->setPath('uploads/properties/'.$property->getId().'/'.$filename)
                ->setSortOrder($order)
                ->setIsCover(0 === $order && 0 === $existing);
            $property->addImage($image);
            ++$order;
        }

        $this->em->flush();
        $this->ensureSingleCover($property);

        return $property;
    }

    /**
     * @param array<int,array{imageId?:string,sortOrder?:int,isCover?:bool}> $ops untrusted JSON
     */
    public function reorder(Property $property, array $ops): Property
    {
        $byId = [];
        foreach ($property->getImages() as $image) {
            $byId[$image->getId()] = $image;
        }

        $coverSet = false;
        foreach ($ops as $op) {
            $id = $op['imageId'] ?? null;
            if (null === $id || !isset($byId[$id])) {
                continue;
            }
            $image = $byId[$id];
            if (isset($op['sortOrder'])) {
                $image->setSortOrder((int) $op['sortOrder']);
            }
            if (!empty($op['isCover']) && !$coverSet) {
                $this->clearCovers($property);
                $image->setIsCover(true);
                $coverSet = true;
            }
        }

        $this->em->flush();
        $this->ensureSingleCover($property);

        return $property;
    }

    public function deleteImage(Property $property, string $imageId): void
    {
        $image = $this->imageRepository->find($imageId);
        if (null === $image || $image->getProperty()?->getId() !== $property->getId()) {
            throw new NotFoundHttpException('Image not found.');
        }

        $wasCover = $image->isCover();
        $property->removeImage($image);
        $this->em->remove($image);
        $this->em->flush();

        // Promote the next image to cover if we removed the cover.
        if ($wasCover) {
            $next = $property->getImages()->first();
            if ($next) {
                $next->setIsCover(true);
                $this->em->flush();
            }
        }
    }

    private function ensureSingleCover(Property $property): void
    {
        $covers = [];
        foreach ($property->getImages() as $image) {
            if ($image->isCover()) {
                $covers[] = $image;
            }
        }

        if (0 === count($covers) && !$property->getImages()->isEmpty()) {
            $property->getImages()->first()->setIsCover(true);
            $this->em->flush();
        } elseif (count($covers) > 1) {
            foreach (array_slice($covers, 1) as $extra) {
                $extra->setIsCover(false);
            }
            $this->em->flush();
        }
    }

    private function clearCovers(Property $property): void
    {
        foreach ($property->getImages() as $image) {
            $image->setIsCover(false);
        }
    }

    private function assertValidFile(UploadedFile $file): void
    {
        if ($file->getSize() > self::MAX_BYTES) {
            throw new BadRequestHttpException('Each image must be 5 MB or smaller.');
        }
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            throw new BadRequestHttpException('Only JPEG, PNG, and WebP images are allowed.');
        }
    }
}
