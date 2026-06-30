<?php

namespace App\Controller\Api;

use App\Api\ApiResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiHealthController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function index(ApiResponder $api): Response
    {
        return $api->success([
           'service' => 'databridge-hub',
           'status' => 'ok',
           'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]);
    }
}
