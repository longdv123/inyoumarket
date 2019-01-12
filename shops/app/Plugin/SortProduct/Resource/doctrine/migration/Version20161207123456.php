<?php



namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Common\Constant;
use Eccube\Application;  //★エンティティマネージャーの取得のために必要です
use Eccube\Entity\PageLayout;

use Plugin\SortProduct\Entity\SortProduct;
use Eccube\Entity\Master\ProductListOrderBy;

class Version20161207123456 extends AbstractMigration
{

    // 対象のエンティティを指定
    protected $entities = array(
        'Plugin\SortProduct\Entity\SortProduct',
    );


    public function up(Schema $schema)
    {
        // テーブルの生成
        $app = Application::getInstance();  // $app取得
        $em = $app['orm.em'];

        // [plg_sort_product]テーブル作成
        if (!$schema->hasTable('plg_sort_product')) {
            $meta = $this->getMetadata($em);  // カラム情報を取得している？
            $tool = new SchemaTool($em);  // テーブル生成のためにスキーマツールをインスタンス化
            $tool->createSchema($meta);  // テーブルを生成

            // 作成したテーブルへデータ書き込み
            //   [dtb_product]テーブルから[product_id]一覧を取得
            $allProductRecords = $app['eccube.repository.product']->findAll();

            //   上記の[product_id]一覧を元に、作成したテーブルの[product_id][rank]カラムを埋める
            foreach($allProductRecords as $allProductRecord){
                $productId = $allProductRecord->getId();
                $entity = new SortProduct();
                $entity->setProduct_id($productId);
                $entity->setRank($productId);  // とりあえずproductId順のランクにする
                $app['orm.em']->persist($entity);
            }
        }


        // 既存テーブルへのデータ追加するもの

        // [mtb_product_list_order_by]テーブルに[おすすめ順]を追加する
        // [おすすめ順]のrankは最上位とする
        // すでに追加済みの場合は登録しない
        $targetRepository = $app['orm.em']->getRepository('Eccube\Entity\Master\ProductListOrderBy');
        $targetName = 'おすすめ順';
        $targetRecord = $targetRepository->findOneBy(array('name'=>$targetName));
        if(!$targetRecord) {  // [おすすめ順]が未追加の場合に追加する
            //$debugMessage[]='['.$targetName.'] を追加します';
            $records_ProductListOrderBy = $targetRepository->findAll();

            // [おすすめ順]のrankを最上位[0]に入れ込むため、既存のrankを+1ずつ増やして 後ろへずらす
            // ついでにIDの最大値もゲットする
            $maxId = 0;
            foreach ($records_ProductListOrderBy as $record_ProductListOrderBy) {
                // 最大IDの調査
                $id = $record_ProductListOrderBy->getId();
                if ($id > $maxId) {
                    $maxId = $id;
                }
                // rankを+1 増やしてずらす
                $rank = $record_ProductListOrderBy->getRank();
                $record_ProductListOrderBy->setRank($rank + 1);
                $app['orm.em']->persist($record_ProductListOrderBy);
            }
            //$debugMessage['new_$records_ProductListOrderBy'] = $records_ProductListOrderBy;

            // テーブルに[おすすめ順]を追加する
            $entity = new ProductListOrderBy;
            $entity->setID($maxId + 1);
            $entity->setName($targetName);
            $entity->setRank('0');
            $app['orm.em']->persist($entity);
            //$debugMessage['saved_$records_ProductListOrderBy'] = $targetRepository->findAll();
            //$debugMessage[]='['.$targetName.'] を追加しました';
        } else {
            //$debugMessage[]='['.$targetName.'] はすでに追加されてます';
        }


        // 保存
        $app['orm.em']->flush();

        // 追加のフロントページの情報をDBへ追記
        // 特になし

    }


    public function down(Schema $schema)
    {
        $app = Application::getInstance();
        $em = $app['orm.em'];

        $meta = $this->getMetadata($em);

        $tool = new SchemaTool($em);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);

        // テーブル削除
        foreach ($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }

        // シーケンス削除
        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }

        // テーブルからデータ削除（テーブルの削除は無しのもの）

        // [mtb_product_list_order_by]テーブルから[おすすめ順]を削除する
        // [おすすめ順]のrankは最上位に設定されていなくても対応可能にする
        //    -> [おすすめ順]よりrankが下のものはrankを-1する
        // すでに削除済みの場合は削除しない
        $targetRepository = $app['orm.em']->getRepository('Eccube\Entity\Master\ProductListOrderBy');
        $targetName = 'おすすめ順';
        $targetRecord = $targetRepository->findOneBy(array('name'=>$targetName));
        if($targetRecord) {  // [おすすめ順]が登録されている場合のみ削除する（すでに削除済みの場合は削除しない）
            //$debugMessage[]='['.$targetName.'] を削除します';
            // 必要なデータの収集
            $targetRank = $targetRecord->getRank();  // [おすすめ順]のrankの調査
            $records_ProductListOrderBy = $targetRepository->findAll();  // 全レコード取得
            // rankの修正
            foreach ($records_ProductListOrderBy as $record_ProductListOrderBy) {
                $rank = $record_ProductListOrderBy->getRank();
                if( $rank > $targetRank ){
                    $record_ProductListOrderBy->setRank($rank - 1);
                    $app['orm.em']->persist($record_ProductListOrderBy);
                }
            }
            $app['orm.em']->remove($targetRecord);  // [おすすめ順]のデータ削除
            $app['orm.em']->flush();
            //$debugMessage[]='['.$targetName.'] を削除しました';
        } else {
            //$debugMessage[]='['.$targetName.'] はすでに削除されてます';
        }

    }


    protected function getMetadata(EntityManager $em)
    {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }

}
