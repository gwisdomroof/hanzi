<?php

use yii\helpers\Html;

header('Content-Type: text/html; charset=big5');

/* @var $this yii\web\View */
$this->title = $title;
?>
<script type="text/javascript">
	function test() {
		// var test = $(window.frames["down"].document).find("a[name='bm_021']").html()
    }

</script>

<frameset cols="70%,30%">
	<frameset rows="20%,80%">
		<frame src=<?=$up?> name="up">
		<frame src=<?=$down?> name="down" onload='test()'>
	</frameset>
	<frame src=<?=$right?> name="right">
</frameset>


