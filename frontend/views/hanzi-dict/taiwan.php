<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = $title;
?>

<frameset cols="70%,30%">
	<frameset rows="20%,80%">
		<frame src=<?=$up?> name="up">
		<frame src=<?=$down?> name="down">
	</frameset>
	<frame src=<?=$right?> name="right">
</frameset>


