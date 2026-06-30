<?php
declare(strict_types=1);

namespace App\Security;

use App\Api\ApiResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

final class ApiAuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        private readonly ApiResponder $api,
    )
    {

    }

    public function onAuthenticationFailure(
        Request                  $request,
        AuthenticationException $exception,
    ): Response
    {
        return $this->api->unauthorized('Некорректный или просроченный Bearer token.');
    }
}
