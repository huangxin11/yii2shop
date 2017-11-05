<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'start',['inline'=>1])->radioList(['0'=>'隐藏','1'=>'显示']);
echo $form->field($model,'sort')->dropDownList(['1'=>'置顶','2'=>'自动']);
echo $form->field($model,'logo')->hiddenInput();
//echo $form->field($model,'imgFile')->fileInput();
//注册css文件和js文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    'depends'=>\yii\web\JqueryAsset::className(),
]);
$url = \yii\helpers\Url::to(['upload']);
$this->registerJs(
    <<<JS
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,
    
    // swf文件路径
    swf: '/js/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/jpg,image/jpeg,image/png',//弹出选择框慢的问题
        
    }
});
//文件上传成功  回c显图片
uploader.on( 'uploadSuccess', function( file ,response) {
    //$( '#'+file.id ).addClass('upload-state-done');
    //console.log(response);
    //console.log(file);
    //response.url  //上传成功的文件路径
    //将图片地址赋值给img
    $("#img").attr('src',response.url);
    //将图片地址写入logo
    $("#brand-logo").val(response.url);
});
JS

);
?>
    <div id="uploader-demo">
        <div id="fileList" class="uploader-list"></div>
        <div id="filePicker">选择图片</div>
    </div>

<?php
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,['id'=>'img','height'=>50]);

echo $form->field($model,'intro')->textInput();
//验证码
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'template'=>'<div class="row"><div class="col-lg-1">{input}</div><div class="col-lg-1">{image}</div></div>'
]);
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();