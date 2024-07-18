<?php

namespace App\EasyAdmin\Fields;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ResumeUploadField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string $propertyName
     * @param string|null $label
     * @return ResumeUploadField
     */
    public static function new(string $propertyName, ?string $label = null): ResumeUploadField
    {
        return (new self())->setProperty($propertyName)
            ->setLabel($label ?? ucfirst($propertyName))
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                "data_class" => null,
            ]);
    }
}