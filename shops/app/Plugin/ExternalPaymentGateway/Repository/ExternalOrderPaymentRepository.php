<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Repository;

use Doctrine\ORM\EntityRepository;
use Plugin\ExternalPaymentGateway\Controller\Util\CommonUtil;

/**
 * OrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExternalOrderPaymentRepository extends EntityRepository
{
    /** @var array */
    public $config;

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * スピード決済判断SQL
     * 注文履歴のカウント取得
     * 購入履歴チェック(都度決済 購入回数の取得) del_flgは考慮しない
     * クレジット決済を行った(決済完了)注文にはdtb_orderのmemo01にクライアントIPが付与されるので、
     * SQLの条件式にT1.memo01 = T2.memo01を加えてスピード決済可否の判別を行う。
     * @param int $customerId
     *
     * @return count
     */
    public function getOrderHistoryCount($customerId)
    {

        if (is_null($customerId)) {
            return 0;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('count(tp.id)')
            ->from('\Eccube\Entity\Order', 'o')
            ->leftJoin('o.Payment', 'p')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment',  'to', 'WITH', 'o.id = to.id')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod', 'tp', 'WITH', 'p.id = tp.id')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('o.Customer', $customerId),
                $qb->expr()->eq('to.memo01', 'tp.memo01'),
                $qb->expr()->eq('tp.memo03', $this->config['EXTERNAL_CREDIT_SSL_CODE']))
            );

        return $qb
            ->getQuery()
            ->getSingleScalarResult();

    }

    /**
     * スピード決済用 注文番号取得
     *
     * @param int $customerId
     *
     * @return $orderIdForSpeed
     */
    public function getOrderIdForSpeed($customerId)
    {

        if (is_null($customerId)) {
            return null;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('max(o.id)')
            ->from('\Eccube\Entity\Order', 'o')
            ->leftJoin('o.Payment', 'p')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment',  'to', 'WITH', 'o.id = to.id')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod', 'tp', 'WITH', 'p.id = tp.id')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('o.Customer', $customerId),
                $qb->expr()->eq('to.memo01', 'tp.memo01'),
                $qb->expr()->eq('tp.memo03', $this->config['EXTERNAL_CREDIT_SSL_CODE']))
            );

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find or record order method
     *
     * @param type $id
     * @return type
     */
    public function findRecord($id)
    {
        if ($id == 0) {
            return NULL;
        } else {
            $Order = $this->find($id);

            if (is_null($Order)) {

                $Order = new \Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment();
                $Order
                    ->setId($id);
            }
        }
        return $Order;
    }

    /**
     * 決済結果受け取り用 レコード取得
     *
     * @param $orderId
     * @param $sendId optional 
     * @return $orderIdForRecv
     */
    public function getOrderIdForRecv($orderId ,$sendId = null)
    {

        if (is_null($orderId)) {
            return null;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('o.id')
            ->from('\Eccube\Entity\Order', 'o')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment', 'to', 'WITH', 'o.id = to.id')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('o.id', $orderId))
            );

        if ($sendId != null) {
            $qb
                ->andWhere($qb->expr()->eq('to.memo09', ':code1'))
                ->setParameter('code1', $sendId);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * ユニークIDから注文データ取得
     *
     * @param $tpl_uniqid
     * @return $orderData
     */
    public function getOrderForCredit($tpl_uniqid)
    {

        if (is_null($tpl_uniqid)) {
            return null;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('o.id','to.memo01')
            ->from('\Eccube\Entity\Order', 'o')
            ->innerJoin('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment', 'to', 'WITH', 'o.id = to.id')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('o.pre_order_id', "'".$tpl_uniqid."'"))
            );

        return $qb
            ->getQuery()
            ->getResult();
    }

}