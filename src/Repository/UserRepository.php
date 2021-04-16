<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->persist($user, true);
    }

    /**
     * @param $usernameOrEmail
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($usernameOrEmail)
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User u
                WHERE u.email = :query'
        )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
    }

    /**
     * @param array $requestData
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(array $requestData, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $user->setFirstName($requestData['first_name']);
        $user->setLastName($requestData['last_name']);
        $user->setEmail($requestData['email']);
        $user->setPassword($passwordEncoder->encodePassword($user, $requestData['password']));
        $user->setRoles(['ROLE_USER']);
        $user->setIsDeleted(false);

        $this->persist($user);
        $this->flush();

        return $user;
    }

    /**
     * @param User $user
     * @param array $requestData
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUser(User $user, array $requestData, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (isset($requestData['first_name'])) {
            $user->setFirstName($requestData['first_name']);
        }
        if (isset($requestData['last_name'])) {
            $user->setLastName($requestData['last_name']);
        }
        if (isset($requestData['email'])) {
            $user->setEmail($requestData['email']);
        }
        if (isset($requestData['password'])) {
            $user->setPassword($passwordEncoder->encodePassword($user, $requestData['password']));
        }

        $this->flush();
        return $user;
    }

    /**
     * @param User|null $user
     * @return User|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteUser(User $user)
    {
        $user->setIsDeleted(true);
        $this->flush();
        return $user;
    }
}
