<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Appreciation\Command\AddAppreciationCommand;
use App\Appreciation\Command\Handler\CommandHandlerInterface;
use App\Appreciation\Command\Handler\DefaultCommandHandler;
use App\Entity\Appreciation;
use App\Interview\Query\FindInterview;
use App\Interview\Query\ItemQueryInterface;
use App\Services\Impl\AppreciationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class AppreciationController extends AbstractController
{
    public function __construct(
        private readonly AppreciationService $appreciationService,
        protected MessageBusInterface            $messageBus,
        #[Autowire(service: DefaultCommandHandler::class)]
        private readonly CommandHandlerInterface $commandHandler,
        #[Autowire(service: FindInterview::class)]
        private readonly ItemQueryInterface $interviewItemQuery,
    ){}
    #[Route('/api/interview/appreciation', name: "app-appreciation", methods: ["GET", "POST"])]
    public function index(Request $request): Response
    {
        if ($request->isMethod("GET")){
            return new JsonResponse(['status' => 'success', 'message' => 'getAppreciations']);
        }
        $data = json_decode($request->getContent(), true);
        $comment = $data['comment'];
        $score = $data['score'];
        $interviewId = $data['interviewId'];
        if (empty($comment) || empty($score)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Some fields are missing'],
                Response::HTTP_BAD_REQUEST
            );
        }
        if (empty($interviewId)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing interview ID'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $appreciation = new Appreciation();
        $appreciation->setComment($comment)
        ->setScore((int)$score)
        ->setInterview($this->interviewItemQuery->findItem((int)$interviewId))
        ;
        $command = new AddAppreciationCommand($appreciation, $this->appreciationService, $this->messageBus);
        $this->commandHandler->handle($command);
        return new JsonResponse(['status' => 'success', 'id' => $appreciation->getId()], Response::HTTP_OK);
    }
}
