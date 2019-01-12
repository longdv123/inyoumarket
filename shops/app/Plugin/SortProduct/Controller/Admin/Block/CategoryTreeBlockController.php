<?php


namespace Plugin\SortProduct\Controller\Admin\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;



// カテゴリーのメニューを作成する
class CategoryTreeBlockController
{

    // カテゴリーのメニューを作成する
    // twigからrender()で呼ばれるメソッド
    public function index(Application $app, $categoryId=null)
    {

        // parent_id対応
        $parent_id = null;  // トップレベルなので、親はなし
        if ($parent_id) {
            $Parent = $app['eccube.repository.category']->find($parent_id);
            if (!$Parent) {
                throw new NotFoundHttpException();
            }
        } else {
            $Parent = null;
        }

        // category_id, id 対応
        $id = $categoryId;
        if ($id) {
            $TargetCategory = $app['eccube.repository.category']->find($id);
            if (!$TargetCategory) {
                throw new NotFoundHttpException();
            }
            $Parent = $TargetCategory->getParent();
        } else {
            $TargetCategory = new \Eccube\Entity\Category();
            $TargetCategory->setParent($Parent);
            if ($Parent) {
                $TargetCategory->setLevel($Parent->getLevel() + 1);
            } else {
                $TargetCategory->setLevel(1);
            }
        }

        // その他の変数
        $Children = $app['eccube.repository.category']->getList(null);
        $Categories = $app['eccube.repository.category']->getList($Parent);
        $TopCategories = $app['eccube.repository.category']->findBy(array('Parent' => null), array('rank' => 'DESC'));
        $category_count = $app['eccube.repository.category']->getTotalCount();


        return $app->renderView('SortProduct/Resource/template/admin/block/block_category_tree.twig',
            array(
                'Children'       => $Children,
                'Parent'         => $Parent,
                'Categories'     => $Categories,
                'TopCategories'  => $TopCategories,
                'TargetCategory' => $TargetCategory,
                'category_count' => $category_count,
                'categoryId'     => $categoryId,
            )
        );


    }

}
