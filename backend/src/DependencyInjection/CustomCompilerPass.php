<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\DependencyInjection;

use App\Extension\AbstractCustomController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CustomCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        //override the AbstractCrudController
        $newDefinition = new Definition(AbstractCustomController::class, []);

        $container->setDefinition(AbstractCrudController::class, $newDefinition);
    }
}