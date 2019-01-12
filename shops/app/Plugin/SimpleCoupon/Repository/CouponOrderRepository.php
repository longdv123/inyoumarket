<?php

namespace Plugin\SimpleCoupon\Repository;

use Doctrine\ORM\EntityRepository;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;

/**
 * CouponOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CouponOrderRepository extends EntityRepository
{
	
	protected $app;
	
	public function setApplication($app)
	{
		$this->app = $app;
	}
	
	public function getCountByCoupon(Coupon $Coupon)
	{
		return $this->createQueryBuilder('co')
		->select('COUNT(co.couponOrderId) as orderCount')
		->innerJoin('co.Order', 'o')
		->andWhere('co.Coupon = :coupon')
		->andWhere('o.del_flg = :order_del_flg')
		->andWhere('o.OrderStatus <> :order_status')
		->andWhere('o.OrderStatus <> :order_processing_status')
		->setParameter('coupon', $Coupon)
		->setParameter('order_del_flg',\Eccube\Common\Constant::DISABLED)
		->setParameter('order_status', $this->app['config']['order_cancel'])
		->setParameter('order_processing_status', $this->app['config']['order_processing'])
		->getQuery()
		->getSingleScalarResult();

		/*
		return $this->createQueryBuilder('co')
		->select('COUNT(co.couponOrderId) as orderCount')
		->innerJoin('co.Order', 'o')
		->andWhere('co.status = :status')
		->andWhere('co.Coupon = :coupon')
		->andWhere('o.del_flg = :order_del_flg')
		->andWhere('o.OrderStatus <> :order_status')
		->setParameter('status', CouponOrder::STATUS_COMPLETE)
		->setParameter('coupon', $Coupon)
		->setParameter('order_del_flg',\Eccube\Common\Constant::DISABLED)
		->setParameter('order_status', $this->app['config']['order_cancel'])
		->getQuery()
		->getSingleScalarResult();
		*/
	}
	
	public function getCouponListByOrder($orderId){
		
		$qb = $this->createQueryBuilder('co')
		->addSelect('c')
		->innerJoin('co.Coupon', 'c')
		->andWhere('co.orderId = :order_id')
		->orderBy('co.couponOrderId', 'DESC');

		
		$query = $qb->getQuery();
		$query->setParameters(array('order_id' => $orderId));
		$result = $query->getResult();
		
		return $result;
		
	}
	
	
	/**
	 * クーポン利用データ一覧のダウンロード用
	 * @param unknown $Coupon
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByCouponForAdmin($Coupon){
		
		$qb =  $this->createQueryBuilder('co')
		->addSelect('c')
		->innerJoin('co.Coupon', 'c')
		->innerJoin('co.Order', 'o')
		//->andWhere('co.status = :status')
		->andWhere('co.Coupon = :Coupon')
		->andWhere('o.del_flg = :order_del_flg')
		->andWhere('o.OrderStatus <> :order_status')
		->andWhere('o.OrderStatus <> :order_processing_status')
		//->setParameter('status', CouponOrder::STATUS_COMPLETE)
		->setParameter('Coupon', $Coupon)
		->setParameter('order_del_flg',\Eccube\Common\Constant::DISABLED)
		->setParameter('order_status', $this->app['config']['order_cancel'])
		->setParameter('order_processing_status', $this->app['config']['order_processing'])
		->orderBy('co.orderId', 'ASC')
		->addOrderBy('co.couponOrderId', 'ASC');
		return $qb;
		
	}
	
	
}