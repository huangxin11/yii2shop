<?php
namespace backend\controllers;
use backend\models\Brand;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class BrandController extends Controller{
    public $enableCsrfValidation = false;
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
//                $model->imgFile = UploadedFile::getInstance($model,'imgFile');
                if ($model->validate()){
//                    $ext = $model->imgFile->extension;
//                    $file = '/upload/'.uniqid().'.'.$ext;
//                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$file,0);
//                    $model->logo = $file;
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
//           $model->imgFile = UploadedFile::getInstance($model,'imgFile');
            if ($model->validate()){
             /*   if (!empty($model->imgFile)){
                    $ext = $model->imgFile->extension;
                    $file = '/upload/'.uniqid().'.'.$ext;
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$file,0);
                    $model->logo = $file;
                }else{
                    $model->logo = $img;
                }*/
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
    //图片
    public function actionUpload(){
        if(\Yii::$app->request->isPost){
            $imgFile = UploadedFile::getInstanceByName('file');
            //判断是否有文件上传
            if($imgFile){
                $fileName = '/upload/'.uniqid().'.'.$imgFile->extension;
                $imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,0);
                // 需要填写你的 Access Key 和 Secret Key
                $accessKey ="0qRQVLvIWqDTRvggLAvpEz-jb8ihvLsk9Fi-xhdj";
                $secretKey = "s6f4uvEDKmERjJfmVeq77QwD82CeadtV4rADQU1t";
                //对象存储 空间名称
                $bucket = "yii2shop";
                $domian = 'oyxh7xz44.bkt.clouddn.com';

                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);

                // 生成上传 Token
                $token = $auth->uploadToken($bucket);

                // 要上传文件的本地路径
                $filePath = \Yii::getAlias('@webroot').$fileName;

                // 上传到七牛后保存的文件名
                $key = $fileName;

                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();

                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    //上传失败 打印错误
                    return Json::encode(['error'=>$err]);
                } else {
                    return Json::encode(['url'=>'http://'.$domian.'/'.$fileName]);
                }
            }
        }
    }
    //测试
    public function actionTest(){


            // 需要填写你的 Access Key 和 Secret Key
                    $accessKey ="0qRQVLvIWqDTRvggLAvpEz-jb8ihvLsk9Fi-xhdj";
                    $secretKey = "s6f4uvEDKmERjJfmVeq77QwD82CeadtV4rADQU1t";
        //对象存储 空间名称
            $bucket = "yii2shop";
            $domian = 'oyxh7xz44.bkt.clouddn.com';

            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);

            // 生成上传 Token
            $token = $auth->uploadToken($bucket);

            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').'/upload/'.'59fc0f0dd2d2a.jpg';

            // 上传到七牛后保存的文件名
            $key = '/upload/'.'59fc0f0dd2d2a.jpg';

            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            echo "\n====> putFile result: \n";
            if ($err !== null) {
                var_dump($err);
            } else {
                var_dump($ret);
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