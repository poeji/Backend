<?php

namespace Persona\Hris\Repository\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ihsan\UsernameGenerator\Repository\UsernameInterface;
use Ihsan\UsernameGenerator\Repository\UsernameRepositoryInterface;
use Persona\Hris\Core\Manager\ManagerFactory;
use Persona\Hris\Core\Security\Model\UserInterface;
use Persona\Hris\Core\Security\Model\UserRepositoryInterface;
use Persona\Hris\Repository\AbstractCachableRepository;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@personahris.com>
 */
final class UserRepository extends AbstractCachableRepository implements UserRepositoryInterface, UsernameRepositoryInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param ManagerFactory $managerFactory
     * @param string         $class
     */
    public function __construct(ManagerFactory $managerFactory, string  $class)
    {
        parent::__construct($managerFactory);
        $this->class = $class;
    }

    /**
     * @param string $id
     *
     * @return UserInterface|null
     */
    public function find(string $id): ? UserInterface
    {
        return $this->managerFactory->getWriteManager()->getRepository($this->class)->findOneBy(['id' => $id, 'deletedAt' => null]);
    }

    /**
     * @param string $sessionId
     *
     * @return UserInterface|null
     */
    public function findUserBySessionId(string $sessionId): ? UserInterface
    {
        return $this->managerFactory->getWriteManager()->getRepository($this->class)->findOneBy(['sessionId' => $sessionId, 'deletedAt' => null]);
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function isExist($username)
    {
        $user = $this->managerFactory->getReadManager()->getRepository($this->class)->findOneBy(['username' => $username, 'deletedAt' => null]);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $characters
     *
     * @return int
     */
    public function countUsage($characters)
    {
        /** @var EntityRepository $repository */
        $repository = $this->managerFactory->getReadManager()->getRepository($this->class);
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('o');
        $queryBuilder->select('COUNT(o.id)');
        $queryBuilder->andWhere($queryBuilder->expr()->like('o.username', $queryBuilder->expr()->literal(sprintf('%%%s%%', $characters))));
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('o.deletedAt'));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param UsernameInterface $username
     */
    public function save(UsernameInterface $username)
    {
        throw new \RuntimeException('This method is not implemented');
    }
}
