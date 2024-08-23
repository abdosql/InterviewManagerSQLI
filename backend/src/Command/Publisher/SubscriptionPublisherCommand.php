<?php

namespace App\Command\Publisher;

use App\Entity\Candidate;
use App\Entity\User;
use App\Notification\PublisherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "app:subscription:publish")]
class SubscriptionPublisherCommand extends Command
{
    private PublisherInterface $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->publisher->publish(['status' => 'OutOfStock'], new User());

        return self::SUCCESS;
    }
}
