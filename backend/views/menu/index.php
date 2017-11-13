<span id="myspan" style="color: red"></span>
<table class="table">
    <tr>
        <th>分类名称</th>
        <th>路由</th>
        <th>操作</th>
    </tr>
    <?php foreach ($menus as $menu):?>
    <tr>
        <td hidden="hidden"><?=$menu->id?></td>
        <td><?=$menu->menu_id!=0?str_repeat('-',4).$menu->name:$menu->name ?></td>
        <td><?=$menu->url?></td>
        <td>
            <a href="javascript:;" class="btn btn-default del"> 删除</a>
            <a href="<?=\yii\helpers\Url::to(['update','id'=>$menu->id])?>" class="btn btn-danger"> 修改</a>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<tr>
    <td>
        <a href="<?=\yii\helpers\Url::to(['add'])?>" class="btn btn-link">添加</a>
    </td>
</tr>
<script>
    <?php $this->beginBlock('myjs');?>
    $("table").on('click','.del',function () {
        var id = $(this).closest("tr").find("td:first").text();
        var that = this;
        if (confirm('是否删除')){
            $.post('delete',{id:id},function (data) {
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