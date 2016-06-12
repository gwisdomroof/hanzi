<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Hanzi */

$this->title = Yii::t('frontend', '初次拆分', [
	'modelClass' => 'Hanzi',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Hanzis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
	#app input:focus {
		outline: none;
	}
	#app a.check{
		background-color: #eee;
	}
</style>
<div class="hanzi-create">

	<?php echo $this->render('_split', [
		'model' => $model,
		'seq' => $seq,
	]) ?>

	
	<div id="app">
		<form class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label">是否难字</label>
				<div class="radio col-sm-6">
					<label>
						<input type="radio" name="optionsRadios" value="option1">是
					</label>
					<label>
						<input type="radio" value="option1">否
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">初步拆分1</label>
				<div class="col-sm-6">
					<check-child></check-child>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">初步拆分2</label>
				<div class="col-sm-6">
					<check-child></check-child>
				</div>
			</div>
		</form>
	</div>
</div>
<template id="check-tmpl">
	<div class="form-control">
		<span>{{one_text}}</span><input type="text" v-model="filterBihua" style="border:0;box-sizing:none;" @keyup="onDown($event)" />
	</div>
	<div>
		<div>
			<div v-show="show_struct">
				<a href="javascript:;" v-for="(index, item) in struct_list" @click="checkStruct(item)" :class="{check: check_index == index}">{{index + 1}}{{item}} </a>
			</div>
			<div v-show="show_bushou">
				<div v-show="filterBihua">
					<a href="javascript:;" v-for="item in bushou" @click="checkBushou(item)">{{item}}</a>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript" src="/js/vue.min.js"></script>
<script type="text/javascript" src="/js/bushou.js"></script>
