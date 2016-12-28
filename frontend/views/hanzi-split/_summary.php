<?php

use common\models\HanziSplit;
use common\models\HanziSet;


/* @var $this yii\web\View */
?>
<div class="form-group">
<div class="">
<table class="table table-striped table-bordered table-center">
	<tbody>
	<tr>
		<td>&nbsp;</td>
		<td style="font-weight: bold" width="28%">初次拆分</td>
		<td style="font-weight: bold" width="28%">回查（二次拆分）</td>
		<td style="font-weight: bold" width="28%">审查</td>
	</tr>
	<tr>
		<td>重复值</td>
		<td><?php echo empty($model->duplicate10) ?null : $model->duplicate10; ?></td>
		<td><?php echo empty($model->duplicate20) ?null : $model->duplicate10; ?></td>
		<td><?php echo empty($model->duplicate30) ?null : $model->duplicate10; ?></td>
	</tr>
	<tr>
		<td>是否难字</td>
		<td><?php echo empty($model->hard10) ?null : HanziSet::hards()[$model->hard10]; ?></td>
		<td><?php echo empty($model->hard20) ?null : HanziSet::hards()[$model->hard20]; ?></td>
		<td><?php echo empty($model->hard30) ?null : HanziSet::hards()[$model->hard30]; ?></td>
	</tr>
	<tr>
		<td>初步拆分1</td>
		<td><?php echo $model->initial_split11; ?></td>
		<td><?php echo $model->initial_split21; ?></td>
		<td><?php echo $model->initial_split31; ?></td>
	</tr>
	<tr>
		<td>初步拆分2</td>
		<td><?php echo $model->initial_split12; ?></td>
		<td><?php echo $model->initial_split22; ?></td>
		<td><?php echo $model->initial_split32; ?></td>
	</tr>
	<tr>
		<td>调笔拆分</td>
		<td><?php echo $model->deform_split10; ?></td>
		<td><?php echo $model->deform_split20; ?></td>
		<td><?php echo $model->deform_split30; ?></td>
	</tr>
	<tr>
		<td>相似部件</td>
		<td><?php echo $model->similar_stock10; ?></td>
		<td><?php echo $model->similar_stock20; ?></td>
		<td><?php echo $model->similar_stock30; ?></td>
	</tr>
	</tbody>
</table>
</div>
</div>