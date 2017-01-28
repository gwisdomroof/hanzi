<?php

use common\models\HanziSplit;
use common\models\HanziSet;


/* @var $this yii\web\View */
?>
<div class="form-group">
<div class="col-lg-offset-2 col-lg-7">
<table class="table table-striped table-bordered table-center">
<tbody>
<tr>
	<td width="100px">&nbsp;</td>
	<td style="font-weight: bold">初次拆分</td>
	<td style="font-weight: bold">二次拆分</td>
</tr>
<tr>
	<td>重复值</td>
	<td><?php echo empty($model->duplicate10) ?null : HanziSet::hards()[$model->duplicate10]; ?></td>
	<td><?php echo empty($model->duplicate20) ?null : HanziSet::hards()[$model->duplicate20]; ?></td>
</tr>
<tr>
	<td>是否难字</td>
	<td><?php echo empty($model->hard10) ?null : HanziSet::hards()[$model->hard10]; ?></td>
	<td><?php echo empty($model->hard20) ?null : HanziSet::hards()[$model->hard20]; ?></td>
</tr>
<tr>
	<td>初步拆分1</td>
	<td><?php echo $model->initial_split11; ?></td>
	<td><?php echo $model->initial_split21; ?></td>
</tr>
<tr>
	<td>初步拆分2</td>
	<td><?php echo $model->initial_split21; ?></td>
	<td><?php echo $model->initial_split22; ?></td>
</tr>
<tr>
	<td>调笔拆分</td>
	<td><?php echo $model->deform_split10; ?></td>
	<td><?php echo $model->deform_split20; ?></td>
</tr>
<tr>
	<td>相似部件</td>
	<td><?php echo $model->similar_stock10; ?></td>
	<td><?php echo $model->similar_stock20; ?></td>
</tr>
</tbody>
</table>
</div>
</div>