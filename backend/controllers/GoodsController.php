<?php
namespace backend\controllers;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;

use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\KeyForm;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;


class GoodsController extends Controller
{
    /**
     * @return string
     * 分类展示
     */
    public $enableCsrfValidation = false;

    public function actionIndexCategory()
    {
        $categorys = GoodsCategory::find()->orderBy(['tree' => 'ASC', 'lft' => 'ASC'])->all();
        return $this->render('index', ['categorys' => $categorys]);
    }

    //分类添加
    public function actionAddCategory()
    {
        $model = new GoodsCategory();
        $model->parent_id = 0;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent_id == 0) {
                    //创建根节点
                    $model->makeRoot();
                    //$model->save(); //不能使用save创建节点
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['index-category']);
                } else {
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['index-category']);
                }
            }
        }
        return $this->render('add-category', ['model' => $model]);
    }

    /**
     * @return string
     * 分类修改
     */
    public function actionUpdateCategory($id)
    {
        $model = GoodsCategory::findOne(['id' => $id]);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent_id == 0) {
                    if ($model->getOldAttribute('parent_id') == 0) {
                        $model->save();
                    } else {
                        //修改根节点
                        $model->makeRoot();
                    }
                    \Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect(['index-category']);
                } else {
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    \Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect(['index-category']);
                }

            }
        } else {
//            var_dump($model);die;
//            var_dump($model->parent_id);die;
            return $this->render('add-category', ['model' => $model]);
        }
    }

    /**
     * @return string
     * 分类删除
     */
    public function actionDeleteCategory()
    {
        $id = $_POST['id'];
        $category = GoodsCategory::findOne(['id' => $id]);
        if ($category->isLeaf()) {
            if ($category->parent_id != 0) {
                $category->delete();
                echo 'success';
            } else {
                $category->deleteWithChildren();
                echo 'success';
            }
        } else {
            echo 'false';
        }
        /*      $chiden = GoodsCategory::find()->where(['parent_id'=>$id])->all();
              if ($chiden){

              }else{

              }*/
    }

    /**
     * 商品的显示
     */
    public function actionIndexGoods()
    {
        $quest = Goods::find();
        $pager = new Pagination();
        $pager->totalCount = $quest->where(['status' => 1])->count();
        $pager->pageSize = 3;
        if (!empty($_GET)) {
            if (isset($_GET['name'])) {
                $keyword = $_GET;
                $name = $keyword['name'] ? $keyword['name'] : '';
                $sn = $keyword['sn'] ? $keyword['sn'] : '';
                $minprice = $keyword['minprice'] ? $keyword['minprice'] : 0;
                $maxprice = $keyword['maxprice'] ? $keyword['maxprice'] : PHP_INT_MAX;
                $goods = $quest->where(['status' => 1])->andWhere(['like', 'name', $name])->andWhere(['like', 'sn', $sn])->andWhere(['between', 'shop_price', $minprice, $maxprice])->limit($pager->pageSize)->offset($pager->offset)->all();
            } else {
                $goods = $quest->where(['status' => 1])->limit($pager->pageSize)->offset($pager->offset)->all();
            }
        } else {
            $goods = $quest->where(['status' => 1])->limit($pager->pageSize)->offset($pager->offset)->all();
        }


        return $this->render('index-goods', ['goods' => $goods, 'pager' => $pager]);
    }

    /**
     * @return string
     * 商品的添加
     */
    public function actionAddGoods()
    {
        $model = new Goods();
        $model1 = new GoodsIntro();
        $model2 = GoodsDayCount::findOne(['day' => date('Y-m-d', time())]);
        if (empty($model2)){
            $goods = new GoodsDayCount();
            $goods->day = date('Y-m-d', time());
            $goods->save();
        }
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            $model1->load($request->post());
            $sn = date('Ymd', time()) * 100000 + $model2->count+1;
            $model->sn = $sn;
            if ($model->validate() && $model1->validate()) {
                $model->create_time = time();
                $model->save(0);
                $model1->goods_id = $model->id;
                $model1->save(0);

                    $model2->count += 1;
                    $model2->save(0);

                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['index-goods']);
            }else{
                var_dump($model->getErrors());
                var_dump($model1->getErrors());
                var_dump($model2->getErrors());
            }
        }else{
            $brands = Brand::find()->where(['start' => 1])->all();
            $model->goods_category_id = 0;
            $brands = ArrayHelper::map($brands, 'id', 'name');
            return $this->render('add-goods', ['model' => $model, 'brands' => $brands, 'model1' => $model1]);
        }



    }


    /**
     * @return string
     * 修改
     */
    public function actionUpdateGoods($id){
        $model = Goods::findOne(['id'=>$id]);
        $model1 = GoodsIntro::findOne(['goods_id'=>$id]);
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            $model1->load($request->post());
            if ($model->validate() && $model1->validate()){
                $model->save(0);
                $model1->save(0);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['index-goods']);
            }
        }else{
            $brands = Brand::find()->where(['start'=>1])->all();
//            $model->goods_category_id=0;
            $brands = ArrayHelper::map($brands,'id','name');
            return $this->render('add-goods',['model'=>$model,'model1'=>$model1,'brands'=>$brands]);
        }
    }
    /**
     * @return string
     * 删除商品
     */
    public function actionDeleteGoods(){
//        var_dump($_POST);die;
        $id = $_POST['id'];
        $goods = Goods::findOne(['id'=>$id]);
        $goods->status = 0;
        if ($goods->save(0)){
            echo 'success';
        }else{
            echo 'false';
        };
    }
    /**
     * @return string
     * 相册
     */
    public function actionIndexLogo(){
        $id = $_GET['id'];
        $model = new GoodsGallery();
        $goodslogo = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('add-logo',['model'=>$model,'id'=>$id,'goodslogo'=>$goodslogo]);
    }
    /**
     * ajax添加图片
     */
    public function actionAddLogo(){
        $request = \Yii::$app->request;
        $model = new GoodsGallery();
        if ($request->isPost){
           $logo = $request->post();
           $model->goods_id = $logo['goods_id'];
           $model->path = $logo['path'];
           if ($model->validate()){
               if ($model->save()){
                   echo $model->id;
               }
           }
        }

    }
    /**
     * @return string
     * 删除照片
     */
    public function actionDeleteLogo(){
        $id = $_POST['id'];
        $logo = GoodsGallery::findOne(['id'=>$id]);
        if ($logo->delete()){
            echo 'success';
        }else{
            echo 'false';
        };

    }

    //测试
    public function actionTest(){
        $this->layout = false;
        //不加载布局文件
        return $this->render('test');
    }
    //图片
    public function actionUploads(){
        if(\Yii::$app->request->isPost){
            $imgFile = UploadedFile::getInstanceByName('file');
//            var_dump($imgFile);die;
            //判断是否有文件上传
            if($imgFile){
                $fileName = '/upload/'.uniqid().'.'.$imgFile->extension;
                $imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,0);
                // 要上传文件的本地路径
                $filePath = \Yii::getAlias('@webroot').$fileName;
                return Json::encode(['url'=>$fileName]);
            }
        }
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => '/upload',//图片访问路径前缀
                    "imagePathFormat" => "/image/{yyyy}{mm}{dd}/{time}{rand:6}",//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot").'/upload'
                ]
            ]
        ];
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
