<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

/**
 * Class LedgerRepository
 * @package App\Repository
 */
class LedgerRepository extends EntityRepository
{
    /**
     * @param $userObject
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBalance($userObject): ?int
    {
        $qb = $this->createQueryBuilder('l')
            ->select('(sum(l.credit) - sum(l.debit)) as balance')
            ->where('l.user = :userId')
            ->setParameter('userId', $userObject)
            ->getQuery();

        return $qb
            ->getSingleScalarResult();
    }

    /**
     * @param $userObject
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isEligibleToCharge($userObject): bool
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.debit')
            ->where('l.user = :user')
            ->andWhere('DATE(l.created_at) = CURRENT_DATE()')
            ->setParameter('user', $userObject)
            ->getQuery()
            ->getOneOrNullResult();

        if($qb['debit'] > 0) {
            return false;
        }

        return true;
    }

}