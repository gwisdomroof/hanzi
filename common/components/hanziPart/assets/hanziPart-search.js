
$('#component-hide').click(function() {
    $('#hanzi-component').hide(500);
    $('#component-show').show();
});
$('#component-show').click(function() {
    $('#hanzi-component').show(200);
    $('#component-show').hide();
});

// 计算四字节汉字的长度
var uniLen = function(str) {
    var total = 0,
    c, i, len, sub;

    for (i = 0, len = str.length; i < len; i++) {
        sub = str.substr(i);
        c = sub.charCodeAt(0);
        if ((c >= 0xD800) && (c <= 0xDBFF)) c = ((c - 0xD800) << 10) + sub.charCodeAt(1) + 0x2400;

        total += 1;
        if (c >= 0x20000) {
            i++;
        }
    }
    return total;
};

// 部件检索
var GetMatch = function(search, limit) {
    // 构造正则检索式
    search = search.trim();
    var regSearch = null;
    if (search == "") { // 检索式为空
        regSearch = new RegExp("\d*[a-z]*");
    } else if (m4 = search.match(/^0([bwzysxjc]*)$/)) { // 结构符
        regSearch = new RegExp("^0" + m4[1]);
    } else if (m1 = search.match(/^([hspnz]{1,})$/)) { // 纯笔顺
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
    var lastStock = -1,
    curStock = -1;
    for (var i in hanziComponents) {
        var p = hanziComponents[i];
        var value = "";
        if (p.search.match(regSearch)) {
            var m = p.search.match(/(\d+)/);
            if (m) curStock = parseInt(m[1]);
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
            if (r.length > limit) break;
        }
    }
    return r;
};

var FindMatch = function() {
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
    } else if (!list && typeof(list) == 'object') {
        // 检索结果为空
        document.getElementById("msg").innerHTML = "检索结果为空！";
        document.getElementById("output").innerHTML = "";
        return;
    } else if (!list) {
        return;
    }
    document.getElementById("msg").innerHTML = "";
    document.getElementById("output").innerHTML = list.join(" ") + "<br>";
};