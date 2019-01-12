<?php
/*
* Plugin Name : SimpleCoupon
*
*/

namespace Plugin\SimpleCoupon;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;

class PluginManager extends AbstractPluginManager
{

    /**
     * Image folder path (cop source)
     * @var type
     */
    protected $imgSrc;
    /**
     *Image folder path (copy destination)
     * @var type
     */
    protected $imgDst;

    public function __construct()
    {
    }

    public function install($config, $app)
    {
    	$this->migrationSchema($app, __DIR__ . '/Migration', $config['code']);
        
    }

    public function uninstall($config, $app)
    {     
    	$this->migrationSchema($app, __DIR__ . '/Migration', $config['code'], 0);
    }

    public function enable($config, $app)
    {
    	//ページレイアウト情報の登録
    	$PageLayout = $app['eccube.repository.page_layout']->findOneBy(array('url' => 'plg_simplecoupon_front_coupon'));
    	if (is_null($PageLayout)) {
    		// pagelayoutの作成
    		$this->createPageLayout($app);
    	}
    	
    	//クーポン支払い方法の登録
    	$this->createPayment($app);
    	
    	
    }

    public function disable($config, $app)
    {
    	// pagelayoutの削除
    	$this->removePageLayout($app);
    	
    	//クーポン支払い方法の削除
    	$this->removePayment($app);
    	
    }

    public function update($config, $app)
    {
    	$this->migrationSchema($app, __DIR__ . '/Migration', $config['code']);
    }
    
    
    /**
     * クーポン用ページレイアウトを作成
     *
     * @param $app
     * @throws \Exception
     */
    private function createPageLayout($app)
    {
    
    	// ページレイアウトにプラグイン使用時の値を代入
    	$DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
    
    	/** @var \Eccube\Entity\PageLayout $PageLayout */
    	$PageLayout = $app['eccube.repository.page_layout']->findOrCreate(null, $DeviceType);
    
    	$PageLayout->setEditFlg(PageLayout::EDIT_FLG_DEFAULT);
    	$PageLayout->setName('SimpleCouponプラグイン/クーポン利用登録');
    	$PageLayout->setUrl('plg_simplecoupon_front_coupon');
    	$PageLayout->setFileName('../../Plugin/SimpleCoupon/Resource/template/default/Simplecoupon/index');
    	$PageLayout->setMetaRobots('noindex');
    
    	// DB登録
    	$app['orm.em']->persist($PageLayout);
    	$app['orm.em']->flush($PageLayout);
    
    }
    
    
    /**
     * クーポン用ページレイアウトを削除
     *
     * @param $app
     * @throws \Exception
     */
    private function removePageLayout($app)
    {
    	// ページ情報の削除
    	$PageLayout = $app['eccube.repository.page_layout']->findOneBy(array('url' => 'plg_simplecoupon_front_coupon'));
    
    	if ($PageLayout) {
    		// Blockの削除
    		$app['orm.em']->remove($PageLayout);
    		$app['orm.em']->flush($PageLayout);
    	}
    
    }
    
    /**
     * クーポン用設定レコードを作成
     *
     * @param $app
     * @throws \Exception
     */
    private function createConfig($app)
    {
    	//設定レコードを取得
    	$Config = $app['eccube.plugin.simplecoupon.repository.config']->findOrCreate();
    
    	// DB登録
    	$app['orm.em']->persist($Config);
    	$app['orm.em']->flush($Config);
    
    }
    
    /**
     * クーポン用設定レコードを削除
     *
     * @param $app
     * @throws \Exception
     */
    private function removeConfig($app)
    {
    	// 設定情報の削除
    	$Repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\Config');
    	$list = $Repo->findAll();
    	if (count($list)>0) {
    		foreach($list as $Config){
    			$app['orm.em']->remove($Config);
    			$app['orm.em']->flush($Config);
    		}
    	}
    }
    
    
    /**
     * クーポン用無料決済方法を作成
     *
     * @param $app
     * @throws \Exception
     */
    private function createPayment($app)
    {
		//Configを取得
		$Repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\Config');
    	$Config = $Repo->findOrCreate();
    	
    	if(is_null($Config->getCouponPaymentId())){
    		//Paymentレコードを新規作成
    		$Payment = $this->newPayment($app);
    	}else{
    		//Paymentレコードがあるかどうか確認する。なければ、再作成する。
    		$payment_id = $Config->getCouponPaymentId();
    		$Payment = $app['eccube.repository.payment']->find($payment_id);
    		if(is_null($Payment)){
    			$Payment = $this->newPayment($app);
    		}
    	}
    	$Config->setCouponPaymentId($Payment->getId());
    	
    	// DB登録
    	$app['orm.em']->persist($Config);
    	$app['orm.em']->flush();
    
    }
    
    
    /**
     * クーポン用無料決済方法をを削除
     *
     * @param $app
     * @throws \Exception
     */
    private function removePayment($app)
    {
    	//Configを取得
    	$Repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\Config');
    	$list = $Repo->findAll();
    	if (count($list)>0) {
    		foreach($list as $Config){
    			$payment_id = $Config->getCouponPaymentId();
    			if(!is_null($payment_id)){
    				//payment_optionを削除
    				$option_list = $app['eccube.repository.payment_option']->findBy(array('payment_id' => $Config->getCouponPaymentId()));
    				foreach($option_list as $Option){
    					$app['orm.em']->remove($Option);
    					$app['orm.em']->flush($Option);
    				}
    				//注文のpayment_idもnullにする。
    				$app['db']->update('dtb_order' , array('payment_id' => null), array('payment_id' => $payment_id));
    				
    				//支払いマスタも削除
    				$app['db']->delete('dtb_payment' ,  array('payment_id' => $payment_id));
    				
    			}
    		}
    	}
    }
    
    
    /**
     * 
     * @return \Eccube\Entity\Payment
     */
    private function newPayment($app){
		$Creator = null;
		$Authority=null;
		$authority_list = $app['eccube.repository.master.authority'] ->findBy(
			array(
				'rank' => 0
			),
			array('id' => 'ASC')
		);
		if(count($authority_list)>0){
			$Authority = $authority_list[0];
		}

		if(!is_null($Authority)){
			$creator_list = $app['eccube.repository.member']->findBy(
				array(
					'Authority' => $Authority,
					'del_flg' => 0
				),
				array('id' => 'ASC')
			);
			if(count($creator_list)>0){
				$Creator = $creator_list[0];
			}
		}
		if(is_null($Creator)){
			$creator_list = $app['eccube.repository.member']->findBy(
				array(
					'del_flg' => 0
				),
				array('id' => 'ASC')
			);
			if(count($creator_list)>0){
				$Creator = $creator_list[0];
			}
		}
    	
    	$Payment = new \Eccube\Entity\Payment();
    	$Payment->setCreator($Creator);
    	$Payment->setMethod("クーポンのご利用のため無料");
    	$Payment->setCharge(0);
    	$Payment->setRuleMax(0);
    	$Payment->setRank(0);
    	$Payment->setFixFlg(0);
    	$Payment->setDelFlg(0);
    	$Payment->setChargeFlg(0);
    	$Payment->setRuleMin(0);
    	
    	$app['orm.em']->persist($Payment);
    	$app['orm.em']->flush($Payment);
    	
    	return $Payment;
    }
    
}