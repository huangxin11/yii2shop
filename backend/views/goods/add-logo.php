<?php
$form = \yii\bootstrap\ActiveForm::begin([]);
//echo $form->field($model,'imgFile')->fileInput();
//注册css文件和js文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    'depends'=>\yii\web\JqueryAsset::className(),
]);
$url = \yii\helpers\Url::to(['uploads']);
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
    $("#goodsgallery-path").val(response.url);
});
JS

);
?>
    <div id="uploader-demo">
        <div id="fileList" class="uploader-list"></div>
        <div id="filePicker">选择图片</div>
    </div>

<?php
echo \yii\bootstrap\Html::img($model->path?$model->path:false,['id'=>'img','height'=>50]);
echo \yii\bootstrap\Html::button('添加图片',['class'=>'btn btn-block add']);
echo $form->field($model,'path')->hiddenInput();
echo $form->field($model,'goods_id')->hiddenInput(['value'=>$id]);
\yii\bootstrap\ActiveForm::end();
?>
<span id="myspan" style="color: red"></span>
<table class="table">
    <tr>
        <th hidden="hidden">ID</th>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach ($goodslogo as $logo):?>
    <tr>
        <td hidden="hidden"><?=$logo->id?></td>
        <td><?=\yii\bootstrap\Html::img($logo->path,['width'=>100])?></td>
        <td>
            <a href="javascript:;" class="btn btn-link del">删除</a>
        </td>
    </tr>
    <?php endforeach;?>
</table>

    <script>
        <?php $this->beginBlock('myjs');?>
        $(".add").click(function () {
            var id = $("#goodsgallery-goods_id").val();
            var path = $("#goodsgallery-path").val();
            $.post('add-logo',{goods_id:id,path:path},function (data) {
                    $("<tr><td hidden=hidden>"+data+"</td> <td><img src='"+path+"' alt='' width='100'></td><td> <a href='javascript:;' class='btn btn-link del'>删除</a></td></tr>").appendTo("table");
                $("#myspan").html('添加成功！');
            });
            $("input[type=reset]").trigger("click");//触发reset
        });
        $("table").on('click','.del',function () {
            var id = $(this).closest("tr").find("td:first").text();
            var that = this;
            if (confirm('是否删除')){
                $.post('delete-logo',{id:id},function (data) {
                    if(data == 'success'){
                        $(that).closest("tr").remove();
                        $("#myspan").html('删除成功！');
                    } else {
                        $("#myspan").html('删除失败！');
                    }
                });
            }

        });

        <?php $this->endBlock();?>
    </script>
<?php $this->registerJs($this->blocks['myjs']);?>