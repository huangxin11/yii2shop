<?php
namespace frontend\controllers;
use frontend\models\Cart;
use frontend\models\Goods;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class CartController extends Controller{
    public $enableCsrfValidation = false;
    /**
     * 展示购物列表
     */
    public function actionIndex(){
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $carts = $cookies->getValue('carts');
            if ($carts){
                $carts = unserialize($carts);
            }else{
                $carts = [];
            }
            $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
        }else{
//            echo 1;die;
            $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->identity->id])->all();
            $carts = ArrayHelper::map($carts,'goods_id','amount');
            $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
        }
        return $this->render('flow',['carts'=>$carts,'models'=>$models]);

    }
    /**
     * 加入购物车
     */
    public function actionAdd(){
        if (\Yii::$app->user->isGuest){
            //当没有登录得时候保存到cookie
            $goods_id = $_GET['goods_id'];
            $amount = $_GET['amount'];
//            var_dump($_GET);die;
            $cookies = \Yii::$app->request->cookies;
            //查看cookie里面是否有
            $carts = $cookies->getValue('carts');
            if ($carts){
                //有值就反序列化
                $carts = unserialize($carts);
            }else{
                $carts = [];
            }
            //判断cookie里面是否有提交得商品
            if (array_key_exists($goods_id,$carts)){
                $carts[$goods_id] += $amount;
            }else{
                $carts[$goods_id] = $amount;
            }
        $cookies = \Yii::$app->response->cookies;
         $cookie = new Cookie();
        $cookie->name = 'carts';
        $cookie->value = serialize($carts);
        $cookie->expire = time()+30*24*3600;
        $cookies->add($cookie);
        }else{
            $request = \Yii::$app->request;
            if ($request->isGet){
                $goods_id = $_GET['goods_id'];
                $amount = $_GET['amount'];
//            var_dump($goods_id);die;
                $goods = Cart::findOne(['goods_id'=>$goods_id]);
//                var_dump($goods);die;
                if ($goods && $goods->member_id == \Yii::$app->user->identity->id){
                    $goods->amount += $amount;
                    $goods->save();
                }else{
                    $model = new Cart();
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                    $model->member_id = \Yii::$app->user->identity->id;
                    if ($model->validate()){
                        $model->save();
                    }
                }
            }
        }
        return $this->redirect(['cart/index']);
    }
    public function actionAjaxCart($type){
        //登录操作数据库 未登录操作cookie
        switch ($type){
            case 'change'://修改购物车
                $goods_id = \Yii::$app->request->post('goods_id');
                $amount = \Yii::$app->request->post('amount');
//                var_dump($goods_id);die;
                if(\Yii::$app->user->isGuest){
                    //取出cookie中的购物车
                    $cookies = \Yii::$app->request->cookies;
                    $carts = $cookies->getValue('carts');
                    if($carts){
                        $carts = unserialize($carts);
                    }else{
                        $carts = [];
                    }
                    //修改购物车商品数量
                    $carts[$goods_id] = $amount;
                    //保存cookie
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    $cookie->name = 'carts';
                    $cookie->value = serialize($carts);
                    $cookies->add($cookie);
                }else{
                    $cart = Cart::findOne(['goods_id'=>$goods_id]);
                    $cart->amount = $amount;
                    if ($cart->save()){
                        if ($cart->save()){
                            echo 'success';exit;
                        }else{
                            echo 'false';
                        };
                    }
                }
                break;
            case 'del':
                $goods_id = $_POST['goods_id'];
                if (\Yii::$app->user->isGuest){
                    //取出cookie
                    $cookies = \Yii::$app->request->cookies;
                    $carts = $cookies->getValue('carts');
                    $carts = unserialize($carts);
                    unset($carts[$goods_id]);
                    $carts = serialize($carts);
//存入cookie
                    $cookies = \Yii::$app->response->cookies;
                    $cookie = new Cookie();
                    $cookie->name = 'carts';
                    $cookie->value = $carts;
                    $cookies->add($cookie);
                    return 'success';
                }else{
                    $cart = Cart::findOne(['goods_id'=>$goods_id]);
                    if ($cart->delete()){
                        echo 'success';
                        exit;
                    }else{
                        echo 'false';
                        exit;
                    };
                }

                break;
        }
    }
    public function actionDelete(){
        $id = $_POST['id'];
        $model = Cart::findOne(['id'=>$id]);
        if ($model->delete()){
            echo 'success';
        }else{
            echo 'false';
        };

    }
}
