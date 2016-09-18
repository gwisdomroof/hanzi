<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\HanziSplit */

$this->title = Yii::t('frontend', '初次拆分', [
	'modelClass' => 'HanziSplit',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'HanziSplits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = '当前积分：' . \common\models\HanziUserTask::getScore(Yii::$app->user->id);
$this->params['breadcrumbs'][] = '日进度：' . Yii::$app->session->get('curSplitProgress');
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

</div>

<!-- <template id="check-tmpl">
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
<script type="text/javascript" src="/js/bushou.js"></script> -->
