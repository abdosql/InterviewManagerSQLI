<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InterviewController extends AbstractController
{
    #[Route('/api/interview', name: 'app_interview', methods: ["post"])]
    public function createEvent(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        return $this->json(['success' => true, 'data' => $data]);
    }
}
