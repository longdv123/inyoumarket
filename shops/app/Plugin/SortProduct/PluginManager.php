<?php



namespace Plugin\SortProduct;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Entity\Master\ProductListOrderBy;

class PluginManager extends AbstractPluginManager
{

    // インストール時に、指定の処理を実行します
    public function install($config, $app)
    {
    }

    // アンインストール時にマイグレーションの「down」メソッドを実行します
    public function uninstall($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    // プラグイン有効時に、マイグレーションの「up」メソッドを実行します
    public function enable($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);

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
            // 保存
            $app['orm.em']->flush();
        } else {
            //$debugMessage[]='['.$targetName.'] はすでに追加されてます';
        }

    }


    // プラグイン無効時に、指定の処理 ( ファイルの削除など ) を実行します
    public function disable($config, $app)
    {
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

    // プラグインアップデート時に、指定の処理を実行します
    public function update($config, $app)
    {
    }


}
