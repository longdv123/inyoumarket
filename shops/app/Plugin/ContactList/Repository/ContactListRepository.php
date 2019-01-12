<?php

namespace Plugin\ContactList\Repository;

use Eccube\Util\Str;
use Doctrine\ORM\EntityRepository;

class ContactListRepository extends EntityRepository
{
    /**
     *
     * @param  array        $searchData
     * @return QueryBuilder
     */
    public function getQueryBuilderBySearchDataForAdmin($searchData)
    {
        $qb = $this->createQueryBuilder('o');

        // contact_id_start
        if (isset($searchData['contact_id_start']) && Str::isNotBlank($searchData['contact_id_start'])) {
            $qb
                ->andWhere('o.contact_id >= :contact_id_start')
                ->setParameter('contact_id_start', $searchData['contact_id_start']);
        }
        // multi
        if (isset( $searchData['multi']) && Str::isNotBlank($searchData['multi'])) {
            $multi = preg_match('/^\d+$/', $searchData['multi']) ? $searchData['multi'] : null;
            $qb
                ->andWhere('o.contact_id = :multi OR o.contact_name01 LIKE :likemulti OR o.contact_name02 LIKE :likemulti OR ' .
                           'o.contact_kana01 LIKE :likemulti OR o.contact_kana02 LIKE :likemulti OR o.contact_email LIKE :likemulti')
                ->setParameter('multi', $multi)
                ->setParameter('likemulti', '%' . $searchData['multi'] . '%');
        }

        // order_id_end
        if (isset($searchData['contact_id_end']) && Str::isNotBlank($searchData['contact_id_end'])) {
            $qb
                ->andWhere('o.contact_id <= :contact_id_end')
                ->setParameter('contact_id_end', $searchData['contact_id_end']);
        }

        // status
        if (!empty($searchData['status']) && $searchData['status']) {
            $qb
                ->andWhere('o.contact_status = :status')
                ->setParameter('status', $searchData['status']);
        }

        // name
        if (isset($searchData['name']) && Str::isNotBlank($searchData['name'])) {
            $qb
                ->andWhere('CONCAT(o.contact_name01, o.contact_name02) LIKE :name')
                ->setParameter('name', '%' . $searchData['name'] . '%');
        }

        // kana
        if (isset($searchData['kana']) && Str::isNotBlank($searchData['kana'])) {
            $qb
                ->andWhere('CONCAT(o.contact_kana01, o.contact_kana02) LIKE :kana')
                ->setParameter('kana', '%' . $searchData['kana'] . '%');
        }

        // email
        if (isset($searchData['email']) && Str::isNotBlank($searchData['email'])) {
            $qb
                ->andWhere('o.contact_email like :email')
                ->setParameter('email', '%' . $searchData['email'] . '%');
        }

        // oreder_date
        if (!empty($searchData['contact_date_start']) && $searchData['contact_date_start']) {
            $date = $searchData['contact_date_start']
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('o.contact_ins_time >= :contact_date_start')
                ->setParameter('contact_date_start', $date);
        }
        if (!empty($searchData['contact_date_end']) && $searchData['contact_date_end']) {
            $date = clone $searchData['contact_date_end'];
            $date = $date
                ->modify('+1 days')
                ->format('Y-m-d H:i:s');
            $qb
                ->andWhere('o.contact_ins_time < :contact_date_end')
                ->setParameter('contact_date_end', $date);
        }

    // Order By
        $qb->addOrderBy('o.contact_ins_time', 'DESC');

        return $qb;
    }

}