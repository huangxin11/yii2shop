<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
   'depends'=>\yii\web\JqueryAsset::className(),
]);
$nodes = \yii\helpers\Json::encode(\yii\helpers\ArrayHelper::merge([['id'=>0,'parent_id'=>0,'name'=>'顶级分类']],\backend\models\GoodsCategory::getZtreeNodes()));

$this->registerJs(
    <<<js
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
       var setting = {
            callback:{
                onClick: function(event, treeId, treeNode){
                    //获取被点击节点的id
                    var id= treeNode.id;
                    //将id写入parent_id的值
                    $("#goodscategory-parent_id").val(id);
                }
            }
            ,
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            }
        };
        var zNodes = {$nodes};
        
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开所有节点
        zTreeObj.expandAll(true);
        //选中节点(回显)   
        //获取节点  ,根据节点的id搜索节点
        var node = zTreeObj.getNodeByParam("id",{$model->parent_id},null);   
        zTreeObj.selectNode(node);
        

js
);

echo ' <div>
        <ul id="treeDemo" class="ztree"></ul>
    </div>';
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
