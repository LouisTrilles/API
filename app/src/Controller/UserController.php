<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    /**
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->validator = $validator;
    }


    #[Route('/api/users', name: 'app_user_details', methods: "GET")]
    public function userDetails(): Response
    {
       $user = $this->getUser();
       return new JsonResponse($this->serializer->serialize($user, "json", [
           "groups" => ["read:user"]
       ]), 200, [], true);
    }

    #[Route('/api/users', name: 'app_user_update', methods: "PATCH")]
    public function updateUser(Request $request): Response
    {
        $currentUser = $this->getUser();
        $updatedUser = $this->serializer->deserialize($request->getContent(), User::class, "json", [
            AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser
        ]);
        if (count($this->validator->validate($updatedUser)) > 0){
            throw new ValidationFailedException("Validation Failed", $this->validator->validate($updatedUser));
        }
        $this->em->flush();
        return new JsonResponse($this->serializer->serialize($currentUser, "json", [
            "groups" => ["read:user"]
        ]), 200, [], true);
    }
}
