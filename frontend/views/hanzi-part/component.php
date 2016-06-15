<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;

require_once(__DIR__ . '/_components.php');

?>

<style type="text/css">
    .index {
        display: inline-block;
        width: 2em;
        position: absolute;
        margin: 5px;
    }
    .stock-item {
        width: 22px;
        height: 21px;
        font-size: 18px;
        color: #cc0000;
        display: inline-table;
        border: 1px solid #eee;
        vertical-align: middle;
        margin: 0px;
        padding: 0px;
        text-align: center;
        border-collapse: collapse;
    }
    .component-item {
        width: 22px;
        height: 22px;
        font-size: 20px;
        display: inline-table;
        border: 1px solid #eef;
        vertical-align: middle;
        margin: 1px;
        padding: 0px;
        border-collapse: collapse;
    }
    .component-img {
        width: 22px;
        height: 27px;
        font-size: 20px;
        display: inline-table;
        border: 1px solid #eef;
        vertical-align: middle;
        margin: 1px;
        padding: 3px 1px;
        border-collapse: collapse;;
    }

    .hanzi-component-index {
        border: 1px solid #eef;
    }


</style>


<span title="显示" class="glyphicon glyphicon-align-justify pull-right" style="color: #2e6da4; border: 5px solid #eef; background: #eef; margin-top: -2px; display:none" id="toggle2"></span>

<div class="hanzi-component-index pull-right" id="hanzi-component" style="width:50%">
    <span title="隐藏" class="glyphicon glyphicon-align-justify pull-right" style="color: #2e6da4; border: 5px solid #eef; background: #eef; margin-top: -2px; z-index:100" id="toggle1"></span>
    <div style="width:90%; margin: 10px 5px;" >
        <div class="input-group add-on" >
            <input class="form-control" placeholder="请输入笔画、笔顺检索部件..." name="srch-term" id="search" type="text" oninput="FindMatch()">
            <div class="input-group-btn">
                <a class="btn btn-default" title="帮助" href="/article/component-help" target="blank" style="font-weight:bold;">?</a>
            </div>
        </div>
        <div id="msg" style="color:#cc0000"></div>
	</div>

    <div id="output">
        <?php foreach ($components as $stock_num => $stock_array): ?>
            <span class="stock-item"><?= $stock_num?></span>
            <?php
            $i = 1;
                foreach ($stock_array as $key => $value) {
                    if(mb_strlen($key,'utf-8') == 1) {
                        echo "<span class='component-item' value='" . $value . "'>";
                        echo $key;
                    } else {
                        echo "<span>";
                        $path =  '/img/components/' . $key . '.png';
                        echo "<img class='component-img' src='$path' alt='$value'>";
                    }
                    echo "</span>";
            } ?>
        <?php endforeach; ?>
    </div>
    </div>
</div>

<?php

// $comp = json_decode('@web/js/component.js');

$this->registerJsFile('@web/js/component.js', ['depends' => 'frontend\assets\FrontendAsset']);

$script = <<<SCRIPT
    $('.component-item').click(function() {
        var value = $('#hanzisetsearch-param').val() + $(this).text();
        $('#hanzisetsearch-param').val(value);
    });
    $('.component-img').click(function() {
        var value = $('#hanzisetsearch-param').val() + $(this).attr("alt");
        $('#hanzisetsearch-param').val(value);
    });
    $('#searchIds-clear').click(function() {
        $('#hanzisetsearch-param').val('');
    });

    $('#toggle1').click(function() {
        $('#hanzi-component').hide(500);
        $('#toggle1').hide();
        $('#toggle2').show();
    });
    $('#toggle2').click(function() {
        $('#hanzi-component').show(200);
        $('#toggle1').show();
        $('#toggle2').hide();
    });

    // 计算四字节汉字的长度
    var uniLen = function(str){
        var total = 0, 
            c, 
            i, 
            len,
            sub;

        for(i = 0, len = str.length; i < len; i++){
            sub = str.substr(i);
            c = sub.charCodeAt(0);
            if ((c >= 0xD800) && (c <= 0xDBFF))  
                c = ((c - 0xD800) << 10) + sub.charCodeAt(1) + 0x2400;

            total += 1;
            if(c >= 0x20000) {
                i++;
            }
        }
        return total;
    };

    // 部件检索
    var GetMatch=function(search, limit)
    {
        // 构造正则检索式
        search = search.trim();
        var regSearch = null;
        if (search == ""){  // 检索式为空
            regSearch = new RegExp("\d*[a-z]*");
        } else if (m4 = search.match(/^0([bwzysxjc]*)$/)) { // 结构符
            regSearch = new RegExp("^0" + m4[1]);
        } else if (m1 = search.match(/^([hspnz]{2,})$/)) {     // 纯笔顺
            regSearch = new RegExp("" + m1[1]);
        } else if (m2 = search.match(/^([hspnz]{2,})(\d+)$/)) { // 先笔顺后笔画
            var stock = m2[1].length + parseInt(m2[2]);
            regSearch = new RegExp("^" + stock + m2[1]);
        } else if (m3 = search.match(/^(\d*)(\s*)([hspnz]*)$/)) { // 先笔画后笔顺
            if (m3[1] != "" && m3[2] != "" && m3[3] != "") {
                regSearch = new RegExp("^" + m3[1] + ".*" + m3[3]);
            } else if (m3[3] == "") {
                regSearch = new RegExp("^" + m3[1] + "[hspnz]*$");
            } else if (m3[2] == "") {
                regSearch = new RegExp("^" + m3[1] + m3[3]);
            }
        } else {
            return false;
        }

        // 查找
        var r = [];
        var lastStock = -1, curStock = -1;
        for (var i in hanziComponents)
        {
            var p = hanziComponents[i];
            var value = "";
            if (p.search.match(regSearch)) {
                var m = p.search.match(/(\d+)/);
                if (m)
                    curStock = parseInt(m[1]);
                if (curStock != lastStock) {
                    value = "<span class='stock-item' >" + curStock + "</span>";
                    r.push(value);
                    lastStock = curStock;
                }

                if (uniLen(p.display) == 1) {
                    value = "<span class='component-item' value='" + p.display + "'>" + p.display + "</span>";
                    r.push(value);
                } else {
                    value = "<span><img class='component-img' alt='" + p.input + "' src='/img/components/" + p.display + ".png' ></span>";
                    r.push(value);
                }
                if (r.length > limit)  break;
            }
        }
        return r;
    };

    var FindMatch=function()
    {
        var search = document.getElementById("search").value;
        // if (!search)
        // {
        //     document.getElementById("msg").innerHTML = "";
        //     document.getElementById("output").innerHTML = "";
        //     return;
        // }
        var limit = 1000;
        var list = GetMatch(search, limit);
        if (!list && typeof(list) == 'boolean') {
            document.getElementById("msg").innerHTML = "检索式有误！";
            return;
        } else if (!list && typeof(list) == 'object'){
            // 检索结果为空
            document.getElementById("msg").innerHTML = "检索结果为空！";
            document.getElementById("output").innerHTML = "";
            return;
        } else if (!list ){
            return;
        }
        document.getElementById("output").innerHTML = list.join(" ") + "<br>";
    };

SCRIPT;

$this->registerJs($script, \yii\web\View::POS_END);

