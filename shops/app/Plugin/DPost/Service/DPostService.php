<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Service;


class DPostService {

    /** @var $app \Eccube\Application */
    public $app;

    public function __construct(\Eccube\Application $app) {

        $this->app = $app;
    }

    public function getCustomerEx($customer_id) {

        /* @var $CustomerEx \Plugin\DPost\Entity\CustomerEx */
        $CustomerEx = $this->app['eccube.repository.customerex']->findOneBy(array('customer_id' => $customer_id));

        return $CustomerEx;
    }

    public function getRegistId($customer_id) {

        /* @var $CustomerEx \Plugin\DPost\Entity\CustomerEx */
        $CustomerEx = $this->getCustomerEx($customer_id);

        if(empty($CustomerEx)) {
            return "";
        } else {
            return $CustomerEx->getRegistId();
        }
    }

    public function getPushCount() {

        $customerExList = $this->app['eccube.repository.customerex']->findAll();

        return count($customerExList);
    }

    public function getPushProductCustomer($product_id) {

        $ProductPostQueueList = $this->app['eccube.repository.productpostqueue']->getRegistList($product_id);

        $PostQueueList = array();
        $index = 0;
        $i = 0;

        /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
        foreach ($ProductPostQueueList as $ProductPostQueue) {

            $regist_id = $ProductPostQueue->getRegistId();

            // 500人単位で送信
            $PostQueueList[$index]['regist_id'][] = $regist_id;
            $PostQueueList[$index]['product_id'][] = $product_id;
            $i++;

            if($i == 500) {
                $i = 0;
                $index++;
            }
        }

        return $PostQueueList;
    }

    public function getPushCustomer($news_id) {

        $CustomerExList = $this->app['eccube.repository.customerex']->getRegistList();

        $PostQueueList = array();
        $index = 0;
        $i = 0;

        /* @var $CustomerEx \Plugin\DPost\Entity\CustomerEx */
        foreach ($CustomerExList as $CustomerEx) {

            $regist_id = $CustomerEx->getRegistId();

            /* @var $PostQueue \Plugin\DPost\Entity\PostQueue */
            $PostQueue = $this->app['eccube.repository.postqueue']->findOneBy(array('regist_id' => $regist_id, 'news_id' => $news_id));

            if(!empty($PostQueue)) {
                // 送信済のユーザには送らない
                continue;
            }

            // 500人単位で送信
            $PostQueueList[$index]['regist_id'][] = $regist_id;
            $PostQueueList[$index]['customer_id'][] = $CustomerEx->getCustomerId();
            $i++;

            if($i == 500) {
                $i = 0;
                $index++;
            }
        }

        return $PostQueueList;
    }

    public function getProductPushMessage($regist_id) {

        $PostQueueList = $this->app['eccube.repository.productpostqueue']
                        ->findBy(array('regist_id' => $regist_id, 'queue_status' => 2), array('update_date' => 'ASC'));

        return $PostQueueList;
    }

    public function getPushMessage($regist_id) {

        $PostQueueList = $this->app['eccube.repository.postqueue']
                        ->findBy(array('regist_id' => $regist_id, 'queue_status' => 0), array('create_date' => 'ASC'));

        return $PostQueueList;
    }

    public function getProductPostQueueSend($regist_id, $product_id) {

        $PostQueueList = $this->app['eccube.repository.productpostqueue']
                            ->findBy(array('regist_id' => $regist_id, 'product_id' => $product_id, 'queue_status' => 3));

        return $PostQueueList;
    }

    public function getPostQueue($regist_id, $news_id) {

        $PostQueueList = $this->app['eccube.repository.postqueue']
                            ->findBy(array('regist_id' => $regist_id, 'news_id' => $news_id));

        return $PostQueueList;
    }

    public function getNews($news_id) {

        $News = $this->app['eccube.repository.news']->findOneById($news_id);

        return $News;
    }

    public function getNewsEx($news_id) {

        $NewsEx = $this->app['eccube.repository.newsex']->findOneBy(array('news_id' => $news_id));

        return $NewsEx;
    }

    public function getProductClickCount($product_id) {

        $clickCount = $this->app['eccube.repository.productpostqueue']->getClickCount($product_id);

        return $clickCount;
    }

    public function getClickCount($news_id) {

        $clickCount = $this->app['eccube.repository.postqueue']->getClickCount($news_id);

        return $clickCount;
    }

    public function getProductPush($product_id) {

        $ProductPush = $this->app['eccube.repository.productpush']->findOneBy(array('product_id' => $product_id));

        return $ProductPush;
    }

    public function getNoStock($product_id) {

        /* @var $em \Doctrine\ORM\EntityRepository */
        $em = $this->app['orm.em'];

        $where = 'p.id = :product_id';
        $where .= ' and pc.del_flg = 0';
        $where .= ' and pc.stock_unlimited = 0';
        $where .= ' and ps.stock = 0';

        $qb = $em->createQueryBuilder('pc')
                ->select('pc')
                ->from('Eccube\Entity\ProductClass', 'pc')
                ->innerJoin('pc.Product', 'p')
                ->innerJoin('pc.ProductStock', 'ps')
                ->where($where)
                ->setParameter(':product_id', $product_id)
                ->getQuery();

        $ProductClass = $qb->getResult();

        return $ProductClass;
    }

    public function isProductPostQueueExists($regist_id, $product_id) {

        $ProductPostQueues = $this->app['eccube.repository.productpostqueue']
                                    ->getSingleResult($regist_id, $product_id);
        $count = 0;
        if (!empty($ProductPostQueues)) {
            $count = 1;
        }

        return ($count == 0 ? false : true);
    }

    public function getProductPostQueue($regist_id, $product_id) {

        $ProductPostQueues = $this->app['eccube.repository.productpostqueue']
                        ->getSingleResult($regist_id, $product_id);

        if(!empty($ProductPostQueues)) {
            return $ProductPostQueues[0];
        } else {
            return array();
        }
    }

    public function getRegistIdCount($regist_id) {
        return $this->app['eccube.repository.customerex']->getRegistIdCount($regist_id);
    }

    public function getRegistIdCountProduct($regist_id) {
        return $this->app['eccube.repository.productpostqueue']->getRegistIdCount($regist_id);
    }
}
