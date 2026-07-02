<?php

namespace App\Api\Presenter;

use App\Entity\Property\Property;
use App\Entity\Property\PropertyImage;
use Symfony\Component\HttpFoundation\UrlHelper;

class PropertyPresenter
{
    public function __construct(
        private readonly UrlHelper $urlHelper,
        private readonly UserPresenter $userPresenter,
    ) {
    }

    /**
     * Compact card for list responses.
     *
     * @param array<string,bool> $favouritedIds set of property ids the viewer has favourited
     */
    public function list(Property $property, array $favouritedIds = []): array
    {
        $cover = $property->getCoverImage();

        return [
            'id' => $property->getId(),
            'title' => $property->getTitle(),
            'slug' => $property->getSlug(),
            'listingType' => $property->getListingType()->value,
            'category' => $property->getCategory()->value,
            'price' => $property->getPriceMinor(),
            'priceFormatted' => $this->formatPrice($property->getPriceMinor(), $property->getCurrency()),
            'currency' => $property->getCurrency(),
            'area' => $property->getArea(),
            'areaUnit' => $property->getAreaUnit()->value,
            'bedRooms' => $property->getBedRooms(),
            'bathRooms' => $property->getBathRooms(),
            'city' => $property->getCity(),
            'isFeatured' => $property->isFeatured(),
            'coverImageUrl' => $cover ? $this->imageUrl($cover) : null,
            'isFavourited' => isset($favouritedIds[$property->getId()]),
        ];
    }

    /**
     * Full detail view.
     *
     * @param array<string,bool> $favouritedIds
     */
    public function detail(Property $property, array $favouritedIds = []): array
    {
        $images = [];
        foreach ($property->getImages() as $image) {
            $images[] = [
                'id' => $image->getId(),
                'url' => $this->imageUrl($image),
                'sortOrder' => $image->getSortOrder(),
                'isCover' => $image->isCover(),
            ];
        }

        return array_merge($this->list($property, $favouritedIds), [
            'description' => $property->getDescription(),
            'type' => $property->getType()->value,
            'status' => $property->getStatus()->value,
            'rooms' => $property->getRooms(),
            'direction' => $property->getDirection()?->value,
            'state' => $property->getState(),
            'country' => $property->getCountry(),
            'latitude' => $property->getLatitude(),
            'longitude' => $property->getLongitude(),
            'images' => $images,
            'agent' => $property->getOwner() ? $this->userPresenter->agent($property->getOwner()) : null,
            'createdAt' => $property->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ]);
    }

    private function imageUrl(PropertyImage $image): string
    {
        // Stored paths are relative to the public/ web root.
        return $this->urlHelper->getAbsoluteUrl('/'.ltrim($image->getPath(), '/'));
    }

    private function formatPrice(int $minor, string $currency): string
    {
        $amount = $minor / 100;
        $formatter = new \NumberFormatter('en_IN', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        return $formatter->formatCurrency($amount, $currency);
    }
}
