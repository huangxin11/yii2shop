<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'start',['inline'=>1])->radioList(['0'=>'隐藏','1'=>'显示']);
echo $form->field($model,'sort')->dropDownList(['1'=>'置顶','2'=>'自动']);
echo $form->field($model,'imgFile')->fileInput();
echo $form->field($model,'intro')->textInput();
//验证码
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'template'=>'<div class="row"><div class="col-lg-1">{input}</div><div class="col-lg-1">{image}</div></div>'
]);
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();