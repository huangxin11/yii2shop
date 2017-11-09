<span id="myspan" style="color: red"></span>
<table class="table">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>是否允许自动登录</th>
        <th>状态</th>
        <th>邮箱</th>
        <th>最后登录时间</th>
        <th>操作</th>
    </tr>
    <?php  foreach ($users as $user):?>
    <tr>
        <td><?=$user->id?></td>
        <td><?=$user->username?></td>
        <td><?=$user->auth_key==0?'不允许':'允许' ?></td>
        <td><?=$user->status==0?'禁用':'启用' ?></td>
        <td><?=date('Y-m-d H:i:s',$user->last_login_time) ?></td>
        <td><?=$user->email?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['update','id'=>$user->id])?>">修改</a>
            <a href="javascript:;" class="btn btn-link del">删除</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6">
            <a href="<?=\yii\helpers\Url::to(['add'])?>">添加</a>
        </td>
    </tr>
</table>
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
                    $("#myspan").html('删除失败！');
                }
            });
        }
    });

    <?php $this->endBlock();?>
</script>
<?php $this->registerJs($this->blocks['myjs']);?>