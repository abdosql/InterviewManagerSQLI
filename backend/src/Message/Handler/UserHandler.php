<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\Abstract\AbstractUserMessage;
use App\Message\User\UserCreatedMessage;
use App\Message\User\UserDeletedMessage;
use App\Message\User\UserUpdatedMessage;
use App\Services\Impl\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private UserService $userService,
        private LoggerInterface $logger
    ) {}

    /**
     * @param AbstractUserMessage $message
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(AbstractUserMessage $message): void
    {
        try {
            if ($message instanceof UserCreatedMessage) {
                $this->handleNewUser($message);
            } elseif ($message instanceof UserUpdatedMessage) {
                $this->handleUpdatedUser($message);
            } elseif ($message instanceof UserDeletedMessage) {
                $this->handleDeletedUser($message);
            } else {
                throw new \InvalidArgumentException('Unsupported message type');
            }
        } catch (\Exception $e) {
            $this->logger->error('Error processing user message: ' . $e->getMessage(), [
                'message_type' => get_class($message),
                'candidate_id' => $message->getId(),
            ]);
            throw $e;
        }
    }

    /**
     * @param UserCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleNewUser(UserCreatedMessage $message): void
    {
        $userDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'user');
        $this->userService->saveDocument($userDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    private function handleUpdatedUser(UserUpdatedMessage $message): void
    {
        $updatedUserDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'user');
        $this->userService->updateDocument($message->getId(), $updatedUserDocument);
    }

    /**
     * @throws MongoDBException
     */
    private function handleDeletedUser(UserDeletedMessage $message): void
    {
        $this->userService->deleteDocument($message->getId());
    }
}