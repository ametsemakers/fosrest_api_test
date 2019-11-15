<?php

namespace App\Repository;

class ArticleRepository extends AbstractRepository
{
    public function search($term, $order = 'asc', $limit = 20, $offset = 0)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.title', $order)
        ;
        
        if ($term) {
            $qb
                ->where('a.title LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }
        
        return $this->paginate($qb, $limit, $offset);
    }

    /* pagination example */
    public function findBlogArticles($limit = 10, $page)
    {
        $sectionCode = 'blog';
        $localeCode  = 'fr_FR';

        $qb = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->innerJoin('o.sections', 'section')
            ->where('translation.locale = :localeCode')
            ->andWhere('section.code = :sectionCode')
            ->andWhere('o.enabled = true')
            ->orderBy('o.updatedAt', 'DESC')
            ->setParameter('sectionCode', $sectionCode)
            ->setParameter('localeCode', $localeCode)
        ;

        return $this->paginate($qb, $limit, $page);
    }
}