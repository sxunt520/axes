(function() {
    var $gImgItem = $('.J_ImgItem');
    var $gSwitchItem = $();
    var gListSize = $gImgItem.length;
    var kLoopTime = 5000;
    var loopIntroDomList = [];
    var kLoopIntro = [
        '平台独家pick最爱佳作',
        '暖心评论，遇见万里挑一的灵魂',
        '完全免费，纯粹只为阅读而生',
        '独享精致，只属于你的时光',
    ];

    function bind() {
        $(document)
            .on('click', '#J_QrcodeText', function(e) {
                $('#J_QrcodeMain').fadeToggle(200);
                e.stopPropagation();
            })
            .on('click', function() {
                var isQRCodeHidden = $('#J_QrcodeMain').is(':hidden');
                if (!isQRCodeHidden) {
                    $('#J_QrcodeMain').fadeOut(200);
                }
            })
            .on('click', '.J_ImgItem', function(e) {
                var index = $(e.target).index();
                addStyle(index);
                e.stopPropagation();
            })
            .on('click', '.J_SwitchItem', function(e) {
                var index = $(e.target).index();
                addStyle(index);
                e.stopPropagation();
            });
    }

    function init() {
        var $switchList = $('.J_SwitchList');
        var switchListTpl = '';
        for (var i = 0; i < gListSize; i++) {
            switchListTpl += '<li class="J_SwitchItem switch-item"></li>';
        }
        for (var j = 0; j < kLoopIntro.length; j ++) {
            loopIntroDomList[j] = '<p class="intro intro-0'+ (j + 1) +'">'+ kLoopIntro[j] +'</p>';
        }
        $switchList.append($(switchListTpl));

        $gSwitchItem = $('.J_SwitchItem');

        addStyle(0);
        setInterval(function() {
            loop();
        }, kLoopTime);
    }

    function clearStyle() {
        $gImgItem.removeClass('main-img-item')
            .removeClass('prev-img-item')
            .removeClass('next-img-item')
            .removeClass('left-img-item')
            .removeClass('right-img-item');
        $gSwitchItem.removeClass('switch-item-active');
    }

    function addStyle(index) {
        clearStyle();

        var $mainImgItem;
        var $prevImgItem;
        var $nextImgItem;
        var $leftImgItem;
        var $rightImgItem;
        var $mainSwitchItem;

        $mainImgItem = $gImgItem.eq(index);
        $mainSwitchItem = $gSwitchItem.eq(index);

        if (index === 0) {
            $prevImgItem = $gImgItem.eq(gListSize - 1);
            $leftImgItem = $gImgItem.eq(gListSize - 2);
        } else if (index === 1) {
            $prevImgItem = $gImgItem.eq(index - 1);
            $leftImgItem = $gImgItem.eq(gListSize - 1);
        } else if (index > 1) {
            $prevImgItem = $gImgItem.eq(index - 1);
            $leftImgItem = $gImgItem.eq(index - 2);
        }

        if (index === (gListSize - 1)) {
            $nextImgItem = $gImgItem.eq(0);
            $rightImgItem = $gImgItem.eq(1);
        } else if (index === (gListSize - 2)) {
            $nextImgItem = $gImgItem.eq(gListSize - 1);
            $rightImgItem = $gImgItem.eq(0);
        } else if (index < (gListSize - 2)) {
            $nextImgItem = $gImgItem.eq(index + 1);
            $rightImgItem = $gImgItem.eq(index + 2);
        }
        
        $mainImgItem.addClass('main-img-item');
        $prevImgItem.addClass('prev-img-item');
        $nextImgItem.addClass('next-img-item');
        $leftImgItem.addClass('left-img-item');
        $rightImgItem.addClass('right-img-item');
        $mainSwitchItem.addClass('switch-item-active');
        resetIntroduction(index);
    }

    function resetIntroduction(index) {
        $('#J_IntroBox').html(loopIntroDomList[index]);
    }

    function loop() {
        var index = $gImgItem.filter('.main-img-item').index();
        index = index + 1;
        if (index > gListSize - 1) {
            index = 0;
        }
        addStyle(index);
        resetIntroduction(index);
    }

    bind();
    init();
})();