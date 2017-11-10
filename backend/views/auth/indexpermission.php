<?php
$this->registerCssFile('@web/DataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/media/js/jquery.dataTables.js',[
    'depends'=>\yii\web\JqueryAsset::className() //指定依赖
]);
?>
<table id="table_id_example" class="display">
<thead>
    <tr>
        <th>名称</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
</thead>
    <tbody>
<?php foreach ($permissions as $permission):?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to('update-permission?name='.$permission->name)?>" class="btn btn-default">修改</a>
            <a href="javascript:;" class="btn btn-link del">删除</a>
        </td>
    </tr>
<?php endforeach;?>
    </tbody>
    <tr>
        <td colspan="3">
            <a href="<?=\yii\helpers\Url::to('auth/add-permission',['class'=>'btn btn-link'])?>">添加</a>
        </td>
    </tr>

</table>

<script>
    <?php $this->beginBlock('myjs');?>
    $("table").on('click','.del',function () {
        var name = $(this).closest("tr").find("td:first").text();
        var that = this;
        if (confirm('是否删除')){
            $.post('delete-permission',{name:name},function (data) {
                if(data == 'success'){
                    $(that).closest("tr").remove();
                    $("#myspan").html('删除成功！');
                } else {
                    $("#myspan").html('删除失败！该分类还有下级分类');
                }
            });
        }

    });
    $('#table_id_example').DataTable({
        language: {
            "sProcessing": "处理中...",
            "sLengthMenu": "显示 _MENU_ 项结果",
            "sZeroRecords": "没有匹配结果",
            "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
            "sInfoPostFix": "",
            "sSearch": "搜索:",
            "sUrl": "",
            "sEmptyTable": "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands": ",",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "上页",
                "sNext": "下页",
                "sLast": "末页"
            },
            "oAria": {
                "sSortAscending": ": 以升序排列此列",
                "sSortDescending": ": 以降序排列此列"
            }
        }
    });
    <?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>


