<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\User\UserCreatedMessage;
use App\Message\User\UserDeletedMessage;
use App\Message\User\UserUpdatedMessage;
use App\Services\Impl\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

readonly class UserHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private UserService $userService,
    ) {}

    /**
     * @param UserCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleNewUser(UserCreatedMessage $message): void
    {
        $userDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'user');
        $this->userService->saveDocument($userDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleUpdatedUser(UserUpdatedMessage $message): void
    {
        $updatedUserDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'user');
        $this->userService->updateDocument($message->getId(), $updatedUserDocument);
    }

    /**
     * @throws MongoDBException
     */
    #[AsMessageHandler]
    public function handleDeletedUser(UserDeletedMessage $message): void
    {
        $this->userService->deleteDocument($message->getId());
    }
}