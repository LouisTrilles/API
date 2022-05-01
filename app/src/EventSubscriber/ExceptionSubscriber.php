<?php

namespace App\EventSubscriber;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        if($event->getThrowable() instanceof ValidationFailedException){
            $violations = $event->getThrowable()->getViolations();
            $response = ["errors" => []];
            foreach ($violations as $violation){
                array_push($response["errors"],$violation->getPropertyPath()." ".$violation->getMessage());
            }
            $event->setResponse(new JsonResponse($response, 400));
        } else if ($event->getThrowable() instanceof NotNormalizableValueException) {
            $response = [
                "error" => $event->getThrowable()->getMessage()
            ];
            $event->setResponse(new JsonResponse($response, 400));
        } else if ($event->getThrowable() instanceof UniqueConstraintViolationException){
            $response = [
                "error" => "One or more unique constraints has been violated"
            ];
            $event->setResponse(new JsonResponse($response, 400));
        } else if ($event->getThrowable() instanceof NotFoundHttpException){
            $response = [
                "error" => $event->getThrowable()->getMessage()
            ];
            $event->setResponse(new JsonResponse($response, 404));

        } else if ($event->getThrowable() instanceof AccessDeniedException){

            $response = [
                "error" => $event->getThrowable()->getMessage()
            ];

            $event->setResponse(new JsonResponse($response, 403));
        }
        else {
            $response = [
                "error" => $event->getThrowable()->getMessage()
            ];
            $event->setResponse(new JsonResponse($response, 500));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => ['onKernelException', 256],
        ];
    }
}
