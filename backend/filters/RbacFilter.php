<?php
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter {
    public function beforeAction($action)
    {
        if (!\Yii::$app->user->can($action->uniqueId)){
            if (\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }else{
                throw new HttpException(403,'对不起,您没有该操作权限');
                return false;
            }
        }
        return parent::beforeAction($action);
    }
}
