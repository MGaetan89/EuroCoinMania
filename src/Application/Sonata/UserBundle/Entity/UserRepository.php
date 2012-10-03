<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRepository extends EntityRepository {

	public function count() {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->count('u'))
						->getQuery()
						->getSingleScalarResult();
	}

	public function findAgeStats() {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('total', 'total', 'integer');
		$rsm->addScalarResult('count_date', 'count_date', 'integer');

		$result = $this->getEntityManager()
				->createNativeQuery('
					SELECT SUM(YEAR(FROM_DAYS(DATEDIFF(NOW(), u.date_of_birth)))) AS total, COUNT(u.date_of_birth) AS count_date
					FROM fos_user__user u
					WHERE u.date_of_birth IS NOT NULL
					', $rsm)
				->getSingleResult();

		return round($result['total'] / $result['count_date']);
	}

	public function findGendersStats() {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->count('u.id') . ' AS total, u.gender')
						->where($expr->neq('u.gender', "''"))
						->groupBy('u.gender')
						->orderBy('total', 'DESC')
						->addOrderBy('u.gender', 'ASC')
						->setMaxResults(10)
						->getQuery()
						->getResult();
	}

	public function findFromStats() {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->count('u.id') . ' AS total, u.country')
						->where($expr->isNotNull('u.country'))
						->groupBy('u.country')
						->orderBy('total', 'DESC')
						->setMaxResults(10)
						->getQuery()
						->getResult();
	}

}
