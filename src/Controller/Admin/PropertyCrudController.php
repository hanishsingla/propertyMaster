<?php

namespace App\Controller\Admin;

use App\Entity\Property\Property;
use App\Enum\AreaUnit;
use App\Enum\Direction;
use App\Enum\ListingType;
use App\Enum\PropertyCategory;
use App\Enum\PropertyStatus;
use App\Enum\PropertyType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PropertyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('title');
        yield TextField::new('slug')->hideOnForm();
        yield AssociationField::new('owner')->autocomplete();
        yield $this->enumField('listingType', ListingType::class);
        yield $this->enumField('category', PropertyCategory::class);
        yield $this->enumField('type', PropertyType::class);
        yield $this->enumField('status', PropertyStatus::class);
        yield IntegerField::new('priceMinor')->setLabel('Price (paise)');
        yield TextField::new('currency')->hideOnIndex();
        yield IntegerField::new('area');
        yield $this->enumField('areaUnit', AreaUnit::class)->hideOnIndex();
        yield IntegerField::new('bedRooms')->hideOnIndex();
        yield IntegerField::new('bathRooms')->hideOnIndex();
        yield IntegerField::new('rooms')->hideOnIndex();
        yield $this->enumField('direction', Direction::class)->hideOnIndex();
        yield TextField::new('city');
        yield TextField::new('state')->hideOnIndex();
        yield TextField::new('country')->hideOnIndex();
        yield NumberField::new('latitude')->hideOnIndex();
        yield NumberField::new('longitude')->hideOnIndex();
        yield BooleanField::new('isFeatured');
        yield TextareaField::new('description')->hideOnIndex();
    }

    /**
     * @param class-string<\App\Enum\LabelledEnum&\BackedEnum> $enumClass
     */
    private function enumField(string $field, string $enumClass): ChoiceField
    {
        $choices = [];
        foreach ($enumClass::cases() as $case) {
            $choices[$case->label()] = $case;
        }

        return ChoiceField::new($field)->setChoices($choices);
    }
}
