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

    /**
     * @return string
     * 展示分类列表
     */
    public function actionIndexCategory()
    {
        //根据左值右值排序查询
        $categorys = GoodsCategory::find()->orderBy(['tree' => 'ASC', 'lft' => 'ASC'])->all();
        //展示分类页面
        return $this->render('index', ['categorys' => $categorys]);
    }

    //分类添加
    public function actionAddCategory()
    {
        //实例化分类活动记录
        $model = new GoodsCategory();
        //给父id默认0
        $model->parent_id = 0;
        $request = \Yii::$app->request;
        //判断是否是post方式提交数据
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证
            if ($model->validate()) {
                //判断是否是顶级ID
                if ($model->parent_id == 0) {
                    //创建根节点
                    $model->makeRoot();
                    //$model->save(); //不能使用save创建节点
                    //提示
                    \Yii::$app->session->setFlash('success', '添加成功');
                    //实例化redis
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1',6379);
                    //删除redis
                    $redis->del('goods-category');
                    //跳转
                    return $this->redirect(['index-category']);
                } else {
                    //如果是二级目录或者三级目录，根据父ID查询出数据
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    $redis = new \Redis();
                    //删除redis
                    $redis->connect('127.0.0.1',6379);
                    $redis->del('goods-category');
                    //提示
                    \Yii::$app->session->setFlash('success', '添加成功');
                    //跳转
                    return $this->redirect(['index-category']);
                }
            }
        }
        //展示添加分类列表
        return $this->render('add-category', ['model' => $model]);
    }

    /**
     * @return string
     * 分类修改
     */
    public function actionUpdateCategory($id)
    {
        //查询要修改的数据
        $model = GoodsCategory::findOne(['id' => $id]);
        $request = \Yii::$app->request;
        //判断是否post方式提交数据
        if ($request->isPost) {
            //接收数据
            $model->load($request->post());
            //验证
            if ($model->validate()) {
                //判断是否是顶级分类
                if ($model->parent_id == 0) {
                    //判断之前是否是顶级分类
                    if ($model->getOldAttribute('parent_id') == 0) {
                        //保存数据库
                        $model->save();
                    } else {
                        //修改根节点
                        $model->makeRoot();
                    }
                    //提示
                    \Yii::$app->session->setFlash('success', '修改成功');
                    //跳转
                    return $this->redirect(['index-category']);
                } else {
                    //如果不是修改为顶级分类
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    //添加子节点
                    /*      $russia = new Menu(['name' => 'Russia']);
                          $russia->prependTo($countries);*/
                    $model->prependTo($parent);
                    //提示
                    \Yii::$app->session->setFlash('success', '修改成功');
                    //跳转
                    return $this->redirect(['index-category']);
                }

            }
        } else {
//            var_dump($model);die;
//            var_dump($model->parent_id);die;
            //展示添加分类的列表
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
        //查询要删除的数据
        $category = GoodsCategory::findOne(['id' => $id]);
        //判断是否是树叶，也就是有没有子分类
        if ($category->isLeaf()) {
            //判断不是顶级分类的时候
            if ($category->parent_id != 0) {
                $category->delete();
                echo 'success';
            } else {
                //是顶级分类的时候
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
        //实例化分页类
        $pager = new Pagination();
        //总数量
        $pager->totalCount = $quest->where(['status' => 1])->count();
        //每页显示数量
        $pager->pageSize = 3;
        //判断GET有没有值
        if (!empty($_GET)) {
            //判断get中是否name
            if (isset($_GET['name'])) {
                //给搜索默认值
                $keyword = $_GET;
                $name = $keyword['name'] ? $keyword['name'] : '';
                $sn = $keyword['sn'] ? $keyword['sn'] : '';
                $minprice = $keyword['minprice'] ? $keyword['minprice'] : 0;
                $maxprice = $keyword['maxprice'] ? $keyword['maxprice'] : PHP_INT_MAX;
                //把搜索条件放在查询语句中
                $goods = $quest->where(['status' => 1])->andWhere(['like', 'name', $name])->andWhere(['like', 'sn', $sn])->andWhere(['between', 'shop_price', $minprice, $maxprice])->limit($pager->pageSize)->offset($pager->offset)->all();
            } else {
                //没有搜索传值的时候就默认搜索所有数据
                $goods = $quest->where(['status' => 1])->limit($pager->pageSize)->offset($pager->offset)->all();
            }
        } else {
            $goods = $quest->where(['status' => 1])->limit($pager->pageSize)->offset($pager->offset)->all();
        }

//展示商品列表
        return $this->render('index-goods', ['goods' => $goods, 'pager' => $pager]);
    }

    /**
     * @return string
     * 商品的添加
     */
    public function actionAddGoods()
    {
        $model = new Goods();
        //实例化商品内容活动模型
        $model1 = new GoodsIntro();
        //查询是否有商品添加表是否有今天的
        $model2 = GoodsDayCount::findOne(['day' => date('Y-m-d', time())]);
        //如果没有今天的就添加一条数据
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
                    "imageUrlPrefix"  => \Yii::$app->params['backend_domain'].'/upload',//图片访问路径前缀
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
