<?php

/* @var $this yii\web\View */

$this->title = '旅人计划 超S级互动故事阅读平台';
?>
<header class="header">
    <div class="header-content">
        <h1 class="header-logo"><a href="/" class="header-logo-link">旅人计划</a></h1>
    </div>
</header>
<div id="dowebok">

    <div class="section" style="background:url(/images/home_bg.jpg) no-repeat center center/cover">
        <div class="box1">
            <div class="header_title"></div>
            <div class="down_c">
                <div class="android_down"><span></span><a href="javascript:void(0);"></a></div>
                <div class="apple_down"><span></span><a href="javascript:void(0);"></a></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="myBox">
            <div class="myBox_c">
                <div class="myBox_l">
                    <div class="phone_xxx phone_xxx_1"></div>
                    <div class="phone_img phone_img_1"></div>
                    <div class="can_u_speak_chinese"><img src="/images/can_u_speak_chinese.png" width="30%"></div>
                    <div class="can_u_speak_japanese"><img src="/images/can_u_speak_japanese.png" width="30%"></div>
                </div>
                <div class="myBox_r"><img src="/images/pick_1.png" width="235" height="114"></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="myBox">
            <div class="myBox_c">
                <div class="myBox_l">
                    <div class="phone_xxx phone_xxx_2"></div>
                    <div class="phone_img phone_img_2"></div>
                    <div class="like_xing"></div>
<!--                    <a class="hover_like" href="javascript:void(0);"><span></span></a>-->
                </div>
                <div class="myBox_r"><img src="/images/pick_2.png" width="410" height="113"></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="myBox">
            <div class="myBox_c">
                <div class="myBox_l">
                    <div class="phone_xxx phone_xxx_3"></div>
                    <div class="phone_img phone_img_3"></div>

                    <div class="section-freeicon free_ico"><img src="/images/free_ico.png" width="90%"></div>
                    <div class="section-freeicon free_text1 free_xxx"><img src="/images/free_text1.png" width="30%"></div>
                    <div class="section-freeicon free_text2 free_xxx"><img src="/images/free_text2.png" width="30%"></div>
                    <div class="section-freeicon free_text3 free_xxx"><img src="/images/free_text3.png" width="30%"></div>
                    <div class="section-freeicon free_text4 free_xxx"><img src="/images/free_text4.png" width="30%"></div>

                </div>
                <div class="myBox_r"><img src="/images/pick_3.png" width="362" height="114"></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="myBox">
            <div class="myBox_c">
                <div class="myBox_l">
                    <div class="phone_xxx phone_xxx_4"></div>
                    <div class="phone_img phone_img_4"></div>

                </div>
                <div class="myBox_r"><img src="/images/pick_4.png" width="410" height="114"></div>
            </div>
        </div>
    </div>

</div>

<div id="down_float_r" style="display: none;"><a href="javascript:void(0);"></a><div class="float_qrcode"><img src="/images/down_float.png" width="103" height="110"></div></div>

<footer class="footer" id="footer" style=" display:none;"><span> © 2020 www.233i.com All rights reserved 蜀ICP备<a href="http://www.beian.miit.gov.cn/" target="_blank">18015972</a>号 川网文（2018）9245-339号
</span></footer>

<script>
    $(function(){
        $('#dowebok').fullpage({
            sectionsColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
            'navigation': true,
            loopBottom: true,  //滚动到最底部后是否滚回顶部
            //continuousVertical: true,//无感觉循环
            'navigationColor':'#E9292C',
            afterLoad: function(anchorLink, index){

                if(index == 1){
                    $('#footer').hide();
                    $('#down_float_r').hide();
                }
                if(index == 2){
                    $('#footer').fadeIn("slow");
                    $('#down_float_r').show();

                    $(".can_u_speak_chinese img").animate({
                        width: "100%",
                        height: "100%",
                    }, 100 );

                    //setTimeout(function(){
                        $(".can_u_speak_japanese img").animate({
                            width: "100%",
                            height: "100%",
                        }, 100 );
                    //}, 1000);

                }
                if(index == 3){
                    $('#footer').show();
                    $('#down_float_r').show();

                }
                if(index == 4){
                    $('#footer').show();
                    $('#down_float_r').show();

                    $(".free_xxx img").animate({
                        width: "100%",
                        height: "100%",
                    }, 100 );

                    // $(".free_ico img").animate({
                    //     width: "100%",
                    //     height: "100%",
                    // }, 500 ).animate({
                    //     width: "90%",
                    //     height: "90%",
                    // }, 500 );

                    // setTimeout(function(){
                    //     $(".free_text1 img").animate({
                    //         width: "100%",
                    //         height: "100%",
                    //     }, 500 ).animate({
                    //         width: "90%",
                    //         height: "90%",
                    //     }, 500 );
                    // }, 1000);
                    //
                    // setTimeout(function(){
                    //     $(".free_text2 img").animate({
                    //         width: "100%",
                    //         height: "100%",
                    //     }, 500 ).animate({
                    //         width: "90%",
                    //         height: "90%",
                    //     }, 500 );
                    // }, 1500);
                    //
                    // setTimeout(function(){
                    //     $(".free_text3 img").animate({
                    //         width: "100%",
                    //         height: "100%",
                    //     }, 500 ).animate({
                    //         width: "90%",
                    //         height: "90%",
                    //     }, 500 );
                    // }, 2000);
                    //
                    // setTimeout(function(){
                    //     $(".free_text4 img").animate({
                    //         width: "100%",
                    //         height: "100%",
                    //     }, 500 ).animate({
                    //         width: "90%",
                    //         height: "90%",
                    //     }, 500 );
                    // }, 2500);

                }

            },
            onLeave: function(index, direction){
                if(index == 1){
                    $('#footer').hide();
                    $('#down_float_r').hide();
                }
                if(direction == 1){
                    $('#footer').hide();
                    $('#down_float_r').hide();
                }

                if(index == 2){
                    $(".can_u_speak_chinese img").width('30%').height('30%');
                    $(".can_u_speak_japanese img").width('30%').height('30%');
                }

                if(index == 4){
                    $(".free_xxx img").width('30%').height('30%');
                }

            }
        });
        $.fn.fullpage.setScrollingSpeed(500);//滚动速度
        setInterval(function(){
            $.fn.fullpage.moveSectionDown();
        }, 10000);

        $(".android_down").mouseover(function(){
            $(".android_down span").show();
        }).mouseout(function(){
            $(".android_down span").hide();
        });

        $(".apple_down").mouseover(function(){
            $(".apple_down span").show();
        }).mouseout(function(){
            $(".apple_down span").hide();
        });

        $("#down_float_r a").mouseover(function(){
            $("#down_float_r .float_qrcode").show();
        }).mouseout(function(){
            $("#down_float_r .float_qrcode").hide();
        });

        $(".myBox_l a.hover_like").mouseover(function(){
            $(".myBox_l a.hover_like span").show();
        }).mouseout(function(){
            $(".myBox_l a.hover_like span").hide();
        });

        //适配
        $(window).resize(function(){
            autoScrolling();
        });
        function autoScrolling(){
            var $ww = $(window).width();//console.log($ww);
            if($ww < 1300){
               //alert('xxxxxx');
                var x_scroll=$ww-1000;
                if(x_scroll<70){
                    x_scroll=70;
                }
                $(".footer").height(x_scroll+'px');
                $(".footer span").css('bottom','5px');
            }else if($ww > 1300){
                $(".footer").height('300px');
                $(".footer span").css('bottom','20px');
            }
        }
        autoScrolling();


    });
</script>