<?php
namespace ExpressiveValidator\Middleware;

use Exception;
use Psr\Http\Message\ResponseInterface;
use ExpressiveValidator\Exception\EntityNotFoundException;
use ExpressiveValidator\Exception\MethodNotAllowedException;
use ExpressiveValidator\Response\JsonExceptionResponse;
use ExpressiveValidator\Validator\ValidationFailedException;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles the exception thrown by
 * not allowed methods or not implemented
 * Class MethodNotAllowedMiddleware
 * @package SchedulerApi\Middleware
 */
class ErrorMiddleware
{
    /**
     * @param mixed $error
     * @param Request $request
     * @param Response $response
     * @param callable|null $out
     * @return
     * @throws Exception
     */
    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        if (!($error instanceof Exception)) {
            $error = new MethodNotAllowedException();
        }
        switch (true) {
            case $error instanceof MethodNotAllowedException || $error instanceof EntityNotFoundException:
                return $out($request, new JsonExceptionResponse($error->getCode(), $error->getMessage()));
            case ($error instanceof ValidationFailedException):
                $messages = $error->getValidationResult()->getMessages();
                return $out($request, new JsonExceptionResponse(
                    $error->getCode(),
                    count($messages) > 0 ? $messages[0] : 'Something is not right'
                ));
            default:
                throw $error;
        }
    }
}
