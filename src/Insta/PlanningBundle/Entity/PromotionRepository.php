<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * PromotionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PromotionRepository extends EntityRepository
{
    /** @var  array|Promotion[] */
    private $promotionCache = null;

    public function exists($promoName) {
        $qb = $this->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.name = :promotionName')
            ->setParameter('promotionName', $promoName)
            ->setMaxResults(1);

        try {
            $result = $qb->getQuery()->getSingleResult();
            return true;
        } catch (NoResultException $e) {
            return false;
        }

    }

    public function loadByName($name) {
        if (is_null($this->promotionCache)) {
            $this->__loadPromotions();
        }

        if (array_key_exists($name, $this->promotionCache)) {
            return $this->promotionCache[ $name ];
        } else {
            return null;
        }
    }

    private function __loadPromotions() {

        $this->promotionCache = array();
        foreach ($this->findAll() as $promotion) {
            $this->promotionCache[ $promotion->getName() ] = $promotion;
        }

    }

}
