<?php

namespace App\Controller\Api;

use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\Gender;
use App\Enum\LabelledEnum;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class EnumApiController extends AbstractController
{
    #[Route('/api/enums', name: 'api_enums', methods: ['GET'])]
    public function enums(): JsonResponse
    {
        return new JsonResponse([
            'gender' => $this->cases(Gender::cases()),
            'listingType' => $this->cases(ListingType::cases()),
            'propertyType' => $this->cases(PropertyType::cases()),
            'propertyCategory' => $this->cases(PropertyCategory::cases()),
            'propertyStatus' => $this->cases(PropertyStatus::cases()),
            'areaUnit' => $this->cases(AreaUnit::cases()),
            'direction' => $this->cases(Direction::cases()),
        ]);
    }

    /**
     * @param array<int, LabelledEnum&\BackedEnum> $cases
     *
     * @return array<int, array{value:string,label:string}>
     */
    private function cases(array $cases): array
    {
        return array_map(
            static fn (LabelledEnum&\BackedEnum $case) => ['value' => $case->value, 'label' => $case->label()],
            $cases
        );
    }
}
