<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\ErrorHelper;
use App\Helpers\TokenGenerator;
use App\Helpers\UserHelper;
use App\Model\Login;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /** @var TokenGenerator */
    private $tokenGenerator;

    /** @var UserHelper */
    private $helper;

    /** @var ErrorHelper */
    private $errorHelper;

    /**
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param TokenGenerator $tokenGenerator
     * @param UserHelper $helper
     * @param ErrorHelper $errorHelper
     */
    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        TokenGenerator $tokenGenerator,
        UserHelper $helper,
        ErrorHelper $errorHelper
    ) {
        $this->validator      = $validator;
        $this->encoder        = $encoder;
        $this->em             = $em;
        $this->serializer     = $serializer;
        $this->tokenGenerator = $tokenGenerator;
        $this->helper         = $helper;
        $this->errorHelper    = $errorHelper;
    }

    /**
     * @Route(path="/api/user/create", methods={"POST"})
     *
     * @param Request $request
     *
     * @throws \TypeError
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $this->helper->createUser($user, $request);

        $errors = $this->validator->validate($user);

        if ($errors->count() > 0) {
            return $this->json($this->errorHelper->prepareResponse($errors));
        }

        $password = $this->encoder->encodePassword($user, $request->get('password'));
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(['message' => 'User created.']);
    }

    /**
     * @Route("/api/user/edit/{user}", methods={"POST"}, requirements={"user"="\d+"})
     *
     * @param User|null $user
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \TypeError
     */
    public function editAction(User $user = null, Request $request)
    {
        if (!$user) {
            return $this->json(['message' => 'user does not exist.']);
        }

        $this->helper->editUser($user, $request);
        $errors = $this->validator->validate($user);

        if ($errors->count() > 0) {
            return $this->json($this->errorHelper->prepareResponse($errors));
        }

        $password = $this->encoder->encodePassword($user, $request->get('password'));
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(['message' => 'User updated.']);
    }

    /**
     * @Route(path="/api/user/delete/{user}", methods={"POST"}, requirements={"user"="\d+"})
     *
     * @param User|null $user
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(User $user = null)
    {
        if (!$user) {
            return $this->json(['message' => 'User does not exist.']);
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->json(['message' => 'User deleted.']);
    }

    /**
     * @Route(path="/api/login", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function loginAction(Request $request)
    {
        $login = new Login();
        $login->setEmail($request->get('email', null));
        $login->setPassword($request->get('password', null));

        $errors = $this->validator->validate($login);

        if ($errors->count() > 0) {
            return $this->json($this->errorHelper->prepareResponse($errors));
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);
        if (!$user) {
            return $this->json(['message' => 'User does not exist.']);
        }

        $isValid = $this->encoder->isPasswordValid($user, $request->get('password'));

        if ($isValid) {
            $user->setToken($this->tokenGenerator->generate());
            $this->em->flush();

            return $this->json(['message' => 'Logged in successfully..', 'token' => $user->getToken()]);
        }

        return $this->json(['message' => 'Invalid user name password.']);
    }

    /**
     * @Route(path="/api/users", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsers()
    {
        $context     = SerializationContext::create()->setGroups(['user-list']);
        $users       = $this->em->getRepository(User::class)->findAll();
        $userRecords = $this->serializer->serialize($users, 'json', $context);

        return $this->json(['data' => json_decode($userRecords)]);
    }
}
