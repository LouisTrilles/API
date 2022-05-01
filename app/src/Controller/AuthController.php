<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: "POST")]
    public function register(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, "json");
        $user->setRoles(["ROLE_USER"]);
        $hashedPassword = $hasher->hashPassword($user, json_decode($request->getContent(), true)["password"]);
        $user->setPassword($hashedPassword);
        $errors = $validator->validate($user);
        if (count($errors) > 0){
            throw new ValidationFailedException("Something goes wrong in your request", $errors);
        }
        $em->persist($user);
        $em->flush();
        $serializedUser = $serializer->serialize($user, "json", ["groups" => "read:user"]);
        return new JsonResponse($serializedUser, 201, [], true);
    }
}
