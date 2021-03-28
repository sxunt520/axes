var Url = 'https://api.qingcigame.com/';
var QQurl = 'https://static.qcplay.com/kol/jump_03.html'    // 加入qq群
var quantity = '';
var obj = {
    index: function() {
        this.preloading(); // 预加载
        this.indexBind(); // 绑定
        this.rulesScroll();
        this.indexAjax();   // 首页数据
        this.downloadAjax();
        this.share();       // 分享按钮
        this.canvasFollow(); // 首页鼠标跟随
    },
    news: function() {
        this.newsBind();
        this.share();
        this.nav();
        this.newsTabPage();     // 新闻tab切换
        this.newsZxList();
        this.newsAjax();
    },
    m_index: function() {
        this.m_preloading();
        this.m_rulesScroll();   // 移动端滚动监测
        this.wowAni();
        this.indexBind(); // 绑定
        this.m_indexAjax();   // 首页数据
        this.downloadAjax();
        this.canvasAni();
        this.share();       // 分享按钮
    },
    // 图片资源预加载
    preloading: function() {
        //图片预加载
        var queue = new createjs.LoadQueue();
        queue.on("progress", function(e) {
            //加载中的进度
            if (e.progress * 100 >= 10) {
                $(".loding_time span").width(parseInt(e.progress * 100) + '%')
            };
            var numbers = e.progress * 100;

        }, this);
        queue.on("complete", function(e) {
            //加载完成执行操作
            $('.loding_time').fadeOut();
            setTimeout(function() {
                $('.preloading').addClass('preloading-ani').fadeOut(1000);
                $('.index').addClass('index-show');
            },400)
            obj.nav();  // 左侧导航相关
            this.indexSwiper();
        }, this);
        queue.loadManifest([
            //加载图片，不包括后台的
            '//static.qcplay.com/tideng/official/images/pc/m_jdt-9b7ce326bb.png',
            '//static.qcplay.com/tideng/official/images/pc/m_jdt_01-ac612e83c9.png',
            '//static.qcplay.com/tideng/official/images/pc/m_jdt_02-1f8555b6c3.png',
            '//static.qcplay.com/tideng/official/images/pc/m_dt_t-50d55e2138.png',
            '//static.qcplay.com/tideng/official/images/pc/m_dt_sz-0adcd1c653.png',
            '//static.qcplay.com/tideng/official/images/pc/logo-d5016309eb.png',
            '//static.qcplay.com/tideng/official/images/pc/logo_img-c736e476e1.png',
            '//static.qcplay.com/tideng/official/images/pc/mengban-291323a55e.png',
            '//static.qcplay.com/tideng/official/images/pc/preloading_role-4f0e90ce20.png',
            '//static.qcplay.com/tideng/official/images/pc/shine2-57a917767b.png',
            '//static.qcplay.com/tideng/official/images/pc/title_all-13c030835d.png',
            '//static.qcplay.com/tideng/official/images/pc/title_img-9ca42e2c1e.png',
            '//static.qcplay.com/tideng/official/images/pc/vertical_text_all-cc946763f4.png',
            '//static.qcplay.com/tideng/official/images/pc/video_btn-c0b2667741.png',
            '//static.qcplay.com/tideng/official/images/pc/line_bg-7a6c8c213e.png',
            '//static.qcplay.com/tideng/official/images/pc/icon_img-4552839628.png',
            '//static.qcplay.com/tideng/official/images/pc/content_bg_01-4152784850.jpg',
            '//static.qcplay.com/tideng/official/images/pc/arrow_b-59f2fd502a.png',
            '//static.qcplay.com/tideng/official/images/pc/code_img_bg-b6ca42f89b.png'
        ]);
    },
    // 导航相关
    nav: function() {
        setTimeout(function() {
            $('.float-nav').addClass('float-nav-ani');
        },400)
        $('.icon').on('click', 'a', function(event) {
            var _this = $(this);
            _this.addClass('icon-changes').siblings().removeClass('icon-changes');
            for (var i = 1; i < 5; i++) {
                $('.code-hiden-box').removeClass('code-box-'+i+'');
            };
            $('.code-hiden-box').addClass('code-box-'+_this.attr('data')+'');
        });
    },
    // canvas循环动画
    canvasAni: function() {
        var wnAni = new Image;        
        wnAni.src = '//static.qcplay.com/tideng/official/images/mobile/ani_bg-9e57bbc64e.png';
        function sprite (options) {
            var that = {},
                frameIndex = 0,
                tickCount = 0,
                ticksPerFrame =  options.ticksPerFrame || 0,
                numberOfFrames = options.numberOfFrames || 1;            
            that.context = options.context;
            that.width = options.width;
            that.height = options.height;
            that.image = options.image;
            that.loop = options.loop;            
            //渲染函数
            that.render = function () {
                //清空 Canvas
                that.context.clearRect(0, 0, that.width, that.height);
                //绘制图像，产生动画效果
                that.context.drawImage(
                    that.image,                                 //图像
                    frameIndex * that.width / numberOfFrames,   //开始剪切的 X 坐标位置
                    0,                                          //开始剪切的 Y 坐标位置
                    that.width / numberOfFrames,                //被剪切图像的宽度
                    that.height,                                //被剪切图像的高度
                    0,                                          //在画布上放置图像的 X 坐标位置
                    0,                                          //在画布上放置图像的 Y 坐标位置
                    that.width / numberOfFrames,                //要使用的图像的宽度
                    that.height);                               //要使用的图像的高度
            };
            
            //更新循环函数
            that.update = function () {
                tickCount += 1;
                if (tickCount > ticksPerFrame) {
                    tickCount = 0;
                    //frameIndex 重新赋值
                    if (frameIndex < numberOfFrames - 1) {
                        frameIndex += 1;
                    } 
                    else if (that.loop) {     //重新循环
                        frameIndex = 0;
                    }
                }
            };
            return that;
        }

        //初始化
        var liquidCanvas = document.getElementById('wnAni');
        liquidCanvas.width = 750;
        liquidCanvas.height = 1327;
        //硬币雪碧图对象
        var liquid = sprite ({
            context: liquidCanvas.getContext("2d"),
            width: 8250,            //宽
            height: 1327,            //高
            // width: 1000,            //宽
            // height: 100,            //高
            image : wnAni,      //硬币图
            ticksPerFrame : 11,      //旋转速度（60/4=15fps每秒）
            numberOfFrames: 11,     //一共11帧
            loop: true              //是否循环
        })
        //循环
        function gameLoop () {
            window.requestAnimationFrame(gameLoop);
            
            liquid.update();
            liquid.render();
        }
        //硬币图片加载完成后执行 gameLoop 函数
        wnAni.addEventListener("load", gameLoop);

        $('#wnAni').css({
            'width': '100%',
            'height': '100%'
        });
    },
    // 首页轮播
    indexSwiper: function() {
        var mySwiper = new Swiper('.swiper-container', {
            direction: 'vertical',
            slidesPerView : 'auto',
            pagination: {
                el: '.swiper-pagination',
                clickable :true,
            },
            spaceBetween: 0,
            mousewheel: true,
            resistanceRatio : 0,
            on: {
                slideChangeTransitionEnd: function(){
                    var ac_index = this.activeIndex+1;
                    $('.float-nav  .nav-li-0'+ac_index+'').addClass('select').siblings().removeClass('select');
                },
            },
        });

        var swiper = new Swiper('.swiper-container0', {
            pagination: {
                el: '.swiper-pagination',
                clickable :true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            paginationClickable: true,
            spaceBetween: 30,
        })

        var swiper2 = new Swiper('.swiper-container2', {
            paginationClickable: true,
            spaceBetween: 30,
            observer:true,
            observeParents:true,
            on: {
                slideChangeTransitionEnd: function(){
                    var ac_index = this.activeIndex+1;
                    $('.swiper-container2 .btn-'+ac_index+'').addClass('select').siblings().removeClass('select');
                },
            },
        })

        var swiper4 = new Swiper('.swiper-container4', {
            paginationClickable: true,
            spaceBetween: 30,
            observer:true,
            observeParents:true,
            on: {
                slideChangeTransitionEnd: function(){
                    var ac_index = this.activeIndex+1;
                    $('.swiper-container4 .btn-'+ac_index+'').addClass('select').siblings().removeClass('select');
                },
            },
        })

        function Ani(btn, page, change) {
            $(btn).on('click', function() {
                $(btn).addClass('select').siblings().removeClass('select');
                // 深渊资讯
                if(change == 1) {
                    swiper2.slideToLoop(page, 500, true)
                    return
                }
                // 游戏攻略
                if(change == 2) {
                    swiper4.slideToLoop(page, 500, true)
                    return
                }
                // 首页轮播
                if(change == 3) {
                    mySwiper.slideToLoop(page, 500, true)
                    return
                }
            });
        };

        // 深渊资讯
        var ani1 = Ani(".swiper-container2 .btn-1", '0', '1');
        var ani2 = Ani(".swiper-container2 .btn-2", '1', '1');
        var ani3 = Ani(".swiper-container2 .btn-3", '2', '1');

        // 游戏攻略
        var ani4 = Ani(".swiper-container4 .btn-1", '0', '2');
        var ani5 = Ani(".swiper-container4 .btn-2", '1', '2');
        var ani6 = Ani(".swiper-container4 .btn-3", '2', '2');
        var ani7 = Ani(".swiper-container4 .btn-4", '3', '2');

        // 首页轮播
        var ani8 = Ani(".float-nav .nav-li-01", '0', '3');
        var ani9 = Ani(".float-nav .nav-li-02", '1', '3');
        var ani10= Ani(".float-nav .nav-li-03", '2', '3');
        var ani11= Ani(".float-nav .nav-li-04", '3', '3');

        function t(t, a, s) {
            var n = 1 * s.attr("data-index");
            if (! (13 > n - d && n - d > 2 || n - d > -13 && -2 > n - d)) {
                d = n,
                h = n,
                t = parseInt(t),
                a = parseInt(a);
                var p = Math.abs(t - a),
                f = 0;
                3 > p ? f = t > a ? c + p * i: c - p * i: a > 2 && 4 > t ? f = c + (5 - p) * i: 4 > a && t > 2 && (f = c - (5 - p) * i),
                o.css("transform", "rotate(" + -f + "deg)"),
                e.css("transform", "rotate(" + f + "deg)"),
                r = a,
                c = f,
                v.slideTo(r, 1e3, !1),
                o.removeClass("active"),
                s.addClass("active"),
                s.siblings().css("opacity", 0),
                "0" == s.attr("data-index") ? (o.eq( - 1).css("opacity", 1), o.eq( - 2).css("opacity", 1)) : "1" == s.attr("data-index") ? o.eq( - 1).css("opacity", 1) : "14" == s.attr("data-index") ? (o.eq(0).css("opacity", 1), o.eq(1).css("opacity", 1)) : "13" == s.attr("data-index") && o.eq(0).css("opacity", 1),
                s.css("opacity", 1),
                s.prev().css("opacity", 1),
                s.prev().prev().css("opacity", 1),
                s.next().css("opacity", 1),
                s.next().next().css("opacity", 1)
            }
        }
        function a() {
            p = setInterval(function() {
                var a = void 0;
                0 > h - 1 ? h = 14 : h -= 1,
                a = 0 > r - 1 ? 4 : r - 1,
                t(r, a, o.eq(h))
            },
            5e3)
        }
        function s() {
            clearInterval(p)
        }
        var e = $(".char-pagi-container"),
        i = 24,
        c = 0,
        r = 0,
        n = void 0,
        o = $(".char-pagi"),
        d = 0,
        h = 0,
        p = null,
        v = new Swiper("#js_char_swiper", {
            speed: 500,
            effect: "fade",
            fadeEffect: {
                crossFade: !0
            },
            simulateTouch: !1
        }),
        f = e.width();
        n = f / 2.5;
        var l = e.height(),
        u = Math.PI,
        y = 2 * Math.PI / o.length;
        o.each(function() {
            var t = Math.round(f / 2 + n * Math.cos(u) - $(this).width() / 2),
            a = Math.round(l / 2 + n * Math.sin(u) - $(this).height() / 2);
            $(this).css({
                left: t + "px",
                top: a + "px"
            }),
            u += y
        }),
        o.click(function() {
            var a = $(this),
            e = $(this).attr("data-char-index");
            s(),
            t(r, e, a)
        }),
        t(0, r, o.eq(h)),
        a()

    },
    downloadAjax: function() {
        // 下载链接
        function download() {
            var game_id = 33;
            var tap_url = 'https://l.taptap.com/aOoEKn8V';
            var appo_url= 'https://tideng.qingcigame.com/appointment/';
            $.ajax({
                type : "get",
                url: 'https://mapi.qingcigame.com/get_url?game_id='+game_id+'',
                dataType: 'json',
                success: function(json) {
                    // 苹果下载
                    $('.app-btn').attr('href', json.data.ios_down_url);
                    // 安卓下载
                    $('.and-btn').attr('href', json.data.android_down_url);
                    // taptap下载
                    $('.taptap-btn').attr('href', tap_url);
                    // 预约跳转地址
                    $('.appointment-btn').attr('href', appo_url);

                    // 移动端设备
                    if (/(iphone)/i.test(navigator.userAgent)) {
                        //苹果用户下载
                        $('.download-btn').attr('url_data',json.data.ios_down_url);
                        return
                    } else {
                        //安卓用户下载
                        $('.download-btn').attr('url_data',json.data.android_down_url);
                        return
                    }

                }
            })
        }download();

        
        $('.download-btn').click(function(event) {
            var url_data = $(this).attr('url_data');
            var ua = navigator.userAgent.toLowerCase();
            // 微信
            if (ua.match(/MicroMessenger/i) == "micromessenger") {
                $('.download-wrap').fadeIn();
                return
            } else {
                location.href = url_data
            }
        });

        // 关闭下载提示
        $('.download-wrap').on('click', function() {
            $(this).fadeOut();
        });

    },
    // 绑定
    indexBind: function() {

        $('.wx-btn').click(function(event) {
            $('.wx-code-wrap').fadeIn();
        });

        $('.wx-code-wrap').on('click', '.close-btn', function(event) {
            close();
        });

        // 关闭视频弹窗
        var Media = document.getElementById('media-play');
        $('.video-wrap').on('click', '.close-btn', function(event) {
            Media.pause();
            close();
        });


        $('.video-btn').on('click', function(event) {
            Media.play();
            $('.video-wrap').fadeIn();
            openA();
        });

        $('.share-btn').on('click', function(event) {
            $('.share-wrap').fadeIn();
        });

        // 关闭分享提示
        $('.share-wrap').on('click', function() {
            $(this).fadeOut();
        });
        


        // 关闭动画
        function close() {
            $('.popup_wrap').fadeOut().find('.popup_box').removeClass('ani');
        }

        // 点击关闭弹窗
        $('.close-bg').click(function(event) {
            close();
        });

        // 开启动画
        function openA() {
            setTimeout(function() {
                if (!!window.ActiveXObject || "ActiveXObject" in window) {
                    $('.popup_box').addClass('ani-ie');
                } else {
                    $('.popup_box').addClass('ani');
                }
            }, 200);
            setTimeout(function() {
                $('.close').addClass('closeAni')
            }, 300);
        }

        // 关闭动画
        function close() {
            $('.popup_wrap').fadeOut().find('.popup_box').removeClass('ani');
        }
    },
    indexAjax: function() {
        var index = layer.load(2, {shade: [0.1, '#fff']});
        // 首页新闻轮播部分数据
        function indexData() {
            $.ajax({
                url: Url+"web/lantern/pc/data/index?game_id=33",
                type: "GET",
                xhrFields: {
                    withCredentials: true
                },
                success: function(json) {
                    var linkHtml = '';
                    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                        linkHtml = 'news_mobile.html'
                    } else {
                        linkHtml = 'news.html'
                    }

                    // 轮播
                    // var banner = json.data.articleList.banner;
                    // $.each(banner, function(index, values) {
                    //     var addHtml = '<div class="swiper-slide">'+
                    //         '<a href="'+linkHtml+'?id='+values.id+'">'+
                    //             '<div class="t-img">'+
                    //                 '<img src="'+values.image+'">'+
                    //             '</div>'+
                    //             '<div class="b-info">'+  
                    //                '<h1>'+values.article_excerpt+'</h1>'+
                    //                 '<p>'+values.article_title+'</p>'+
                    //                '<span>'+values.created_at+'</span>'+
                    //             '</div>'+
                    //         '</a>'+
                    //     '</div>';
                    //     $('.news-module .swiper-l .swiper-wrapper').append(addHtml);
                    // });
                    // 
                    var banner = json.data.articleList.banner;
                    $.each(banner, function(index, values) {
                        var addHtml = '<div class="swiper-slide">'+
                            '<a href="'+values.url+'&cate_id='+values.cate_id+'">'+
                                '<div class="t-img">'+
                                    '<img src="'+values.image+'">'+
                                '</div>'+
                                '<div class="b-info">'+  
                                   '<span>'+values.created_at+'</span>'+
                                   '<h1>'+values.name+'</h1>'+
                                    '<p>'+values.title+'</p>'+
                                '</div>'+
                            '</a>'+
                        '</div>';
                        $('.news-module .swiper-l .swiper-wrapper').append(addHtml);
                    });

                    // 新闻
                    var newsInfo = json.data.articleList.news;
                    // pc端渲染
                    $.each(newsInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                    '<p>'+values.article_excerpt+'</p>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-news-info-ul').append(addHtml);                        
                    });
                    

                    // 公告
                    var noticeInfo = json.data.articleList.notice;
                    // pc端渲染
                    $.each(noticeInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                    '<p>'+values.article_excerpt+'</p>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-notice-info-ul').append(addHtml);
                    });

                    // 活动
                    var activityInfo = json.data.articleList.activity;
                    // pc端渲染
                    $.each(activityInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                    '<p>'+values.article_excerpt+'</p>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-activity-info-ul').append(addHtml);
                    });

                    // 2-游戏攻略部分
                    // 推荐
                    var bannerArticleInfo = json.data.articleList.banner_article;
                    $.each(bannerArticleInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<dl>'+
                                    '<dt><img src="'+values.thumbnail+'"></dt>'+
                                    '<dd>'+values.article_title+'</dd>'+
                                '</dl>'+
                            '</a>'+
                        '</li>';
                        $('.recommend-box ul').append(addHtml);
                    });

                    // 新手攻略
                    var xinshouInfo = json.data.articleList.xinshou;
                    // $.each(xinshouInfo, function(index, values) {
                    //     var addHtml = '<li>'+
                    //         '<a href="'+linkHtml+'?id='+values.id+'">'+
                    //            '<span>'+values.created_at+'</span>'+
                    //             // '<h1>'+values.article_title+'</h1>'+
                    //             '<p>'+values.article_title+'</p>'+
                    //         '</a>'+
                    //     '</li>';
                    //     $('.novice-info-ul').append(addHtml);
                    // });
                    // 新手攻略pc,左侧
                    $.each(xinshouInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-novice-info-ul-l').append(addHtml);
                    });
                    // 新手攻略pc,右侧
                    $.each(xinshouInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<span>'+values.created_at+'</span><p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.pc-novice-info-ul-r').append(addHtml);
                    });


                    // 宠物攻略
                    var petsInfo = json.data.articleList.chongwu;
                    $.each(petsInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-pets-info-ul-l').append(addHtml);
                    });

                    $.each(petsInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<span>'+values.created_at+'</span><p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.pc-pets-info-ul-r').append(addHtml);
                    });
                    
                    // 进阶攻略
                    var jinjieInfo = json.data.articleList.jinjie;
                    $.each(jinjieInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<div class="l-img">'+
                                    '<img src="'+values.thumbnail+'">'+
                                '</div>'+
                                '<div class="r-info">'+  
                                   '<h1>'+values.article_title+'</h1>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</li>';
                        $('.pc-advanced-info-ul-l').append(addHtml);
                    });
                    
                    $.each(jinjieInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'&cate_id='+values.cate_id+'">'+
                                '<span>'+values.created_at+'</span><p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.pc-advanced-info-ul-r').append(addHtml);
                    });
                    

                },error: function() {
                    layer.msg("服务器请求失败", {time: 2000});
                },
                complete: function() {
                    layer.close(index);
                }
            })
        }indexData();

        // 关闭下载提示
        $('.download-wrap').on('click', function() {
            $(this).fadeOut();
        });

        // 首页二维码数据
        function configInfo() {
            $.ajax({
                url: Url+"web/configInfo?game_id=33",
                type: "GET",
                xhrFields: {
                    withCredentials: true
                },
                success: function(json) {
                    var list = json.data.list;
                    // var videoUrl = json.data.list.describe;

                    if(json.code == 200) {
                        // QQ群
                        $('.img-qq').attr('src', list.tiktok);
                        $('.gf-info-01').html(list.QQ[0]);

                        // 微博
                        $('.img-wb').attr('src', list.micro_blog)
                        $('.gf-info-02').html(list.QQ[1]);

                        // 公众号
                        $('.img-wx').attr('src', list.official_account_image)
                        $('.gf-info-03').html(list.QQ[2]);

                        // 客服QQ
                        $('.img-kf').attr('src', list.customer_service_image)
                        $('.gf-info-04').html(list.QQ[3]);

                        // 下载图片
                        $('.img-download').attr('src', list.down_image_url)

                        // 视频链接
                        // $('#media-play').attr('src', videoUrl);
                    }

                },error: function() {
                    layer.msg("服务器请求失败", {time: 2000});
                },
                complete: function() {
                    layer.close(index);
                }
            })
        }configInfo()
    },
    m_indexAjax: function() {
        var index = layer.load(2, {shade: [0.1, '#fff']});
        // 首页新闻轮播部分数据
        function indexData() {
            $.ajax({
                url: Url+"web/lantern/pc/data/index?game_id=33",
                type: "GET",
                xhrFields: {
                    withCredentials: true
                },
                success: function(json) {
                    var linkHtml = '';
                    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                        linkHtml = 'news_mobile.html'
                    } else {
                        linkHtml = 'news.html'
                    }

                    // 轮播
                    var banner = json.data.articleList.banner;
                    $.each(banner, function(index, values) {
                        var addHtml = '<div class="swiper-slide">'+
                            '<a href="'+ values.url +'">'+
                                '<div class="t-img">'+
                                    '<img src="'+values.image+'">'+
                                '</div>'+
                                '<div class="b-info">'+  
                                   '<h1>'+values.article_excerpt+'</h1>'+
                                    '<p>'+values.article_title+'</p>'+
                                   '<span>'+values.created_at+'</span>'+
                                '</div>'+
                            '</a>'+
                        '</div>';
                        $('.news-module .swiper-l .swiper-wrapper').append(addHtml);
                    });

                    // 新闻
                    var newsInfo = json.data.articleList.news;
                    // 移动端渲染
                    $.each(newsInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.news-info-ul').append(addHtml);                        
                    });

                    // 公告
                    var noticeInfo = json.data.articleList.notice;
                    // 移动端渲染
                    $.each(noticeInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.notice-info-ul').append(addHtml);
                    });

                    // 活动
                    var activityInfo = json.data.articleList.activity;
                    // 移动端渲染
                    $.each(activityInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.activity-info-ul').append(addHtml);
                    });
                    
                    // 2-游戏攻略部分
                    // 推荐
                    var bannerArticleInfo = json.data.articleList.banner_article;
                    $.each(bannerArticleInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                                '<dl>'+
                                    '<dt><img src="'+values.thumbnail+'"></dt>'+
                                    '<dd>'+values.article_title+'</dd>'+
                                '</dl>'+
                            '</a>'+
                        '</li>';
                        $('.recommend-box ul').append(addHtml);
                    });

                    // 新手攻略
                    var xinshouInfo = json.data.articleList.xinshou;
                    $.each(xinshouInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.novice-info-ul').append(addHtml);
                    });

                    // 宠物攻略
                    var petsInfo = json.data.articleList.chongwu;
                    $.each(petsInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.pets-info-ul').append(addHtml);
                    });
                    
                    // 进阶攻略
                    var jinjieInfo = json.data.articleList.jinjie;
                    $.each(jinjieInfo, function(index, values) {
                        var addHtml = '<li>'+
                            '<a href="'+linkHtml+'?id='+values.id+'">'+
                               '<span>'+values.created_at+'</span>'+
                                '<p>'+values.article_title+'</p>'+
                            '</a>'+
                        '</li>';
                        $('.advanced-info-ul').append(addHtml);
                    });

                },error: function() {
                    layer.msg("服务器请求失败", {time: 2000});
                },
                complete: function() {
                    layer.close(index);
                }
            })
        }indexData();
    },
    newsBind: function() {
        $('.news').addClass('index-show');
        $('.float-nav').addClass('float-nav-ani');
    },
    newsAjax: function() {
        var id = this.getQueryVariable('id');
            cate_id = this.getQueryVariable('cate_id');
            if(cate_id == 40) $('.tab-name').html('新闻');
            if(cate_id == 41) $('.tab-name').html('公告');
            if(cate_id == 42) $('.tab-name').html('活动');
            if(cate_id == 44) $('.tab-name').html('玩法介绍');
            if(cate_id == 45) $('.tab-name').html('新手攻略');
            if(cate_id == 46) $('.tab-name').html('宠物攻略');
            if(cate_id == 47) $('.tab-name').html('进阶攻略');
        var index = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            url: Url+"web/article/"+id,
            type: "GET",
            xhrFields: {
                withCredentials: true
            },
            success: function(json) {
                if(json.code == 200) {
                    var title = json.data.list.article_title;
                    var origin = json.data.list.origin;
                    var times = json.data.list.created_at;
                    var content = json.data.list.article_content;

                    var last = json.data.last;
                    var next = json.data.next;

                    var linkHtml = '';

                    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                        linkHtml = 'news_mobile.html'
                    } else {
                        linkHtml = 'news.html'
                    }

                    last == '' ? $('.prev-page').hide() : $('.prev-page a').html('上一篇：'+last.article_title).attr('href', ''+linkHtml+'?id='+last.id+'&cate_id='+cate_id)
                    next == '' ? $('.next-page').hide() : $('.next-page a').html('下一篇：'+next.article_title).attr('href', ''+linkHtml+'?id='+next.id+'&cate_id='+cate_id)


                    $('.title h2').html(title);
                    $('.title p').html('作者：'+origin + '&nbsp&nbsp&nbsp&nbsp时间：'+times);
                    $('.content').append(content);
                }

            },error: function() {
                layer.msg("服务器请求失败", {time: 2000});
            },
            complete: function() {
                layer.close(index);
            }
        })
    },
    // 滚动监测
    rulesScroll: function(changeA) {
        var _obj = document.getElementById("nav");
        document.onscroll = function () {
            var ot = 600;
            var st = document.body.scrollTop || document.documentElement.scrollTop;
            if(st <= 1000) {
                $('.nav-li-01').addClass('select').siblings().removeClass('select');
            }
            if(st >= 1000 && st <= 2080) {
                $('.nav-li-02').addClass('select').siblings().removeClass('select');
            }
            if(st >= 2080 && st <= 3180) {
                $('.nav-li-03').addClass('select').siblings().removeClass('select');
            }
            if(st >= 3180 && st <= 4180) {
                $('.nav-li-04').addClass('select').siblings().removeClass('select');
            }
            if(st >= 4180 ) {
                $('.nav-li-05').addClass('select').siblings().removeClass('select');
            }
        }
        // 锚节点动画滚动
        // $(".nav-label").on('click', 'li a', function(event) {
        //     navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i) ? ot = -0 : ot = 0;
        //     $("html, body").animate({
        //         scrollTop: $($(this).attr("href")).offset().top+ot + "px"
        //     }, {
        //         duration: 500,
        //         easing: "swing"
        //     });
        // });
    },
    // 移动端滚动监测
    m_rulesScroll: function(changeA) {
        document.onscroll = function () {
            var ot = 600;
            var st = document.body.scrollTop || document.documentElement.scrollTop;
            if(st <= 2000) {
                // $('.index').removeClass('index-none')
                $('.content-01').show()
            } else {
                // $('.index').addClass('index-none')
                $('.content-01').hide()
            }
        }
    },
    wowAni: function() {
        var wow = new WOW({ 
            boxClass: 'wow', 
            animateClass: 'animated', 
            offset: 0, 
            mobile: true, 
            live: true 
            }); 
        wow.init();
    },
    share: function() {
        // 分享部分
        $('.share-2').share({sites: ['wechat', 'weibo', 'qq', 'qzone']});
        $('#share-3').share({sites: ['weibo', 'qq', 'qzone']});
    },
    // 获取当前hash的具体参数
    getQueryVariable: function(variable) {
        var query = window.location.href.split('?')[1];
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
                if(pair[0] == variable){return pair[1];}
            }
        return(false);
    },
    // 最新、综合、活动跳转
    newsTabPage: function() {
        $('.all-list').on('click', 'li', function(event) {
            $(this).addClass('on').siblings('a').removeClass('on');
            var cate_id = $(this).attr('cate-id');
            window.location.href = window.location.href.split("?")[0]+'?cate_id='+cate_id+'&page=1'

        });
    },
    // 新闻列表接口
    newsZxList: function(urls) {
        cate_id = this.getQueryVariable('cate_id');
        page    = this.getQueryVariable('page');
        current = window.location.href.split("?")[0];
        index = layer.load(2, {shade: [0.1, '#fff']});
        $('.li-tab-'+cate_id+'').addClass('on').siblings().removeClass('on');
        if(cate_id == 40) $('.tab-name').html('新闻');
        if(cate_id == 41) $('.tab-name').html('公告');
        if(cate_id == 42) $('.tab-name').html('活动');

        function newsAjax() {
            $.ajax({
                url: Url+'web/article?&game_id=33',
                type: "GET",
                data: {
                    cate_id: cate_id,
                    page: page,
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function(json) {
                    if(json.code == 200) {
                        var current_page = json.data.articleList.current_page;  // 当前页数
                        var last_page = json.data.articleList.last_page;        // 总共页数
                        var next_page_url = json.data.articleList.next_page_url;// 后一页
                        // 渲染列表
                       // $.each(json.data.articleList.data, function(index, values) {
//							var dtHtml = `<li>
//                                    <a title="${values.article_title}" href="news_mobile.html?id=${values.id}&cate_id=${values.cate_id}">
//                                        <h2>${values.article_title}</h2>
//                                        <span>${values.created_at}</span>
//                                    </a>
//                                </li>`;
//                            $('.list-wrap ul').append(dtHtml);
//                        });
                        $('.current_page').html(current_page);      // 当前页数
                        $('.last_page').html(last_page);            // 总共页数
                        if(next_page_url === null) {                // 下一页
                            $('.more-btn a').attr('data', '').html('已经没有了'); // 没有最后一页
                        } else {
                            $('.more-btn a').attr('data', next_page_url);
                        }
                    }else {
                        layer.msg("服务器请求失败", {time: 2000});
                    }

                },error: function() {
                    layer.msg("服务器请求失败", {time: 2000});
                },
                complete: function() {
                    layer.close(index);
                }
            })            
        }
        newsAjax(urls);
        $('.more-btn').on('click', 'a', function(event) {
            if($(this).attr('data') == '') {
                layer.msg("已经没有了", {time: 2000});
            }else {
                urls = $(this).attr('data');
                newsAjax(urls);
            }            
        });
        for (var i = $('.link-box-02 div a').length; i >= 1; i--) {
            if(cate_id == i) $('.link-box-02 div a:nth-child('+i+')').addClass('on');
        };
    },
    // 图片资源预加载
    m_preloading: function() {
        //图片预加载
        var queue = new createjs.LoadQueue();
        queue.on("progress", function(e) {
            //加载中的进度
            if (e.progress * 100 >= 10) {
                $(".loding_time span").width(parseInt(e.progress * 100) + '%')
            };
            var numbers = e.progress * 100;

        }, this);
        queue.on("complete", function(e) {
            //加载完成执行操作
            $('.loding_time b').fadeOut();
            $('.loding_time span').addClass('hidden');
            $('.preloading').fadeOut();
            setTimeout(function() {
                $('.index, .news').removeClass('landing');
            },200)
            this.m_indexSwiper(); // 绑定
        }, this);
        queue.loadManifest([
            //加载图片，不包括后台的
            '//static.qcplay.com/tideng/official/images/mobile/m_dt_sz-f0b52ee19c.png',
            '//static.qcplay.com/tideng/official/images/mobile/m_dt_t-21488abf42.png',
            '//static.qcplay.com/tideng/official/images/mobile/m_jdt-87d7650f68.png',
            '//static.qcplay.com/tideng/official/images/mobile/m_jdt_01-592e17d14f.png',
            '//static.qcplay.com/tideng/official/images/mobile/m_jdt_02-7e3b63f432.png',
            '//static.qcplay.com/tideng/official/images/mobile/content_01_bg-73e2d01c3d.jpg',
            '//static.qcplay.com/tideng/official/images/mobile/header_bg-485a3b911a.png',
            '//static.qcplay.com/tideng/official/images/mobile/down_btn-31e5c2fe4a.png',
            '//static.qcplay.com/tideng/official/images/mobile/share_btn-326cc75ca6.png',
            '//static.qcplay.com/tideng/official/images/mobile/banner_title_01-5241ab26b7.png',
            '//static.qcplay.com/tideng/official/images/mobile/down_btn_02-d2a2499324.png',
            '//static.qcplay.com/tideng/official/images/mobile/title_02-91f6c35024.png',
            '//static.qcplay.com/tideng/official/images/mobile/title_03-76b6f0b175.png',
            '//static.qcplay.com/tideng/official/images/mobile/title_04-b62b04b954.png',
            '//static.qcplay.com/tideng/official/images/mobile/video_btn-97b0e9bd81.png'
        ]);
    },
    // 移动端首页轮播
    m_indexSwiper: function() {

        var swiper = new Swiper('.swiper-container', {
            pagination: {
                el: '.swiper-pagination',
                clickable :true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            paginationClickable: true,
            spaceBetween: 30,
            observer:true,
            observeParents:true,
        })

        var swiper2 = new Swiper('.swiper-container2', {
            paginationClickable: true,
            spaceBetween: 30,
            observer:true,
            observeParents:true,
            on: {
                slideChangeTransitionEnd: function(){
                    var ac_index = this.activeIndex+1;
                    $('.swiper-container2 .btn-'+ac_index+'').addClass('select').siblings().removeClass('select');
                },
            },
        })

        var swiper4 = new Swiper('.swiper-container4', {
            paginationClickable: true,
            spaceBetween: 30,
            observer:true,
            observeParents:true,
            on: {
                slideChangeTransitionEnd: function(){
                    var ac_index = this.activeIndex+1;
                    $('.swiper-container4 .btn-'+ac_index+'').addClass('select').siblings().removeClass('select');
                },
            },
        })

        function Ani(btn, page, change) {
            $(btn).on('click', function() {
                $(btn).addClass('select').siblings().removeClass('select');
                change == true ? swiper2.slideToLoop(page, 500, true) : swiper4.slideToLoop(page, 500, true)
            });
        };

        // 深渊资讯
        var ani1 = Ani(".swiper-container2 .btn-1", '0', true);
        var ani2 = Ani(".swiper-container2 .btn-2", '1', true);
        var ani3 = Ani(".swiper-container2 .btn-3", '2', true);

        // 游戏攻略
        var ani4 = Ani(".swiper-container4 .btn-1", '0', false);
        var ani5 = Ani(".swiper-container4 .btn-2", '1', false);
        var ani6 = Ani(".swiper-container4 .btn-3", '2', false);

        var personSwiper=new Swiper(".swiper-container-person",{
            // initialSlide :2,
            // loop: true,
        });
        var headSwiper=new Swiper(".swiper-person-head",{
            // loop: true,
            slidesPerView :5,
            // initialSlide :2,
            spaceBetween : 10,
            slideToClickedSlide: true,
            centeredSlides : true,
            controller: {
                control: personSwiper, //控制personSwiper
            },
            navigation: {
                nextEl: '.my-next',
                prevEl: '.my-prev',
            }
        });
        personSwiper.controller.control = headSwiper;//Swiper1控制Swiper2，需要在Swiper2初始化后
        headSwiper.controller.control = personSwiper;//Swiper2控制Swiper1，需要在Swiper1初始化后
    },
    // 鼠标跟随
    canvasFollow: function() {
        var myCanvas = document.getElementById('myCanvas');
        var ctx = myCanvas.getContext("2d");
        var starlist = [];
     
        function init() {
            // 设置canvas区域的范围为整个页面
            myCanvas.width = window.innerWidth;
            myCanvas.height = window.innerHeight;
        };
        init();
        // 监听屏幕大小改变 重新为canvas大小赋值
        window.onresize = init;
     
        // 当鼠标移动时 将鼠标坐标传入构造函数 同时创建一个对象
        myCanvas.addEventListener('mousemove', function(e) {
            // 将对象push到数组中，画出来的彩色小点可以看作每一个对象中记录着信息 然后存在数组中
            starlist.push(new Star(e.offsetX, e.offsetY));
        });
     
        // 随机数函数
        function random(min, max) {
            // 设置生成随机数公式
            return Math.floor((max - min) * Math.random() + min);
        };
     
     
        // 构造函数
        function Star(x, y) {
            // 将坐标存在每一个点的对象中
            this.x = x;
            this.y = y;
            // 设置随机偏移量
            this.vx = (Math.random() - 2) * 1;
            this.vy = (Math.random() - 2) * 1;
            this.color = '#DE7971';
            // 初始透明度
            this.a = 1;
            // 开始画
            this.draw();
        }
     
        // 再star对象原型上封装方法
        Star.prototype = {
            // canvas根据数组中存在的每一个对象的小点信息开始画
            draw: function() {
                ctx.beginPath();
                ctx.fillStyle = this.color;
                // 图像覆盖  显示方式 lighter 会将覆盖部分的颜色重叠显示出来
                ctx.globalCompositeOperation = 'lighter'
                ctx.globalAlpha = this.a;
                ctx.arc(this.x, this.y, 5, 0, Math.PI * 4, false);
                ctx.fill();
                this.updata();
            },
            updata: function() {
                // 根据偏移量更新每一个小点的位置
                this.x += this.vx;
                this.y += this.vy;
                // 透明度越来越小
                this.a *= 0.95;
            }
        }
        // 渲染
        function render() {
            // 每一次根据改变后数组中的元素进行画圆圈  把原来的内容区域清除掉
            ctx.clearRect(0, 0, myCanvas.width, myCanvas.height)
     
            // 根据存在数组中的每一位对象中的信息画圆
            starlist.forEach(function(ele, i) {
                ele.draw();
                // 如果数组中存在透明度小的对象 ，给他去掉 效果展示逐渐消失
                if (ele.a < 0.05) {
                    starlist.splice(i, 1);
                }
            });
            requestAnimationFrame(render);
        }
        render();
    }
}
//# sourceMappingURL=public-ec2bae2639.js.map
