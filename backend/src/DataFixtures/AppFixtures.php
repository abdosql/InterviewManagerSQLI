<?php

namespace App\DataFixtures;

use App\Factory\CandidateFactory;
use App\Factory\EvaluatorFactory;
use App\Factory\HRManagerFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        HRManagerFactory::createOne()->setUsername("abdo")->setRoles(["ROLE_ADMIN", "PUBLIC_ACCESS"]);

        $manager->flush();
    }
}
