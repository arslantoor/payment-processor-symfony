<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $email
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserByUserName($email)
    {
        return $this->userRepository->loadUserByUsername($email);
    }

    /**
     * @param array $requestData
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(array $requestData)
    {
        return $this->userRepository->createUser($requestData, $this->passwordEncoder);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findOneByOrNull(int $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param User $user
     * @param array $requestData
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUser(User $user, array $requestData)
    {
        return $this->userRepository->updateUser($user, $requestData, $this->passwordEncoder);
    }

    /**
     * @param User $user
     * @return User|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteUser(User $user)
    {
        return $this->userRepository->deleteUser($user);
    }
}
