<?php
namespace backend\controllers;
use backend\models\GoodsCategory;

use yii\web\Controller;


class GoodsCategoryController extends Controller{
    /**
     * @return string
     * 展示
     */
    public function actionIndex(){
        $categorys = GoodsCategory::find()->all();
        return $this->render('index',['categorys'=>$categorys]);
    }

    //添加
    public function actionAdd(){
        $model = new GoodsCategory();
        $model->parent_id =0;
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                if ($model->parent_id == 0){
                    /*      $countries = new Menu(['name' => 'Countries']);
           $countries->makeRoot();*/
                    //创建根节点
                    $model->makeRoot();
                    //$model->save(); //不能使用save创建节点
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['index']);
                }else{
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['index']);
                }

            }
        }
        return $this->render('add',['model'=>$model]);
    }
    /**
     * @return string
     * 修改
     */
    public function actionUpdate($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                if ($model->parent_id == 0){
                    //修改根节点
                    $model->makeRoot();
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['index']);
                }else{
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['index']);
                }

            }
        }else{
//            var_dump($model);die;
//            var_dump($model->parent_id);die;
            return $this->render('add',['model'=>$model]);
        }
    }
    /**
     * @return string
     * 删除
     */
    public function actionDelete(){
        $id=$_POST['id'];
        $category = GoodsCategory::findOne(['id'=>$id]);
        $chiden = GoodsCategory::find()->where(['parent_id'=>$id])->all();
        if ($chiden){
            echo 'false';
        }else{
            $category->delete();
             echo 'success';
        }
    }

    //测试
    public function actionTest(){
        $this->layout = false;
        //不加载布局文件
        return $this->render('test');
    }




}
