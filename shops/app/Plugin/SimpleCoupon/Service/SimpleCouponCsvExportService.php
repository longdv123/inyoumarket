<?php
/*
 */


namespace Plugin\SimpleCoupon\Service;

use Eccube\Common\Constant;
use Eccube\Util\EntityUtil;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

use Plugin\SimpleCoupon\Service\CrlfStreamFilter;

class SimpleCouponCsvExportService 
{
	/**
	 * @var
	 */
	protected $app;
	
    /**
     * @var
     */
    protected $fp;

    /**
     * @var
     */
    protected $closed = false;

    /**
     * @var \Closure
     */
    protected $convertEncodingCallBack;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\QueryBuilder;
     */
    protected $qb;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Plugin\SimpleCoupon\Repository\CouponOrderRepository
     */
    protected $couponOrderRepository;

    /**
     * @param $app
     */
    public function setApp($app)
    {
    	$this->app = $app;
    }
    
    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param \Plugin\SimpleCoupon\Repository\CouponOrderRepository $couponOrderRepository
     */
    public function setCouponOrderRepository(\Plugin\SimpleCoupon\Repository\CouponOrderRepository $couponOrderRepository)
    {
        $this->couponOrderRepository = $couponOrderRepository;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function setExportQueryBuilder(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->qb = $qb;
    }
    
    public function exportUsedCouponHeader()
    {
    	$row = array();
    	$row[] = "クーポン利用ID";
    	$row[] = "クーポンコード";
    	$row[] = "値引き金額";
    	$row[] = "注文ID";
    	$row[] = "注文日時";
    	$row[] = "注文年";
    	$row[] = "注文年月";
    	$row[] = "注文年月日";
    	
    	$this->fopen();
    	$this->fputcsv($row);
    	$this->fclose();
    }
    
    public function exportDailyCouponHeader()
    {
    	$row = array();
    	$row[] = "日付";
    	$row[] = "クーポンコード";
    	$row[] = "利用数";
    	$row[] = "値引き金額";
    	 
    	$this->fopen();
    	$this->fputcsv($row);
    	$this->fclose();
    }

    public function exportMonthlyCouponHeader()
    {
    	$row = array();
    	$row[] = "月";
    	$row[] = "クーポンコード";
    	$row[] = "利用数";
    	$row[] = "値引き金額";
    
    	$this->fopen();
    	$this->fputcsv($row);
    	$this->fclose();
    }
    
    /**
     * クエリビルダにもとづいてデータ行を出力する.
     * このメソッドを使う場合は, 事前にsetExportQueryBuilder($qb)で出力対象のクエリビルダをわたしておく必要がある.
     *
     * @param \Closure $closure
     */
    public function exportData(\Closure $closure)
    {
        if (is_null($this->qb) || is_null($this->em)) {
            throw new \LogicException('query builder not set.');
        }

        $this->fopen();
		$query = $this->qb->getQuery();
        foreach ($query->getResult() as $iteratableResult) {
        	$closure($iteratableResult, $this);
            $this->em->detach($iteratableResult);
            $this->em->clear();
            $query->free();
            flush();
        }

        $this->fclose();
    }    

    /**
     * 文字エンコーディングの変換を行うコールバック関数を返す.
     *
     * @return \Closure
     */
    public function getConvertEncodhingCallback()
    {
        $config = $this->config;

        return function ($value) use ($config) {
            return mb_convert_encoding(
                (string) $value, $config['csv_export_encoding'], 'UTF-8'
            );
        };
    }

    /**
     *
     */
    public function fopen()
    {
        if (is_null($this->fp) || $this->closed) {
        	stream_filter_register('crlf', '\Plugin\SimpleCoupon\Service\CrlfStreamFilter');
            $this->fp = fopen('php://output', 'w');
            stream_filter_append($this->fp, 'crlf');
        }
    }

    /**
     * @param $row
     * @param null $callback
     */
    public function fputcsv($row)
    {
        if (is_null($this->convertEncodingCallBack)) {
            $this->convertEncodingCallBack = $this->getConvertEncodhingCallback();
        }

        fputcsv($this->fp, array_map($this->convertEncodingCallBack, $row), $this->config['csv_export_separator']);
    }

    /**
     *
     */
    public function fclose()
    {
        if (!$this->closed) {
            fclose($this->fp);
            $this->closed = true;
        }
    }

    
    public function getUsedCouponQueryBuilder(Request $request, $Coupon)
    {
    	// クーポン利用情報のクエリビルダを構築.
    	$qb = $this->couponOrderRepository->getQueryBuilderByCouponForAdmin($Coupon);
    	return $qb;
    }


}
