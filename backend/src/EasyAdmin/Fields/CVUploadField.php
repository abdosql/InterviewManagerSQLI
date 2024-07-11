<?php

namespace App\EasyAdmin\Fields;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CVUploadField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): CVUploadField
    {
        return (new self())->setProperty($propertyName)
            ->setLabel($label?? ucfirst($propertyName))
            ->setFormType(FileType::class)
            ->setCustomOption('upload_dir', null)
            ->setCustomOption('base_path', null);
    }

    public function setUploadDir(string $uploadDir): self
    {
        $this->setCustomOption('upload_dir', $uploadDir);
        return $this;
    }
    public function setBasePath(string $basePath): self
    {
        $this->setCustomOption('base_path', $basePath);
        return $this;
    }
}