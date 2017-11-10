<?php
namespace backend\controllers;
use backend\models\ArticleCategory;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;

class ArticleCategoryController extends Controller{
    /**
     * 列表展示
     */
    public function actionIndex(){
        $query = ArticleCategory::find();
        $pager = new Pagination();
        //总页数
        $pager->totalCount = $query->where(['>','status',0])->count();
        //每页
        $pager->pageSize = 2;
        $categorys = $query->where(['status'=>1])->limit($pager->pageSize)->offset($pager->offset)->all();
        return $this->render('index',['categorys'=>$categorys,'pager'=>$pager]);
    }
    /**
     * 增加
     */
    public function actionAdd(){
        $request = \Yii::$app->request;
        $model = new ArticleCategory();
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save(0);
                \Yii::$app->session->setFlash('success','添加成功!');
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
            }
        }else{
            return $this->render('add',['model'=>$model]);
        }
    }



    /**
     * 修改
     */
    public function actionUpdate($id){
        $request = \Yii::$app->request;
        $model = ArticleCategory::findOne(['id'=>$id]);
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
//                var_dump($model);die;
                $model->save(0);
                \Yii::$app->session->setFlash('success','修改成功!');
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
            }
        }else{
            return $this->render('add',['model'=>$model]);
        }
    }
    /**
     * 删除
     */
    public function actionDelete(){
        $id = $_POST['id'];
        $category = ArticleCategory::findOne(['id'=>$id]);
        $category->status = -1;
        if ($category->update(0)){
            echo 1;
        }else{
            var_dump($category->getErrors());
        }
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>\backend\filters\RbacFilter::className(),
            ],
        ];
    }


}