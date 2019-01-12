<?php

namespace Plugin\SimpleCoupon\Tests\Service;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Plugin\SimpleCoupon\Service\SimpleCouponCsvExportService;
use org\bovigo\vfs\vfsStream;


/**
 * Class SimpleCouponCsvExportServiceTest
 *
 * @package Plugin\SimpleCoupon\Tests\Service
 */
class SimpleCouponCsvExportServiceTest extends SimpleCouponTestBase
{

	protected $url;
	protected $ExportService;
	
	public function setUp() {
		parent::setUp();
		
		$root = vfsStream::setup('rootDir');
		$this->url = vfsStream::url('rootDir/test.csv');
		
		//Exportサービスをセットアップ
		$this->ExportService = new SimpleCouponCsvExportService();
		$this->ExportService->setEntityManager($this->app['orm.em']);
		$this->ExportService->setApp($this->app);
		$this->ExportService->setConfig($this->app['config']);
		$this->ExportService->setCouponOrderRepository($this->app['eccube.plugin.simplecoupon.repository.coupon_order']);
		
		// CsvExportService のファイルポインタを Vfs のファイルポインタにしておく
		$objReflect = new \ReflectionClass($this->ExportService);
		$Property = $objReflect->getProperty('fp');
		$Property->setAccessible(true);
		$Property->setValue($this->ExportService, fopen($this->url, 'w'));
		
	}
	
	public function testExportUsedCouponHeader()
    {
    	//Execute Test Target Method
    	$this->ExportService->exportUsedCouponHeader();
    	
    	//Assertion
        $arrHeader = explode(',', file_get_contents($this->url));
        // Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
        $this->expected = 8;
        $this->actual = count($arrHeader);
        $this->verify();
    }
    
    public function testExportDailyCouponHeader()
    {
    	//Execute Test Target Method
    	$this->ExportService->exportDailyCouponHeader();
    	 
    	//Assertion
    	$arrHeader = explode(',', file_get_contents($this->url));
    	// Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
    	$this->expected = 4;
    	$this->actual = count($arrHeader);
    	$this->verify();
    }
    
    public function testExportMonthlyCouponHeader()
    {
    	//Execute Test Target Method
    	$this->ExportService->exportMonthlyCouponHeader();
    
    	//Assertion
    	$arrHeader = explode(',', file_get_contents($this->url));
    	// Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
    	$this->expected = 4;
    	$this->actual = count($arrHeader);
    	$this->verify();
    }
    
    public function testExportData()
    {
    	//Data Preperation For This Test
    	$Customer = $this->createCustomer();
    	$this->app['security.token_storage']->setToken(
    		new UsernamePasswordToken(
    			$Customer, null, 'customer', $Customer->getRoles()
  			)
    	);
    	$Order1 = $this->createOrder($Customer);
    	$Order2 = $this->createOrder($Customer);
    	$Order3 = $this->createOrder($Customer);
    	
    	$Coupon1 = $this->createCoupon();
    	$Coupon2 = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon1);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    	
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order1, $Coupon1);
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order1->getId());
    	foreach($list as $CouponOrder){
    		$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    		$this->app['orm.em']->persist($CouponOrder);
    	}
    	$this->app['orm.em']->flush();
    	
    	
    	$qb = $this->ExportService->getUsedCouponQueryBuilder(new \Symfony\Component\HttpFoundation\Request(), $Coupon1);
    	$this->ExportService->setExportQueryBuilder($qb);
    	
    	//Execute Test Target Method
    	$this->ExportService->exportData(function ($entity, $csvService) {
    		$CouponOrder = $entity;
    		$row = array();
    		$row[] = $CouponOrder->getCouponOrderId();
    		$row[] = $CouponOrder->getCoupon()->getCouponCode();
    		$row[] = $CouponOrder->getDiscountPrice();
    		$row[] = $CouponOrder->getOrderId();
    		if(!is_null($CouponOrder->getCreateDate())){
    			$row[] = $CouponOrder->getCreateDate()->format('Y-m-d H:i:s');
    			$row[] = $CouponOrder->getCreateDate()->format('Y');
    			$row[] = $CouponOrder->getCreateDate()->format('Y-m');
    			$row[] = $CouponOrder->getCreateDate()->format('Y-m-d');
    		}else{
    			$row[] = "";
    			$row[] = "";
    			$row[] = "";
    			$row[] = "";
    		}
    		$csvService->fputcsv($row);
    	});
    
    	//Assertion
    	$Result = $qb->getQuery()->getResult();
    	$File = array_map('str_getcsv', file($this->url));
    	// Vfs に出力すると日本語が化けてしまうようなので, カウントのみ比較
    	$this->expected = 1;//count($Result);
    	$this->actual = count($File);
    	$this->verify();
    	
    }
    
}