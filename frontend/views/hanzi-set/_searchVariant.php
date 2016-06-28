
<?php 
use common\models\HanziSet;
?>

<table class="table table-bordered">
<tbody>
    <?php if (!empty($data[HanziSet::SOURCE_TAIWAN])) : ?>
    <tr><td width="80px">TW</td><td><div>
    <?php foreach ($data[HanziSet::SOURCE_TAIWAN] as $normal => $variants) {
        echo "<span class='hanzi-item'>". $normal . "</span><br/>";
        foreach ($variants as $variant) {
            if (!empty($variant->word)) {
                echo "<span class='hanzi-item'>". $variant->word . "</span>";
            } elseif (!empty($variant->pic_name)) {
                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                echo "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'>";
            }
        }
        echo "<br/>";
    } ?>
    </div></td></tr>
    <?php endif;?>

    <?php if (!empty($data[HanziSet::SOURCE_HANYU])) : ?>
    <tr><td width="80px">HY</td><td><div>
    <?php foreach ($data[HanziSet::SOURCE_HANYU] as $normal => $variants) {
        echo "<span class='hanzi-item'>". $normal . "</span><br/>";
        foreach ($variants as $variant) {
            if (!empty($variant->word)) {
                echo "<span class='hanzi-item'>". $variant->word . "</span>";
            } elseif (!empty($variant->pic_name)) {
                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                echo "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'>";
            }
        }
        echo "<br/>";
    } ?>
    </div></td></tr>
    <?php endif;?>

    <?php if (!empty($data[HanziSet::SOURCE_GAOLI])) : ?>
    <tr><td width="80px">GL</td><td><div>
    <?php foreach ($data[HanziSet::SOURCE_GAOLI] as $normal => $variants) {
        echo "<span class='hanzi-item'>". $normal . "</span><br/>";
        foreach ($variants as $variant) {
            if (!empty($variant->word)) {
                echo "<span class='hanzi-item'>". $variant->word . "</span>";
            } elseif (!empty($variant->pic_name)) {
                $picPath = \common\models\HanziSet::getPicturePath($variant->source, $variant->pic_name);
                echo "<img alt= '$variant->pic_name' src='$picPath' class='hanzi-img'>";
            }
        }
        echo "<br/>";
    } ?>
    </div></td></tr>
    <?php endif;?>

</tbody>
</table>
