/**
 * Version:  1.0.0
 * User:     haojb
 * Date:     13-8-16
 * Time:     上午8:20
 * To change this template use File | Settings | File Templates.
 */


function setActionUrl(action, base_url) {
//alert(action);
    var url = self.location.href;
//alert(url);

    url = url.substr(base_url.length);
//alert(url);

    //判断首尾是否为"/"，是的话去除
    var first = url.substr(0, 1);

    if (first == "/") {
        url = url.substr(1);
    }

    var last = url.substr(-1, 1);
    if (last == "/") {
        url = url.substr(0, url.length - 2);
    }

    //清理链接中的~pre_url~之后的内容
    var location = url.indexOf("~pre_url~");
    if (location != -1){
        url = url.substring(0, location);
    }

    var url_array = url.split("/");

    var previous_url = url_array.join("~");
//alert(action + "/pre_url/" + previous_url);
    return action + "/pre_url/" + previous_url;
}

function isArray(obj) {

    return Object.prototype.toString.call(obj) === '[object Array]';

}

function isDate(dateValue) {
    var regex = new RegExp("^(?:(?:([0-9]{4}(-|\/)(?:(?:0?[1,3-9]|1[0-2])(-|\/)(?:29|30)|((?:0?[13578]|1[02])(-|\/)31)))|([0-9]{4}(-|\/)(?:0?[1-9]|1[0-2])(-|\/)(?:0?[1-9]|1\\d|2[0-8]))|(((?:(\\d\\d(?:0[48]|[2468][048]|[13579][26]))|(?:0[48]00|[2468][048]00|[13579][26]00))(-|\/)0?2(-|\/)29))))$");

    if (!regex.test(dateValue)) {

        return false;
    }
    return true;
}

function popDialog(url, width, height) {
    var params = "dialogWidth=" + width + ";dialogHeight=" + height + ";menubar=no;location=no;toolbar=no;center=yes;resizable=yes;scroll=no";

    var rv = window.showModalDialog(url, window, params);

    return rv;
}

//js截取字符串，中英文都能用
//如果给定的字符串大于指定长度，截取指定长度返回，否者返回源字符串。
//字符串，长度
function cutstr(str, len) {
    var str_length = 0;
    var str_len = 0;
    str_cut = new String();
    str_len = str.length;
    for (var i = 0; i < str_len; i++) {
        a = str.charAt(i);
        str_length++;
        if (escape(a).length > 4) {
            //中文字符的长度经编码之后大于4
            str_length++;
        }
        str_cut = str_cut.concat(a);
        if (str_length >= len) {
            str_cut = str_cut.concat("...");
            return str_cut;
        }
    }
    //如果给定字符串小于指定长度，则返回源字符串；
    if (str_length < len) {
        return str;
    }
}

function toJsonArray(jsonStr) {

    var jsonObj = eval('(' + jsonStr + ')');
    if (isArray(jsonObj)) {
        return jsonObj;
    } else {
        var jsonArr = new Array();
        for (var o in jsonObj) {
            jsonArr[o] = jsonObj[o];
        }

        return jsonArr;
    }
}

function array_keys(array) {
    if (isArray(array)) {
        var keyArr = new Array();
        for (var a in array) {
            keyArr.push(a);
        }
        return keyArr;
    } else {
        return false;
    }
}

function array_values(array) {

    if (isArray(array)) {
        var valueArr = new Array();
        for (var a in array) {

            valueArr.push(array[a]);
        }
        return valueArr;
    } else {
        return false;
    }
}

function HiddenLayer(divId) {
    var divE = document.getElementById(divId);
    divE.style.display = "none";
}

function copyToClipboard(txt) {

    if (window.clipboardData) {

        window.clipboardData.clearData();
        window.clipboardData.setData("Text", txt);
    } else  {
        alert("您所使用的浏览器无法使用剪贴板功能。");
        return false;
    }
}

//轮播图片
function loopPictures(div, ulDiv, numStyle) {
    var play_num = 1;
    //设置轮播间隔时间
    var auto_time = 3000;
    //获取图片数量
    var auto_num = $(div + " img").length;

    //获取图片高度
    var auto_width = $(div).width();
    //插入数字番号列表，并为首个列表单元添加样式
    $(div).append("<ul class=" + numStyle + "></ul>");
    for (auto_i = 1; auto_i <= auto_num; auto_i++) {
        $("."+numStyle).append("<li>" + auto_i + "</li>");
    }
    ;
    $("."+numStyle + " li:eq(0)").addClass("on");
    //为图片添加原始序号
    $(ulDiv + " li").each(function () {
        $(this).attr("order", $(this).index())
    });
    //初始化图片显示状态
    $(ulDiv + " li").hide().eq(0).appendTo(ulDiv).show();

    //轮播动画
    function play(play_num) {
        $(ulDiv + " li[order=" + play_num + "]").appendTo(ulDiv).show();
        $(ulDiv).animate({marginLeft: "-" + auto_width}, "slow", function () {
            $(ulDiv + " li[order=" + play_num + "]").siblings().hide();
            $(ulDiv).css("marginLeft", 0);
        });
        $("."+numStyle + " li").removeClass("on").eq(play_num).addClass("on");
    }

    var auto_play = false;
    auto_play = setInterval(function () {
        play(play_num)
        play_num++;
        if (play_num == auto_num) {
            play_num = 0;
        }
    }, auto_time)
    //鼠标事件
    $("."+numStyle + " li").click(function () {
        if (!$(this).hasClass("on")) {
            if (!$(ulDiv).is(":animated")) {
                $(ulDiv).stop();
                auto_stop();
                play($(this).index());
                if ($(this).index() == auto_num - 1) {
                    play_num = 0;
                }
                else {
                    play_num = $(this).index() + 1;
                }
            }
        }
    });
    //鼠标事件
    $(div).hover(function () {
        auto_stop();
    }, function () {
        auto_replay();
    });
    //停止播放
    function auto_stop() {
        clearInterval(auto_play);
    }

    //重新播放
    function auto_replay() {
        auto_play = setInterval(function () {
            play(play_num)
            play_num++;
            if (play_num == auto_num) {
                play_num = 0;
            }
        }, auto_time)
    }
}


//弹出层
var dialog;
function unblockUI(itemurl,titleName,dialogWidth,dialogHeight,divName){

    titleName=titleName==""||titleName==undefined?"弹出窗口":titleName;
    dialogWidth=isNaN(dialogWidth)?790:dialogWidth;
    dialogHeight=isNaN(dialogHeight)?590:dialogHeight;
    divName=divName==""?"dialog-form":divName;
    var iframeW = dialogWidth- 30;
    var iframeH = dialogHeight-55;
    $("#"+divName).remove();
    $("body").append('<div id="'+divName+'" title="'+titleName+'"><iframe frameBorder="0" scrolling="no"  src="'+itemurl+'" style="width:'+iframeW+'px; height:'+iframeH+'px;"></iframe></div>');
    dialog = $( "#"+divName ).dialog({
        autoOpen: false,
        height: dialogHeight,
        width: dialogWidth,
        modal: true
    });
    dialog.dialog( "open" );
}


function closeDailog(){
    dialog.dialog( "close" );
    location.reload();
}



function inputFocus(e,data,type){
    e.value = e.value == data ? "" : e.value;
    e.type = type==""?'text':type;
}
function inputBlur(e,v){
    if(e.value == ''){
        e.type = "text";
        e.value=v;
    }
}

function message(value) {
    var toast = document.createElement('div');
    toast.classList.add('mui-toast-container');
    toast.innerHTML = '<div class="'+'mui-toast-message'+'">'+value+'</div>';
    document.body.appendChild(toast);
    setTimeout(function(){
        document.body.removeChild(toast);
    },3000);
}

function pagination(url){

    var pageCount = $('#pageCount').val();

    var prefix_url = '';
    var last_url = '';
    if (url.indexOf("/page_count/") > 0){
        var position = url.indexOf("/page_count/") + "/page_count/".length;

        prefix_url = url.substring(0,position);
        last_url = url.substring(url.indexOf("/",position));
        url =  prefix_url + pageCount + last_url;
    }else{
        prefix_url = url.substring(0,url.indexOf("/per_page"));
        last_url = url.substring(url.indexOf("/per_page"));
        url =  prefix_url + "/page_count/" + pageCount + last_url;
    }

    location.href = url;
}


//返回顶部
$(document).ready(function () {

    var clickTime = new Date().getTime(), i = 0;
    $(".buttonClick").bind("click", function () {
        var nowTime = new Date().getTime();
        if (clickTime != 'undefined' && (nowTime - clickTime < 5000)) {
            alert('操作过于频繁，稍后再试');
            return false;
        } else {
            clickTime = new Date().getTime();
        }
    });


    /**
     * 返回顶部处理
     */
    var _objscroll = {
        win: $(window),
        doc: $(document),
        gotopdiv: $('#gotop')
    };

    _objscroll.win.scroll(function () {
        if (_objscroll.win.scrollTop() > _objscroll.win.height()) {
            _objscroll.gotopdiv.show();
        } else {
            _objscroll.gotopdiv.hide();
        }

    });

    //返回顶部点击
    _objscroll.gotopdiv.click(function () {//控制返回顶部
        _objscroll.win.scrollTop(0);
        return false;

    });

});