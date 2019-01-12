<?php


namespace Plugin\SortProduct\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Controller\Admin\Content\FileController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Plugin\SortProduct\Entity\SortProduct;
use Plugin\SortProduct\Util\CommonMethod;

class SortProductController
{

    // 並び替えをrankで制御する
    // rankは大きい数字ほど優先順位が高い
    public function index(Application $app, Request $request, $categoryId=null)
    {
        $debugMessage = array();
        $debugMessage[]='SortProductController::index()v1';

        $currentCategoryId = $categoryId;  // 右辺の変数名をルーティング設定に合わせたいため

        // DB調整 並び替え番号(rank)が設定していない商品があったら設定する
        //   並び替え番号がnullの場合は、商品番号順に並び替え番号の現在の最大値+1から順に番号をふる
        CommonMethod::setNewRank();

        // 商品IDの重複を排除する
        CommonMethod::renewProductId();

        // rankが重複しているものをなくすため、rankを振り直す
        CommonMethod::renewRank();


        // ランク変更時はPOSTでくるのでgetする
        $fromRank = $request->get('from_rank') ? $request->get('from_rank') : null;
        if($fromRank == "null"){$fromRank=null;}
        $toRankBefore = $request->get('to_rank_before') ? $request->get('to_rank_before') : null;
        if($toRankBefore == "null"){$toRankBefore=null;}

        // ランク移動司令(fromRank/toRank)がある場合に ランクを変更する処理開始
        if($fromRank!=null && $toRankBefore!=null) {

            // 並び替えを行う
            if($currentCategoryId===null) {
                // カテゴリー指定がない場合: rank順で全データ取得
                $sortProducts = $app['orm.em']->getRepository('Plugin\SortProduct\Entity\SortProduct')
                    ->getAllRecordOrderByRank();
            }else {
                // カテゴリー指定がない場合: カテゴリーで絞り込んだ商品の情報を元にrank順でデータ取得
                // 表示する商品のID一覧の取得（カテゴリーで絞り込んだもの）
                //   対象カテゴリーと子孫カテゴリーのID一覧を作成
                //   対象がトップカテゴリーの場合は、nullが返る
                $categoryIds = ($currentCategoryId !== null) ? $this->getCategoryIds($currentCategoryId) : null;
                //$debugMessage['$categoryIds']=$categoryIds;
                //   商品IDの一覧を作成
                //     対象&子孫カテゴリーID一覧から、そのカテゴリーに属する商品のID一覧を取得
                //     対象&子孫カテゴリーID一覧がnullの場合はトップカテゴリー指定なので、全商品のID一覧を取得する
                $productIds = $this->getProductIdsFromCategoryIds($categoryIds);
                // 対象商品のID一覧をrankでソートする
                $sortProducts = $app['orm.em']->getRepository('Plugin\SortProduct\Entity\SortProduct')
                    ->getRecordOrderByRank($productIds);
            }
            /*
             * データの整形
             *
             * 整形後: $SortProducts[id] = array(
             *                               "rank" => ランク,
             *                               "oldProductId" => 現在設定されている商品ID,
             *                               "newProductId" => rank変更後の商品ID
             *                            );
             */
            $oldRanks = array();  // 初期化
            $newRanks = array();  // 初期化
            foreach ($sortProducts as $no => $sortProduct) {
                $rank      = $sortProduct["rank"];
                $productId = $sortProduct["product_id"];

                $oldRanks[$no] = array(
                    "rank"      => $rank,
                    "product_id" => $productId
                );
                $newRanks[$no] = array(
                    "rank"      => $rank,
                    "product_id" => null
                );

                if($toRankBefore==$rank){
                    $toNoBefore = $no;
                }
                if($fromRank==$rank){
                    $fromNo = $no;
                }

            }


            // 並び替え処理
            if ($toRankBefore == "top") {
                // toRankBeforeがトップのときの例外処理
                $newNo=0;
                $newRanks[$newNo++]["product_id"] = $oldRanks[$fromNo]["product_id"];
                foreach ($oldRanks as $no => $oldRank) {
                    if ($no == $fromNo) {
                        //   検査レコードが[移動元と同じ]場合は、なにもしない
                    } else {
                        $newRanks[$newNo++]["product_id"] = $oldRank["product_id"];
                    }
                }

            } else {
                $newNo=0;
                for($oldNo=0;$oldNo<count($oldRanks);$oldNo++) {
                    $rank = $oldRanks[$oldNo]["rank"];

                    if ($oldNo == $fromNo) {
                        if($fromNo==$toNoBefore){
                            //   対象レコードが[移動元と同じ] && [移動先ひとつ上($toNoBefore)と同じ]場合は、追加する
                            $newRanks[$newNo++]["product_id"] = $oldRanks[++$oldNo]["product_id"];
                            $newRanks[$newNo++]["product_id"] = $oldRanks[$fromNo]["product_id"];
                        }else {
                            //   検査レコードが[移動元と同じ]場合は、なにもしない
                        }
                    } elseif ($oldNo == $toNoBefore) {
                        $newRanks[$newNo++]["product_id"] = $oldRanks[$oldNo]["product_id"];
                        $newRanks[$newNo++]["product_id"] = $oldRanks[$fromNo]["product_id"];
                    } else {
                        //   検査レコードが上記以外場合は、そのまま代入
                        $newRanks[$newNo++]["product_id"] = $oldRanks[$oldNo]["product_id"];
                    }
                }
            }
            $debugMessage["oldRanks"]= $oldRanks;
            $debugMessage["newRanks"]= $newRanks;
            // 新rankを保存
            foreach ($newRanks as $no => $newRank) {
                $rank      = $newRank["rank"];
                $productId = $newRank["product_id"];
                $sortProductIdRecord = $app['orm.em']->getRepository('\Plugin\SortProduct\Entity\SortProduct')
                    ->findOneBy(array('product_id' => $productId));
                $sortProductIdRecord->setRank($rank);
                $app['orm.em']->persist($sortProductIdRecord);
            }
            $app['orm.em']->flush();
        }


        // twigへ渡す変数の準備
        $title = TITLE;
        $subTitle = SUBTITLE.'　'.$currentCategoryId;
        $disps = $app['eccube.repository.master.disp']->findAll();  // [表示/非表示]の一覧作成
        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();  // 表示件数指定リストのリスト作成
        $page_count = $app['config']['default_page_count'];  // デフォルトの表示件数
        $page_status = null;  // [表示/非表示]のステータス
        $page_no = $request->get('page_no') ? $request->get('page_no') : 1;
        $pcount = $request->get('page_count'); $page_count = empty($pcount) ? $page_count : $pcount;



        // 表示する商品のID一覧の取得（カテゴリーで絞り込んだもの）
        //   対象カテゴリーと子孫カテゴリーのID一覧を作成
        //   対象がトップカテゴリーの場合は、nullが返る
        $categoryIds = ($currentCategoryId !== null) ? $this->getCategoryIds($currentCategoryId) : null;
        //$debugMessage['$categoryIds']=$categoryIds;
        //   商品IDの一覧を作成
        //     対象&子孫カテゴリーID一覧から、そのカテゴリーに属する商品のID一覧を取得
        //     対象&子孫カテゴリーID一覧がnullの場合はトップカテゴリー指定なので、全商品のID一覧を取得する
        $productIds = $this->getProductIdsFromCategoryIds($categoryIds);
        //$debugMessage['$productIds']=$productIds;
        // 対象商品のID一覧をrankでソートする
        $productIdsOrderByRank = $app['orm.em']->getRepository('Plugin\SortProduct\Entity\SortProduct')
            ->getProductIdOrderByRank($productIds);
        $sortedProductIds = array();
        foreach($productIdsOrderByRank as $no => $productIdOrderByRank){
            $sortedProductIds[] = array(
                "no"        => $no,
                "productId" => $productIdOrderByRank["product_id"],
            );
        }


        // ページャーへ格納
        $pagination = $app['paginator']()->paginate(
            $sortedProductIds,
            $page_no,
            $page_count
        );


        // rank設定用フォームの選択リスト作成  (選択リストの例：$choices[sort_product_id] = 1~(ASC); )
        $productRanksOrderByRank = $app['orm.em']->getRepository('Plugin\SortProduct\Entity\SortProduct')
            ->getProductRankOrderByRank($productIds);
        $choices = array();
        $i=1;  // 表示用の選択肢番号は1から開始するため
        foreach($productRanksOrderByRank as $productRankOrderByRank){
            $choices[$productRankOrderByRank["rank"]] = $i++;
        }

        //     選択フォームの作成 と 商品リストとの合体（合体後は$productRecordsPlusになる）
        $productRecordsPlus = array();  // 初期化
        foreach($pagination as $sortedProductId){
            $no        = $sortedProductId["no"];
            $productId = $sortedProductId["productId"];
            $productMini = $this->getProductMini($productId);  // 最小限の商品情報を取得
            $rank = CommonMethod::hashProductIdToRank($productId);  // 商品のrankを取得

            // リストの作成
            $productRecordsPlus[$no]['productRecord'] = $productMini;  // 商品レコード
            $productRecordsPlus[$no]['productId']     = $productId;    // 商品ID
            $productRecordsPlus[$no]['rank']          = $rank;         // rank キーになっているが あとで必要になるため格納
        }


        return $app->render('SortProduct/Resource/template/admin/index.twig', array(
            'title' => $title,
            'sub_title' => $subTitle,
            'debug_message' => $debugMessage,
            'this_page' => 'plugin_SortProduct_config',
            'this_page_by' => 'plugin_SortProduct_byCategory',
            'categoryId' => $currentCategoryId,
            'productRecordsPlus' => $productRecordsPlus,
            'pagination' => $pagination,
            'disps' => $disps,  // [表示/非表示]の一覧
            'pageMaxis' => $pageMaxis,  // 表示件数指定のリスト
            'page_no' => $page_no,  // ページ番号
            'page_count' => $page_count,  // デフォルトの表示件数
            'page_status' => $page_status,  // [表示/非表示]のステータス
            'choices' => $choices,  // 異動先番号の選択肢一覧
        ));
    }



    // ランク移動
    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $productId => $rank) {
                $sortProductIdRecord = $app['orm.em']->getRepository('\Plugin\SortProduct\Entity\SortProduct')
                    ->findOneBy(array('product_id'=>$productId));
                $sortProductIdRecord->setRank($rank);
                $app['orm.em']->persist($sortProductIdRecord);
            }
            $app['orm.em']->flush();
        }
        return true;
    }


    // 指定カテゴリーのIDから、指定カテゴリーと子孫カテゴリーのIDのリストを作成
    // トップカテゴリーの場合はnullを返す、カテゴリー未登録の商品も対象とするためにトップであることを明確にする必要があるため
    public function getCategoryIds($targetCategoryId){

        $app = Application::getInstance();  // $app取得

        // 対象カテゴリーのレコードを取得
        $categoryIds = array();
        if ($targetCategoryId) {  // カテゴリーのIDの指定がある場合
            $targetCategory = $app['eccube.repository.category']->find($targetCategoryId);

            //   子孫カテゴリーIDの取得
            //     対象カテゴリーのレコードから子孫カテゴリーを取得
            $targetCategoryDescendants = $targetCategory->getDescendants();

            //   対象カテゴリーと子孫カテゴリーのIDのリストを作成
            $categoryIds = array();
            $categoryIds[] = $targetCategoryId;  // 対象カテゴリーのIDをまずは代入
            foreach ($targetCategoryDescendants as $targetCategoryDescendant) {
                $categoryIds[] = $targetCategoryDescendant->getId();  // 子孫カテゴリーのIDを代入
            }

        }else {
            $categoryIds = null;  // トップカテゴリーの場合はnullを返す
        }

        return $categoryIds;
    }


    // 対象&子孫カテゴリーのIDリストから、そのカテゴリーに属する商品のIDのリストを取得
    // 対象&子孫カテゴリーのIDリストがnullの場合はトップカテゴリーなので、全商品のリストを取得する
    public function getProductIdsFromCategoryIds($categoryIds){

        $app = Application::getInstance();  // $app取得

        // エンティティの用意
        $productCategoryEntity = $app['orm.em']->getRepository('\Eccube\Entity\ProductCategory');

        if($categoryIds === null){
            // 対象&子孫カテゴリーのIDリストがnullの場合はトップカテゴリーなので、全商品のリストを取得する
            // array()は対象外なので[===null]で判定する
            $entity_Product = $app['orm.em']->getRepository('Eccube\Entity\Product');
            $productRecords = $entity_Product->findAll();
            // productIDのリストを作成
            $productIds = array();  // 初期化
            foreach($productRecords as $productRecord){
                $productIds[] = $productRecord->getId();
            }
        } else {
            // productIDのリストを作成
            $productIds = array();  // 初期化
            foreach ($categoryIds as $categoryId) {
                // カテゴリーIDで検索
                $productIdRecords = $productCategoryEntity->findBy(array('category_id' => $categoryId));
                if ($productIdRecords != null) {
                    // 1つのカテゴリーに複数の商品が属するため、展開
                    foreach ($productIdRecords as $productIdRecord) {
                        $productIds[] = $productIdRecord->getProductId();
                    }
                }
            }
            $productIds = array_unique($productIds);  // 複数のカテゴリーに所属する商品があるため重複排除
            //$debugMessage['$productIds']=$productIds;
        }

        return $productIds;
    }


    // プロダクトIDを元に、商品レコード（ただし必要な情報のみ）を取得する
    //   必要な情報 (twigで参照している情報のみ)
    //   ・id
    //   ・mainFileName
    //   ・name
    public function getProductMini($productId)
    {
        $app = Application::getInstance();  // $app取得

        $Product = $app['orm.em']->getRepository('\Eccube\Entity\Product')->findOneBy(array('id'=>$productId));
        $productMini = array();
        if($Product !=null && $Product->getDelFlg()!='1') {
            // 必要な情報のみセット
            $productMini['id'] = $productId;                         // 商品ID
            $productMini['mainFileName'] = $Product->getMainFileName();  // 商品画像データ
            $productMini['name'] = $Product->getName();          // 商品名
        }

        return $productMini;
    }

    // プロダクトIDのリストを元に、商品レコードのリスト（ただし必要な情報のみ）を取得する
    // 必要かどうかはtwigで参照しているかどうかである
    // id
    // mainFileName
    // name
    public function getMiniProductRecord($productIds)
    {
        $app = Application::getInstance();  // $app取得

        $productEntity = $app['orm.em']->getRepository('\Eccube\Entity\Product');
        $productRecords = array();
        foreach($productIds as $productId){
            // テーブルからレコード取得
            $productRecord = $productEntity->findOneBy(array('id'=>$productId));
            if($productRecord !=null && $productRecord->getDelFlg()!='1') {
                // productID取得
                $productId = $productRecord->getId();
                // 必要な情報のみセット
                $productRecords[$productId]['id'] = $productId;                         // 商品ID
                $productRecords[$productId]['mainFileName'] = $productRecord->getMainFileName();  // 商品画像データ
                $productRecords[$productId]['name'] = $productRecord->getName();          // 商品名
            }
        }

        return $productRecords;
    }



}
