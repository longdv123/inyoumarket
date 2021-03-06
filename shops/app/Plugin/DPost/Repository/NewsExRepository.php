<?php

namespace Plugin\DPost\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NewsExRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NewsExRepository extends EntityRepository
{
    public $app;

    public function setApplication($app)
    {
        $this->app = $app;
    }

    public function getNewsList() {

        $qb = $this->createQueryBuilder('n')
                ->innerJoin('n.News', "news")
                ->where("news.del_flg = 0")
                ->orderBy('news.rank', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
