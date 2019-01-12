<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class DPost
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function adminContentNewsBefore() {
        // 別コントローラへリダイレクト
        header("Location: " . $this->app->url('admin_content_news_ex'));
        exit;
    }

    private function addMyPageMenu($RenderService, $active = false) {

        $className = "";
        if($active) {
            $className = "active";
        }

        $url = $this->app->url('mypage_dpost');

        $RenderService->insertAfter(array("//nav[@class='local_nav favorite'] //ul //li", 3),
                        "<li class='" . $className . "'><a href='" . $url . "'>通知設定</a></li>");

        return $RenderService;
    }

    public function renderAddMypageMenu(FilterResponseEvent $event) {

        if (!$this->app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        /* @var $RenderService \Plugin\DPost\Service\RenderService */
        $RenderService = $this->app['dpost.service.renderservice'];

        $request = $event->getRequest();
        $response = $event->getResponse();

        $RenderService->initRenderControl($response);

        $RenderService = $this->addMyPageMenu($RenderService);

        $response->setContent($RenderService->getContent());
    }

    public function renderAddMypageMenuActive(FilterResponseEvent $event) {

        if (!$this->app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        /* @var $RenderService \Plugin\DPost\Service\RenderService */
        $RenderService = $this->app['dpost.service.renderservice'];

        $request = $event->getRequest();
        $response = $event->getResponse();

        $RenderService->initRenderControl($response);

        $RenderService = $this->addMyPageMenu($RenderService, true);

        $response->setContent($RenderService->getContent());
    }


    public function productDetailBefore() {

        // 登録処理
        $request = $this->app['request'];
        $product_id = $request->get('id');
        $regist_id = $request->get('regist_id');

        if(empty($regist_id)) {
            return;
        }

        if($request->getMethod() === 'POST') {

            $mode = $request->get('mode');

            /* @var $DpostService \Plugin\DPost\Service\DPostService */
            $DpostService = $this->app['dpost.service.dpostservice'];

            $form = $this->app->form()->getForm();
            $form->handleRequest($request);

//             if($form->isValid()) {

                switch($mode) {
                    case 'setting':
                        // 設定

                        // 有効情報チェック
                        if(!$DpostService->isProductPostQueueExists($regist_id, $product_id)) {

                            $em = $this->app['orm.em'];

                            // ない場合は登録
                            $Product = $this->app['eccube.repository.product']->find($product_id);

//                             $ProductPush = new \Plugin\DPost\Entity\ProductPush();
//                             $ProductPush->setProductId($product_id);
//                             $ProductPush->setPushCount(0);
//                             $ProductPush->setClick(0);
//                             $ProductPush->setProduct($Product);
//                             $em->persist($ProductPush);
//                             $em->flush();

                            /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                            $ProductPostQueue = new \Plugin\DPost\Entity\ProductPostQueue();
                            $ProductPostQueue->setRegistId($regist_id);
                            $ProductPostQueue->setProductId($product_id);
                            $ProductPostQueue->setQueueStatus(0);
                            $ProductPostQueue->setCreateDate(new \DateTime());
                            $ProductPostQueue->setUpdateDate(new \DateTime());

                            $ProductPostQueue->setProduct($Product);
//                             $ProductPostQueue->setProductPush($ProductPush);

//                             \Doctrine\Common\Util\Debug::dump($ProductPostQueue);

                            $em->persist($ProductPostQueue);

                            $em->flush();
                        }

                        break;

                    case 'delete':

                        // 設定削除
                        /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                        $ProductPostQueue = $DpostService->getProductPostQueue($regist_id, $product_id);

//                         \Doctrine\Common\Util\Debug::dump($ProductPostQueue);exit;

                        if(!empty($ProductPostQueue)) {
                            $em = $this->app['orm.em'];
                            $em->remove($ProductPostQueue);
                            $em->flush();
                        }

                        break;

                    case 'check':
                        // チェック
                        // レンダリングフックにて処理
                        return;

                    default:
                        return;
                        break;
                }
//             }

            // 商品詳細ページにリダイレクト
            header("Location: " . $this->app->url('product_detail', array('id' => $product_id)));
            exit;
        }

    }

    public function renderAddProductDetail(FilterResponseEvent $event) {

        $app = $this->app;

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $this->app['dpost.service.dpostservice'];

        // 商品詳細加工
        $request = $event->getRequest();

        // 商品ID
        $product_id = $request->get('id');

        // regist_id
        $regist_id = $request->get('regist_id');

        // 品切れ検索
//         $arrProductClass = $DpostService->getNoStock($product_id);

        /* @var $Product \Eccube\Entity\Product */
        $Product = $this->app['eccube.repository.product']->get($product_id);


        // Ajax戻り
        $mode = $request->get('mode');
        if($mode == 'check') {

            $arrResult = array();
            if($DpostService->isProductPostQueueExists($regist_id, $product_id)) {
                $arrResult['ret'] = 1;
            } else {
                $arrResult['ret'] = 0;
                $arrResult['product_id'] = $product_id;
            }

            $response = $event->getResponse();
            $response->headers->set('Content-Type', 'application/json');

            $response->setContent(json_encode($arrResult));

            return;

        } else if($mode == 'stop_check') {

            $registIdCount = $DpostService->getRegistIdCount($regist_id);
            $registIdCountProduct = $DpostService->getRegistIdCountProduct($regist_id);

            $arrResult = array();
            if($registIdCount == 0 && $registIdCountProduct <= 1) {
                // 削除
                $arrResult['ret'] = '0';

            } else {
                // 停止のみ
                $arrResult['ret'] = '1';
            }

            $response = $event->getResponse();
            $response->headers->set('Content-Type', 'application/json');

            $response->setContent(json_encode($arrResult));

            return;
        }

        if(!$Product->getStockFind()) {

            // データ取得
            /* @var $DpostSetting \Plugin\DPost\Entity\DpostSetting */
            $DpostSetting = $this->app['eccube.repository.dpostsetting']->findOneBy(array());

            if(empty($DpostSetting)) {

                // 未設定
                return;

            } else {
                $flg = $DpostSetting->getProductFlg();

                if(empty($flg) || $flg == 0) {
                    // 処理なし
                    return;
                }
            }

            $response = $event->getResponse();

            /* @var $RenderService \Plugin\DPost\Service\RenderService */
            $RenderService = $this->app['dpost.service.renderservice'];

            $RenderService->initRenderControl($response);

            // 入荷通知　ボタン追加
            $url = "";
            $html = "";
            $html .= "<div class='btn_area'><ul class='row'>";
            $html .= "<li class='col-xs-12 col-sm-8'><button id='js-push-button' class='js-push-button btn btn-info btn-block";
            $html .= "  prevention-mask'>入荷通知を受け取る</button></li>";
            $html .= "</ul></div>";
            $html .= "<input type='hidden' name='regist_id' id='regist_id' ></input>";

            $RenderService->insertAfter(array("//div[@class='btn_area']", 1), $html);

            // Script追加
            $root_urlpath = $app['config']['root_urlpath'];
            $script = '<link rel="manifest" href="'.$root_urlpath.'/plugin/dpost/manifest.json" />';
            $RenderService->appendChild(array("//head"), $script);

            $script = " <script src='".$root_urlpath."/plugin/dpost/detail_main.js' charset='UTF-8'></script>";
            $RenderService->appendChild(array("//head"), $script);

            $script = " <script>";
            $script .= " </script>";
            $RenderService->appendChild(array("//head"), $script);


            $response->setContent($RenderService->getContent());


        } else {

            return;
        }


//         \Doctrine\Common\Util\Debug::dump($ProductClass);

    }



}