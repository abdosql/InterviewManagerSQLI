<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Abstract\UserCrudController;
use App\Entity\HRManager;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HRManagerCrudController extends UserCrudController
{
    public static function getEntityFqcn(): string
    {
        return HRManager::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield TextField::new('department', "Department");
        yield TextField::new('position', "Position");
    }

}
