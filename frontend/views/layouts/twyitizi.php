<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

\frontend\assets\FrontendAsset::register($this);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <META http-equiv="content-type" content="text/html; charset=big5">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php echo Html::csrfMetaTags() ?>
</head>

<?php echo $content ?>

</html>
