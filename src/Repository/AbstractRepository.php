<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit = 20, $offset = 0)
    {
        $currentPage = ceil(($offset + 1) / $limit);
        if (0 == $limit || 0 == $currentPage) {
            throw new \LogicException('$limit & $currentpage must be greater than 0.');
        }
        
        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        //$currentPage = ceil(($offset + 1) / $limit);
        $pager->setCurrentPage($currentPage);
        $pager->setMaxPerPage((int) $limit);
        
        return $pager;
    }

    /* Custom method for App\Controller::blog */
    protected function ppppaginate(Querybuilder $qb, $limit, $page) // $offset = 0
    {
        if (0 == $limit)
        {
            throw new \LogicException('$limit must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
        $pager->setMaxPerPage((int) $limit);
        $pager->setCurrentPage($page);
        
        return $pager;
    }
}