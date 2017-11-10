<table id="table_id_example" class="table">

    <tr>
        <th>名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>

    <?php foreach ($roles as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to('update-role?name='.$role->name)?>" class="btn btn-default">修改</a>
                <a href="javascript:;" class="btn btn-link del">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="3">
            <a href="<?=\yii\helpers\Url::to('auth/add-role',['class'=>'btn btn-link'])?>">添加</a>
        </td>
    </tr>
</table>

<script>
    <?php $this->beginBlock('myjs');?>
    $("table").on('click','.del',function () {
        var name = $(this).closest("tr").find("td:first").text();
        var that = this;
        if (confirm('是否删除')){
            $.post('delete-role',{name:name},function (data) {
                if(data == 'success'){
                    $(that).closest("tr").remove();
                    $("#myspan").html('删除成功！');
                } else {
                    $("#myspan").html('删除失败！该分类还有下级分类');
                }
            });
        }

    });
    <?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>


