<?php




namespace Plugin\SimpleCoupon\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Monolog\Logger;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponCondition;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;
use Plugin\SimpleCoupon\Entity\ConditionProduct;
use Plugin\SimpleCoupon\Service\SimpleCouponCsvExportService;

class CouponController extends AbstractController
{

	public function index(Application $app, Request $request)
	{
		
		$session = $request->getSession();
		
		$builder = $app['form.factory']->createBuilder('plg_simplecoupon_admin_search_coupon');
		
		$searchForm = $builder->getForm();
		
		$pagination = array();
		
		$disps = $app['eccube.repository.master.disp']->findAll();
		$pageMaxis = $app['eccube.repository.master.page_max']->findAll();
		$page_count = $app['config']['default_page_count'];
		$page_status = null;
		$active = false;
		$page_no = $request->get("page_no");
		
		
		if ('POST' === $request->getMethod()) {
			
			$searchForm->handleRequest($request);
		
			if ($searchForm->isValid()) {
				
				$searchData = $searchForm->getData();
		
				// paginator
				$qb = $app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($searchData);
				
				$app->log('CouponController POST step 5 ', array(), Logger::INFO);
				
				$page_no = 1;
				$pagination = $app['paginator']()->paginate(
						$qb,
						$page_no,
						$page_count
						);
		
				// sessionのデータ保持
				$session->set('eccube.admin.plg_simple_coupon.coupon.search', $searchData);
				
			}
		} else {
			if (is_null($page_no)) {
				// sessionを削除
				$session->remove('eccube.admin.plg_simple_coupon.coupon.search');
				
			} else {
				// pagingなどの処理
				$searchData = $session->get('eccube.admin.plg_simple_coupon.coupon.search');
				
				if (!is_null($searchData)) {
		
					// 公開ステータス
					/*
					$status = $request->get('status');
					if (!empty($status)) {
						if ($status != $app['config']['admin_product_stock_status']) {
							$searchData['status']->clear();
							$searchData['status']->add($status);
						} else {
							$searchData['stock_status'] = $app['config']['disabled'];
						}
						$page_status = $status;
					}*/
					// 表示件数
					$pcount = $request->get('page_count');
		
					$page_count = empty($pcount) ? $page_count : $pcount;
		
					$qb = $app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($searchData);
					
					$pagination = $app['paginator']()->paginate(
							$qb,
							$page_no,
							$page_count
							);
					
					/*
					// セッションから検索条件を復元
					if (!empty($searchData['status'])) {
						$searchData['status'] = $app['eccube.repository.master.order_status']->find($searchData['status']);
					}*/
					
					
					$searchForm->setData($searchData);
				}
			}
		}
		
		foreach($pagination as $cp){
			$count = $app['eccube.plugin.simplecoupon.repository.coupon_order']->getCountByCoupon($cp);
			$cp->setOrderCount($count);
		}
		
		return $app->render('SimpleCoupon/Resource/template/admin/Coupon/index.twig', array(
				//'form' => $form->createView(),
				'searchForm' => $searchForm->createView(),
				'pagination' => $pagination,
				'disps' => $disps,
				'pageMaxis' => $pageMaxis,
				'page_no' => $page_no,
				'page_status' => $page_status,
				'page_count' => $page_count,
				'active' => $active,
		));
	}
	
	
	/**
	 * クーポンの新規作成/編集確定
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function edit(Application $app, Request $request, $id = null)
	{
		$Coupon = null;
		if (!$id) {
			// 新規登録
			$Coupon = new Coupon();
			$Coupon->setCouponCode($app['eccube.plugin.simplecoupon.service.coupon']->createCouponCode());
			$Coupon->setDiscountTargetType(Coupon::DISCOUNT_TARGET_TYPE_TOTAL);
			$Coupon->setCombinedUseFlg(Coupon::COMBINED_USE_FLG_DENY);
			$Coupon->setGuestUseFlg(Coupon::GUEST_USE_FLG_DENY);
			$Coupon->setOnetimeUseFlg(Coupon::ONETIME_USE_FLG_ONETIME);
			$Coupon->setNumberOfIssued(0);
			$Coupon->setBottomPrice(0);
			$Coupon->setConditionType(Coupon::CONDITION_TYPE_NONE);
			$Coupon->setConditionActionType(Coupon::CONDITION_ACTION_TYPE_ALLOW);
			$Coupon->setStatus(Coupon::STATUS_VALID);
			$Coupon->setDelFlg(Coupon::DEL_FLG_ACTIVE);
		} else {
			// 更新
			$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
			if (!$Coupon) {
				$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
	
				return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
			}
		}
	
		$builder2 = $app['form.factory']->createBuilder('plg_simplecoupon_admin_search_coupon');
		
		$builder = $app['form.factory']->createBuilder('plg_simplecoupon_admin_coupon', $Coupon);
		
		$form = $builder->getForm();
		
		// クーポンコードの発行
		
	
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$Coupon = $form->getData();
			if (!$id) {
				$Coupon->setConditionType(Coupon::CONDITION_TYPE_NONE);
			}
			$app['orm.em']->persist($Coupon);
			$app['orm.em']->flush();
			
			// 成功時のメッセージを登録する
			$app->addSuccess('admin.plugin.simplecoupon.regist.success', 'admin');
			
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		return $app->render('SimpleCoupon/Resource/template/admin/Coupon/edit.twig', array(
				'form' => $form->createView(),
				'id' => $id,
		));
	
	}
	
	
	/**
	 * クーポンの削除
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function delete(Application $app, Request $request, $id = null)
	{
		$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		if (!$Coupon) {
			$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		
		$Coupon->setDelFlg(1);
		$app['orm.em']->persist($Coupon);
		$app['orm.em']->flush();
		
		return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
	}
	
	
	/**
	 * クーポンの利用明細をCSV出力する
	 * 
	 * @param Application $app
	 * @param Request $request
	 * @param unknown $id
	 * @return StreamedResponse
	 */
	public function download_list_csv(Application $app, Request $request, $id = null)
	{
		$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		if (!$Coupon) {
			$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		
		// タイムアウトを無効にする.
		set_time_limit(0);
		
		// sql loggerを無効にする.
		$em = $app['orm.em'];
		$em->getConfiguration()->setSQLLogger(null);
		
		$response = new StreamedResponse();
		$response->setCallback(function () use ($app, $request, $Coupon) {
			
			// ヘッダ行の出力.
			$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->exportUsedCouponHeader();
			
			// クーポン利用データ検索用のクエリビルダを取得.
			$qb = $app['eccube.plugin.simplecoupon.service.coupon_csv_export']->getUsedCouponQueryBuilder($request, $Coupon);
			
			// データ行の出力.
			$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->setExportQueryBuilder($qb);
			$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->exportData(function ($entity, $csvService) use ($app, $request) {
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
			
			
		});
		
		$now = new \DateTime();
		$filename = 'coupon_' . $Coupon->getCouponId() . '_' . $now->format('YmdHis') . '.csv';
		$response->headers->set('Content-Type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
		$response->send();
		
		return $response;
			
	}
	
	
	/**
	 * クーポンの日別集計をCSV出力する
	 * 
	 * @param Application $app
	 * @param Request $request
	 * @param unknown $id
	 * @return StreamedResponse
	 */
	public function download_daily_csv(Application $app, Request $request, $id = null){
		$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		if (!$Coupon) {
			$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		
		// タイムアウトを無効にする.
		set_time_limit(0);
		
		// sql loggerを無効にする.
		$em = $app['orm.em'];
		$em->getConfiguration()->setSQLLogger(null);
		
		$response = new StreamedResponse();
		$response->setCallback(function () use ($app, $request, $Coupon, $em) {
				
			// ヘッダ行の出力.
			$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->exportDailyCouponHeader();
				
			// クーポン利用データ検索用のクエリビルダを取得.
			$qb = $app['eccube.plugin.simplecoupon.service.coupon_csv_export']->getUsedCouponQueryBuilder($request, $Coupon);
				
			// データ行の出力.
			$query = $qb->getQuery();
			$current_day = "";
			$count = 0;
			$total_discount_price = 0;
			$isFirst = true;
			$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->fopen();
			foreach ($query->getResult() as $iteratableResult) {
				
				$CouponOrder = $iteratableResult;
				if(!is_null($CouponOrder->getCreateDate())){
					$day = $CouponOrder->getCreateDate()->format('Y-m-d');
				}else{
					$day = null;
				}
				
				if($current_day != $day){
					if($isFirst){
						$isFirst = false;
					}else{
						$row = array();
						$row[] = $current_day;
						$row[] = $Coupon->getCouponCode();
						$row[] = $count;
						$row[] = $total_discount_price;
						$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->fputcsv($row);
					}
					$current_day = $day;
					$count = 0;
					$total_discount_price = 0;
				}
				$count ++;
				$total_discount_price += $CouponOrder->getDiscountPrice();
				
				$em->detach($iteratableResult);
				$em->clear();
				$query->free();
				flush();
			}
			if(!$isFirst){
				//最終行分を出力
				$row = array();
				$row[] = $current_day;
				$row[] = $Coupon->getCouponCode();
				$row[] = $count;
				$row[] = $total_discount_price;
				$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->fputcsv($row);
				$app['eccube.plugin.simplecoupon.service.coupon_csv_export']->fclose();
			}
			
		});
		
		$now = new \DateTime();
		$filename = 'coupon_' . $Coupon->getCouponId() . '_daily_' . $now->format('YmdHis') . '.csv';
		$response->headers->set('Content-Type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
		$response->send();
		
		return $response;
	}
	
	
	/**
	 * クーポンの利用条件を登録する
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function edit_condition(Application $app, Request $request, $id = null)
	{
		$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		if (!$Coupon) {
			$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		$Condition = new CouponCondition();
		$Condition->setCouponId($id);
		$Condition->setCouponCode($Coupon->getCouponCode());
		$Condition->setConditionType($Coupon->getConditionType());
		$Condition->setConditionActionType($Coupon->getConditionActionType());
		
		
		$builder = $app['form.factory']->createBuilder('plg_simplecoupon_admin_coupon_condition', $Condition);
		
		$form = $builder->getForm();
		
		if ('POST' === $request->getMethod()) {
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				//
				$Condition = $form->getData();

				$Coupon->setConditionActionType($Condition->getConditionActionType());

				if($Condition->getConditionType() == Coupon::CONDITION_TYPE_CUSTOMER_ID){
					//会員ID指定
					$Coupon->setConditionType(Coupon::CONDITION_TYPE_CUSTOMER_ID);
					$app['orm.em']->persist($Coupon);
					
					$isSuccess = false;
					$isInvalid = false;
					
					if(strlen($Condition->getTargetCustomer())>0){
						
						$customer_list = explode(",", $Condition->getTargetCustomer());
						
						$repository = $app['eccube.plugin.simplecoupon.repository.condition_customer'];
						$repoCustomer = $app['eccube.repository.customer'];
						
						foreach($customer_list as $customer_id){
							
							if(is_numeric($customer_id)){
								$ret = $repository->findOneBy(array('couponId'=>$id, 'customerId'=>$customer_id));
								if(is_null($ret)){
									$Customer = $repoCustomer->findOneBy(array('id' => $customer_id));
									if($Customer){
										$ConditionCustomer = new ConditionCustomer();
										$ConditionCustomer->setCouponId($id);
										$ConditionCustomer->setCustomerId($customer_id);
										$ConditionCustomer->setCustomer($Customer);
										$app['orm.em']->persist($ConditionCustomer);
										
										$isSuccess = true;
									}else{
										$isInvalid = true;
									}
								}
							}else{
								$isInvalid = true;
							}
							
						}
					}else{
						$isSuccess = true;
					}
					$app['orm.em']->flush();
					$app['db']->delete('plg_simple_coupon_condition_product' ,  array('coupon_id' => $id));
					
					// 成功時のメッセージを登録する
					if($isSuccess){
						$app->addSuccess('admin.plugin.simplecoupon.regist_condition.success', 'admin');
					}else{
						if($isInvalid){
							$app->addError('admin.plugin.simplecoupon.regist_condition.invalid', 'admin');
						}else{
							$app->addError('admin.plugin.simplecoupon.regist_condition.exists', 'admin');
						}
					}
						
					$Condition->setTargetCustomer("");

				}
				else if($Condition->getConditionType() == Coupon::CONDITION_TYPE_PRODUCT){

					//商品指定
					$Coupon->setConditionType(Coupon::CONDITION_TYPE_PRODUCT);
					$app['orm.em']->persist($Coupon);
					
					$isSuccess = false;
					$isInvalid = false;
					
					$repository = $app['eccube.plugin.simplecoupon.repository.condition_product'];
					$repoProduct = $app['eccube.repository.product'];
					$repoProductClass = $app['eccube.repository.product_class'];

					if(strlen($Condition->getTargetProduct())>0){
						$product_list = explode(",", $Condition->getTargetProduct());
						foreach($product_list as $product_id){
							if(is_numeric($product_id)){
								$Product = $repoProduct->findOneBy(array('id'=>$product_id));

								if($Product){
									$class_list = $Product->getProductClasses();
									
									foreach($class_list as $ProductClass){
										$ret = $repository->findOneBy(array('couponId'=>$id, 'productId'=>$product_id, 'productClassId'=>$ProductClass->getId()));
										if(is_null($ret)){
											$ConditionProduct = new ConditionProduct();
											$ConditionProduct->setCouponId($id);
											$ConditionProduct->setProductId($product_id);
											$ConditionProduct->setProduct($Product);
											$ConditionProduct->setProductClassId($ProductClass->getId());
											$ConditionProduct->setProductClass($ProductClass);


											$app['orm.em']->persist($ConditionProduct);
											$isSuccess = true;
										}
									}
								}else{
									$isInvalid = true;
								}
								
							}else{
								$isInvalid = true;
							}
							
						}
					}else{
						$isSuccess = true;
					}
					$app['orm.em']->flush();
					$app['db']->delete('plg_simple_coupon_condition_customer' ,  array('coupon_id' => $id));
					$del_conditions = $repository->findBy(array('couponId' => $id, 'productId'=>null));
					foreach($del_conditions as $product_condition){
						if($product_condition->getProductId() == null || $product_condition->getProductId()==""){
							$app['orm.em']->remove($product_condition);
						}
					}
					$app['orm.em']->flush();

					
					// 成功時のメッセージを登録する
					if($isSuccess){
						$app->addSuccess('admin.plugin.simplecoupon.regist_condition.success', 'admin');
					}else{
						if($isInvalid){
							$app->addError('admin.plugin.simplecoupon.regist_condition.invalid_product', 'admin');
						}else{
							$app->addError('admin.plugin.simplecoupon.regist_condition.exists_product', 'admin');
						}
					}
					$Condition->setTargetProduct("");
					
				}
				else if($Condition->getConditionType() == Coupon::CONDITION_TYPE_CATEGORY){
					
					//カテゴリ指定
					$Coupon->setConditionType(Coupon::CONDITION_TYPE_CATEGORY);
					$app['orm.em']->persist($Coupon);
					
					$isSuccess = false;
					$isInvalid = false;
					
					$repository = $app['eccube.plugin.simplecoupon.repository.condition_product'];
					$repoCategory = $app['eccube.repository.category'];

					if(strlen($Condition->getTargetCategory())>0){
						$category_list = explode(",", $Condition->getTargetCategory());
						
						foreach($category_list as $category_id){
							if(is_numeric($category_id)){
								$Category = $repoCategory->findOneBy(array('id'=>$category_id));

								if($Category){
									$ret = $repository->findOneBy(array('couponId'=>$id, 'categoryId'=>$category_id));
									if(is_null($ret)){
										$ConditionProduct = new ConditionProduct();
										$ConditionProduct->setCouponId($id);
										$ConditionProduct->setCategoryId($category_id);
										$ConditionProduct->setCategory($Category);

										$app['orm.em']->persist($ConditionProduct);
										$isSuccess = true;
									}
								}else{
									$isInvalid = true;
								}
								
							}else{
								$isInvalid = true;
							}
							
						}
					}else{
						$isSuccess = true;
					}
					$app['orm.em']->flush();
					$app['db']->delete('plg_simple_coupon_condition_customer' ,  array('coupon_id' => $id));
					$del_conditions = $repository->findBy(array('couponId' => $id, 'categoryId'=>null));
					foreach($del_conditions as $product_condition){
						if($product_condition->getCategoryId() == null || $product_condition->getCategoryId()==""){
							$app['orm.em']->remove($product_condition);
						}
					}
					$app['orm.em']->flush();
					
					// 成功時のメッセージを登録する
					if($isSuccess){
						$app->addSuccess('admin.plugin.simplecoupon.regist_condition.success', 'admin');
					}else{
						if($isInvalid){
							$app->addError('admin.plugin.simplecoupon.regist_condition.invalid_category', 'admin');
						}else{
							$app->addError('admin.plugin.simplecoupon.regist_condition.exists_category', 'admin');
						}
					}
					$Condition->setTargetCategory("");
					
				}
				else{
					//条件なし
					$Coupon->setConditionType(Coupon::CONDITION_TYPE_NONE);
					$app['orm.em']->persist($Coupon);
					$app['orm.em']->flush();
					$app['db']->delete('plg_simple_coupon_condition_customer' ,  array('coupon_id' => $id));
					$app['db']->delete('plg_simple_coupon_condition_product' ,  array('coupon_id' => $id));
					
					// 成功時のメッセージを登録する
					$app->addSuccess('admin.plugin.simplecoupon.regist_condition.success', 'admin');
				}
				
				$builder2 = $app['form.factory']->createBuilder('plg_simplecoupon_admin_coupon_condition', $Condition);
				
				$form = $builder2->getForm();
				
			}
			
		}else{
			
		}

		
		//ユーザ条件を検索する
		$disps = $app['eccube.repository.master.disp']->findAll();
		$pageMaxis = $app['eccube.repository.master.page_max']->findAll();
		$page_count = $app['config']['default_page_count'];
		$page_no = $request->get("page_no",1);
		$searchData = array('coupon_id'=>$id);

		$pagination = null;
		$paginationProduct = null;
		$paginationCategory = null;


		if($Condition->getConditionType() == Coupon::CONDITION_TYPE_CUSTOMER_ID){
		
			$qb = $app['eccube.plugin.simplecoupon.repository.condition_customer']->getQueryBuilderBySearchDataForAdmin($searchData);
			$pagination = $app['paginator']()->paginate(
				$qb,
				$page_no,
				$page_count
				);

		}
		else if($Condition->getConditionType() == Coupon::CONDITION_TYPE_PRODUCT){

			$qb = $app['eccube.plugin.simplecoupon.repository.condition_product']->getQueryBuilderBySearchDataForAdmin($searchData);
			$paginationProduct = $app['paginator']()->paginate(
				$qb,
				$page_no,
				$page_count
				);

		}
		else if($Condition->getConditionType() == Coupon::CONDITION_TYPE_CATEGORY){
			
				$qb = $app['eccube.plugin.simplecoupon.repository.condition_product']->getQueryBuilderBySearchDataForAdminCategory($searchData);
				$paginationCategory = $app['paginator']()->paginate(
					$qb,
					$page_no,
					$page_count
					);
	
			}
		

		#$page_no = 1;

		
		//pager.twigに渡す追加パラメータ
		$request->query->add(array('id'=>$id));
		
		return $app->render('SimpleCoupon/Resource/template/admin/Coupon/edit_condition.twig', array(
				'form' => $form->createView(),
				'id' => $id,
				'Coupon' => $Coupon,
				//'searchForm' => $searchForm->createView(),
				'pagination' => $pagination,
				'paginationProduct' => $paginationProduct,
				'paginationCategory' => $paginationCategory,
				'disps' => $disps,
				'pageMaxis' => $pageMaxis,
				'page_no' => $page_no,
				//'page_status' => $page_status,
				'page_count' => $page_count,
		));
		
	}
	
	
	/**
	 * クーポンの利用条件を削除する
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function edit_condition_customer_delete(Application $app, Request $request, $id = null, $customer_id = null)
	{
		$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		if (!$Coupon) {
			$app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		}
		
		$ConditionCustomer =  $app['eccube.plugin.simplecoupon.repository.condition_customer']->findOneBy(array('couponId'=>$id, 'customerId'=>$customer_id));
		if($ConditionCustomer){
			$app['orm.em']->remove($ConditionCustomer);
			$app['orm.em']->flush();
		}
		return $app->redirect($app->url('plg_simplecoupon_admin_coupon_edit_condition', array('id'=>$id)));
		
	}


	/**
	 * クーポンの利用条件を削除する
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	 public function edit_condition_product_delete(Application $app, Request $request, $id = null, $product_class_id = null)
	 {
		 $Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		 if (!$Coupon) {
			 $app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			 return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		 }
		 
		 $ConditionProduct =  $app['eccube.plugin.simplecoupon.repository.condition_product']->findOneBy(array('couponId'=>$id, 'productClassId'=>$product_class_id));
		 if($ConditionProduct){
			 $app['orm.em']->remove($ConditionProduct);
			 $app['orm.em']->flush();
		 }
		 return $app->redirect($app->url('plg_simplecoupon_admin_coupon_edit_condition', array('id'=>$id)));
		 
	 }


	 /**
	 * クーポンの利用条件を削除する
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	 public function edit_condition_category_delete(Application $app, Request $request, $id = null, $category_id = null)
	 {
		 $Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->find($id);
		 if (!$Coupon) {
			 $app->addError('admin.plugin.simplecoupon.coupon.notfound', 'admin');
			 return $app->redirect($app->url('plg_simplecoupon_admin_coupon_list'));
		 }
		 
		 $ConditionProduct =  $app['eccube.plugin.simplecoupon.repository.condition_product']->findOneBy(array('couponId'=>$id, 'categoryId'=>$category_id));
		 if($ConditionProduct){
			 $app['orm.em']->remove($ConditionProduct);
			 $app['orm.em']->flush();
		 }
		 return $app->redirect($app->url('plg_simplecoupon_admin_coupon_edit_condition', array('id'=>$id)));
		 
	 }

}
