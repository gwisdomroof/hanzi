
<?php 
use common\models\HanziSet;
?>

<table class="table table-bordered">
<tbody>
    <?php if (!empty($data->min_split)) : ?>
    <tr><td width="80px">初步拆分</td><td><div>
        <?= "<div class='split'>$data->min_split</div>"; ?>
    </div></td></tr>
    <?php endif;?>

    <?php if (!empty($data->deform_split)) : ?>
    <tr><td width="80px">调笔拆分</td><td><div>
        <?= "<div class='split'>$data->deform_split</div>"; ?>
    </div></td></tr>
    <?php endif;?>

    <?php if (!empty($data->mix_split)) : ?>
    <tr><td width="80px">混合拆分</td><td><div>
        <?= "<div class='split'>$data->mix_split</div>"; ?>
    </div></td></tr>
    <?php endif;?>

    <?php if (!empty($data->similar_stock)) : ?>
    <tr><td width="80px">相似部件</td><td><div>
        <?= "<div class='component'>$data->similar_stock</div>"; ?>
    </div></td></tr>
    <?php endif;?>

</tbody>
</table>
