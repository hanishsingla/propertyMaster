<?php

namespace App\Entity\Category;

use App\Entity\AbstractEntity;
use App\Repository\Category\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'category')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{

}
