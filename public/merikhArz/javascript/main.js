$(document).ready(function () {
    let menu = $(".menuItem");
    let btnMenu = $(".btnMenu");
    let profile = $(".profile");
    let notice = $('.notice');
    let notice_desc = $('.notice-desc');
    let sliderLi = $(".slider");
    let arrowRight = $(".arrowRight");
    let arrowLeft = $(".arrowLeft");
    let information = $(".information");
    let url=window.location.protocol+'//'+window.location.host;

    $(btnMenu).click(function () {
        if ($(menu).hasClass('right-[100%]')) {
            $(menu).removeClass('right-[100%]')
            $(menu).removeClass('invisible')
            $(menu).addClass('right-0')
            $(btnMenu).attr('src', url+'/merikhArz/src/images/close.svg')

        } else {
            $(menu).addClass('right-[100%]')
            $(menu).removeClass('right-0')
            $(menu).addClass('invisible')
            $(btnMenu).attr('src', url+'/merikhArz/src/images/hamburger.svg')


        }

        if (!$(profile).hasClass('opacity-0')) {
            $(profile).addClass('opacity-0');
            $(profile).addClass('translate-y-3');
            $(profile).removeClass('z-10');
            $(profile).removeClass('z-10');

        }

    })

    $(".userIcon").click(function () {

        if (!$(menu).hasClass('right-[100%]')) {
            $(menu).addClass('right-[100%]')
            $(menu).addClass('invisible')
            $(menu).removeClass('right-0')
            $(btnMenu).attr('src', url+'/merikhArz/src/images/hamburger.svg')
        }

        if ($(profile).hasClass('opacity-0')) {
            $(profile).removeClass('opacity-0');
            $(profile).removeClass('translate-y-3');
            $(profile).removeClass('-z-10');
            $(profile).addClass('z-10');
        } else {
            $(profile).addClass('opacity-0');
            $(profile).addClass('translate-y-3');
            $(profile).addClass('-z-10');
            $(profile).removeClass('z-10');
        }
    })
    var content = $(notice_desc).text();
    var contentSubstr = '';
    content = $.trim(content);

    function decreaseText() {
        content = $(notice_desc).text();
        contentSubstr = '';
        content = $.trim(content);
        if (content.length > 93) {
            contentSubstr = content.substring(0, 93);
            $(notice_desc).text(contentSubstr)
            $(notice).html('ادامه مطلب...')

        }


    }

    decreaseText()
    let status = false;
    $(notice).click(function () {
        if (content.length > 93) {
            $(notice_desc).text(content)
            content = '';
            $(notice).html('خلاصه کردن متن')
        } else {
            decreaseText()
        }


    })

    $(arrowRight).click(function () {
        let counter = 0;
        let li = $(sliderLi).children('li');
        let totalSlider = $(li).length - 1;
        let active = $(sliderLi).children('li.active');
        let liActiveIndex = $(li).index(active);
        $(active).removeClass('active');
        $(active).addClass('translate-x-full');
        if (liActiveIndex < totalSlider) {
            counter = liActiveIndex + 1
        }
        let activeli = li[counter]
        $(activeli).addClass('active')
        $(activeli).removeClass('translate-x-full');
        $(active).addClass('translate-x-full');

    });

    $(arrowLeft).click(function () {
        let counter = 0;
        let li = $(sliderLi).children('li');
        let totalSlider = $(li).length - 1;
        let active = $(sliderLi).children('li.active');
        let liActiveIndex = $(li).index(active);
        console.log(liActiveIndex)
        $(active).removeClass('active');
        $(active).addClass('translate-x-full');
        if (liActiveIndex > 0) {
            counter = liActiveIndex -1
        }
        else {
            counter=totalSlider;
        }
        let activeli = li[counter]
        $(activeli).addClass('active')
        $(activeli).removeClass('translate-x-full');
        $(active).addClass('translate-x-full');

    });

        $(".btn-information").click(function (){
            if ($(information).hasClass('hidden'))
            {
                $(information).addClass('flex');
                $(information).removeClass('hidden')

                $(this).children('p').text('نمایش خلاصه')
                $('.btn-information').appendTo(information)

            }
            else {
                $(information).removeClass('flex');
                $(information).addClass('hidden');
                $(this).children('p').text('جزئیات بیشتر')
                $('.btn-information').appendTo($('.orderParent'))
            }
        })
})
