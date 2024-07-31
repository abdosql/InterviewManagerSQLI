<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Command\Handler;

use App\Candidate\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DefaultCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function handle(object $command): void
    {
        if (!$command instanceof CommandInterface){
            throw new \InvalidArgumentException('Invalid command');
        }
        $entityId = $command->execute();
        $messageClass = $command::getMessageClass();
        $message = new $messageClass(
            $entityId
        );

        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$messageClass." : ". $e->getMessage());
        }
    }
}