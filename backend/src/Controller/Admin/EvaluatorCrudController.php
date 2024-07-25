<?php
declare(strict_types=1);
namespace App\Controller\Admin;
use App\Controller\Admin\Abstract\UserCrudController;
use App\Entity\Evaluator;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EvaluatorCrudController extends UserCrudController
{

    public static function getEntityFqcn(): string
    {
        return Evaluator::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield from parent::configureFields($pageName);
        yield TextField::new('specialization', "Specialization");
    }


}
