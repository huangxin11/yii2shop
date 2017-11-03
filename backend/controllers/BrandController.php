<?php
namespace backend\controllers;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;

class BrandController extends Controller{
    /**
     * 展示列表
     */
    public function actionIndex(){
        $query = Brand::find();
        $pager = new Pagination();
        //总页数
        $pager->totalCount = $query->where(['>','start',0])->count();
        //每页
        $pager->pageSize = 2;
        $brands = $query->where(['start'=>1])->limit($pager->pageSize)->offset($pager->offset)->all();
        return $this->render('index',['brands'=>$brands,'pager'=>$pager]);
    }
    /**
     * 添加品牌
     */
    public function actionAdd(){
            $request = \Yii::$app->request;
            $model = new Brand();
            if ($request->isPost){
                $model->load($request->post());
                $model->imgFile = UploadedFile::getInstance($model,'imgFile');
                if ($model->validate()){
                    $ext = $model->imgFile->extension;
                    $file = '/upload/'.uniqid().'.'.$ext;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$file,0);
                    $model->logo = $file;
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
     * 修改品牌
     */
    public function actionUpdate($id){
        $request = \Yii::$app->request;
        $model = Brand::findOne(['id'=>$id]);
        $img = $model->logo;
        if ($request->isPost){
            $model->load($request->post());
            $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
                if (!empty($model->imgFile)){
                    $ext = $model->imgFile->extension;
                    $file = '/upload/'.uniqid().'.'.$ext;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$file,0);
                    $model->logo = $file;
                }else{
                    $model->logo = $img;
                }
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
     * 删除品牌
     */
    public function actionDelete(){
        $id = $_POST['id'];
        $brand = Brand::findOne(['id'=>$id]);
        $brand->start = -1;
        $img = $brand->logo;
        if (empty($_POST['imgFile'])){
            $brand->logo = $img;
        }
       if ($brand->save(0)){
           echo 1;
       }else{
           var_dump($brand->getErrors());
       }

    }
}