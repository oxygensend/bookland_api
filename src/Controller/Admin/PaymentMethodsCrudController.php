<?php

namespace App\Controller\Admin;

use App\Entity\PaymentMethods;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PaymentMethodsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentMethods::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
