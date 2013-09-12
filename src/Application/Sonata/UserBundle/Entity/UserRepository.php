<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRepository extends EntityRepository {

	public function count($letter = null) {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->count('u'))
						->where($expr->like($expr->upper('u.username'), $expr->literal(strtoupper($letter) . '%')))
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

	public function findByLetter($letter, $from, $limit) {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		if ($letter !== '#') {
			$cond = $expr->like($expr->upper('u.username'), $expr->literal(strtoupper($letter) . '%'));
		} else {
			$cond = $expr->not($expr->between($expr->upper($expr->substring('u.username', 1, 1)), $expr->literal('A'), $expr->literal('Z')));
		}

		return $queryBuidler
						->where($cond)
						->orderBy('u.username', 'ASC')
						->setFirstResult($from)
						->setMaxResults($limit)
						->getQuery()
						->getResult();
	}

	public function findFirstLetters() {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->upper($expr->substring('u.username', 1, 1)) . ' AS letter')
						->groupBy('letter')
						->orderBy('letter', 'ASC')
						->getQuery()
						->getResult();
	}

	public function findGendersStats() {
		$queryBuidler = $this->createQueryBuilder('u');
		$expr = $queryBuidler->expr();

		return $queryBuidler
						->select($expr->count('u.id') . ' AS total, u.gender')
						->where($expr->isNotNull('u.gender'))
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

	public function findLatestUser() {
		$queryBuilder = $this->createQueryBuilder('u');

		return $queryBuilder
						->orderBy('u.createdAt', 'DESC')
						->setMaxResults(1)
						->getQuery()
						->getResult();
	}

	public function findTodayBirthdays() {
		$queryBuilder = $this->createQueryBuilder('u');
		$expr = $queryBuilder->expr();

		return $queryBuilder
						->where($expr->like('u.dateOfBirth', $expr->literal('____-' . date('m') . '-' . date('d') . '%')))
						->orderBy('u.username')
						->getQuery()
						->getResult();
	}

	public function findUpcomingBirthdays() {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('id', 'id', 'integer');
		$rsm->addScalarResult('username', 'username', 'string');
		$rsm->addScalarResult('public_profile', 'publicprofile', 'boolean');
		$rsm->addScalarResult('date_of_birth', 'dateOfBirth', 'datetime');

		return $this->getEntityManager()
				->createNativeQuery('
					SELECT u.id, u.username, u.public_profile, u.date_of_birth
					FROM fos_user__user u
					WHERE DAYOFYEAR(curdate()) <= dayofyear(u.date_of_birth) AND DAYOFYEAR(curdate()) + 7 >= dayofyear(u.date_of_birth)
					ORDER BY MONTH(u.date_of_birth), DAY(u.date_of_birth)
					LIMIT 10
					', $rsm)
				->getResult();
	}

}
