<?php

namespace App\Controller;

use App\Entity\Group;
use App\Helpers\ErrorHelper;
use App\Helpers\GroupHelper;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    /** @var ErrorHelper */
    private $errorHelper;

    /** @var GroupHelper */
    private $groupHelper;

    /**
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $em
     * @param SerializerInterface    $serializer
     * @param ErrorHelper            $errorHelper
     * @param GroupHelper            $groupHelper
     */
    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ErrorHelper $errorHelper,
        GroupHelper $groupHelper
    ) {
        $this->validator   = $validator;
        $this->em          = $em;
        $this->serializer  = $serializer;
        $this->errorHelper = $errorHelper;
        $this->groupHelper = $groupHelper;
    }

    /**
     * @Route(path="/api/group/create", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createGroupAction(Request $request)
    {
        $group = new Group();
        $group->setName($request->get('name', null));

        $errors = $this->validator->validate($group);

        if ($errors->count() > 0) {
            return $this->json($this->errorHelper->prepareResponse($errors));
        }

        $this->em->persist($group);
        $this->em->flush();

        return $this->json(['message' => 'Group created.']);
    }

    /**
     * @Route(path="/api/group/delete/{id}", methods={"POST"})
     *
     * @param Group|null $group
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeGroup(Group $group = null)
    {
        if (!$group) {
            return $this->json(['message' => 'Requested group does not exist.']);
        }

        if($group->hasUsers()){
            return $this->json(['message' => 'Group cannot be deleted since it has users']);
        }
        
        $this->em->remove($group);
        $this->em->flush();

        return $this->json(['message' => 'Group deleted.']);
    }

    /**
     * @Route(path="/api/groups", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getGroup()
    {
        $context       = SerializationContext::create()->setGroups(['list']);
        $groups        = $this->em->getRepository(Group::class)->findAll();
        $groupsRecords = $this->serializer->serialize($groups, 'json', $context);

        return $this->json(['data' => json_decode($groupsRecords)]);
    }

    /**
     * @Route(path="/api/group/{group}/add-users", methods={"POST"}, requirements={"group"="\d+"})
     *
     * @param Group|null $group
     * @param Request    $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addUserGroup(Group $group = null, Request $request)
    {
        $userIds  = $request->get('users', []);
        $validate = $this->groupHelper->validate($group, $userIds);

        if (!$validate['isValid']) {
            return $this->json(['message' => $validate['message']]);
        }

        $this->groupHelper->manageGroupUsers($userIds, $group, 'add');
        $this->em->flush();

        return $this->json(['message' => 'User added to group.']);
    }

    /**
     * @Route(path="/api/group/{group}/remove-users", methods={"POST"}, requirements={"group"="\d+"})
     *
     * @param Group|null $group
     * @param Request    $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeUserGroup(Group $group = null, Request $request)
    {
        $userIds  = $request->get('users', []);
        $validate = $this->groupHelper->validate($group, $userIds);

        if (!$validate['isValid']) {
            return $this->json(['message' => $validate['message']]);
        }

        $this->groupHelper->manageGroupUsers($userIds, $group, 'remove');

        $this->em->flush();

        return $this->json(['message' => 'User removed from group.']);
    }

    /**
     * @Route(path="/api/group/edit/{group}", methods={"POST"}, requirements={"group"="\d+"})
     *
     * @param Group|null $group
     * @param Request    $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateGroup(Group $group = null, Request $request)
    {
        if (!$group) {
            return $this->json(['message' => 'Group does not exist.']);
        }

        $group->setName($request->get('name', null));
        $errors = $this->validator->validate($group);
        if ($errors->count() > 0) {
            return $this->json($this->errorHelper->prepareResponse($errors));
        }

        $this->em->persist($group);
        $this->em->flush();

        return $this->json(['message' => 'Group updated.']);
    }
}
