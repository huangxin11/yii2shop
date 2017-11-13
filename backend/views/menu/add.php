<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'menu_id')->dropDownList(\yii\helpers\ArrayHelper::merge(['0'=>'顶级分类'],$menus));
echo $form->field($model,'url')->dropDownList(\yii\helpers\ArrayHelper::merge(['0'=>'无'],$permissions));
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();
