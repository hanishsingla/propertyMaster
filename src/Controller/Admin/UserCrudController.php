<?php

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Enum\Gender;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<User>
 */
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield EmailField::new('email');
        yield TextField::new('name');
        yield BooleanField::new('isAgent')->setLabel('Agent');
        yield BooleanField::new('isVerified')->setLabel('Verified');
        yield ChoiceField::new('gender')
            ->setChoices($this->genderChoices())
            ->hideOnIndex();
        yield TextField::new('phone')->hideOnIndex();
        yield TextField::new('city')->hideOnIndex();
        yield ChoiceField::new('roles')
            ->setChoices([
                'User' => 'ROLE_USER',
                'Agent' => 'ROLE_AGENT',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->hideOnIndex();
    }

    /**
     * @return array<string, Gender>
     */
    private function genderChoices(): array
    {
        $choices = [];
        foreach (Gender::cases() as $case) {
            $choices[$case->label()] = $case;
        }

        return $choices;
    }
}
