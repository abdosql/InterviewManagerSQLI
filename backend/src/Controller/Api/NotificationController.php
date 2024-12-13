<?php

namespace App\Controller\Api;

use App\Notification\Command\Handler\CommandHandlerInterface;
use App\Notification\Command\Handler\DefaultCommandHandler;
use App\Notification\Command\MarkNotificationAsReadCommand;
use App\Notification\Query\ItemQueryInterface;
use App\Services\Impl\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly ItemQueryInterface      $findNotification,
        private readonly notificationService     $notificationService,
        protected MessageBusInterface            $messageBus,
        #[Autowire(service: DefaultCommandHandler::class)]
        private readonly CommandHandlerInterface $commandHandler
    )
    {
    }

    #[Route("/api/notifications/{id}/mark-read", name: 'mark_notification_as_read', methods: ['POST'])]
    public function index(int $id): Response
    {
        try {
            $notification = $this->findNotification->findItem($id);
            if (!$notification) {
                return new JsonResponse(['status' => 'error', 'message' => 'Notification not found'], Response::HTTP_NOT_FOUND);
            }
            $command = new MarkNotificationAsReadCommand($notification, $this->notificationService, $this->messageBus);
            $this->commandHandler->handle($command);

            return new JsonResponse(['status' => 'success', 'message' => 'Notification marked as read']);
        } catch (\Exception) {

            return new JsonResponse(['status' => 'error', 'message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
