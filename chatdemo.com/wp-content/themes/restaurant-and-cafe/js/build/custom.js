jQuery(document).ready(function ($) {

    /** Variables from Customizer for testimonial settings */
    var testimonial_control, slider_loop, rtl, animation;

    if (restaurant_and_cafe_data.pager == '1') {
        testimonial_control = true;
    } else {
        testimonial_control = false;
    }

    if (restaurant_and_cafe_data.loop == '1') {
        slider_loop = true;
    } else {
        slider_loop = false;
    }

    if (restaurant_and_cafe_data.rtl == '1') {
        rtl = true;
    } else {
        rtl = false;
    }

    if (restaurant_and_cafe_data.animation == 'slide') {
        animation = false;
    } else {
        animation = true;
    }
    $('#testimonial-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: testimonial_control,
        fade: animation,
        asNavFor: '#testimonial-slider-nav',
        autoplaySpeed: restaurant_and_cafe_data.speed, //ms'
        autoplay: restaurant_and_cafe_data.auto,
        infinite: slider_loop,
        rtl: rtl,
        draggable: false,
    });
    $('#testimonial-slider-nav').slick({
        slidesToShow: 7,
        slidesToScroll: 1,
        asNavFor: '#testimonial-slider',
        dots: false,
        arrows: false,
        focusOnSelect: true,
        draggable: false,
        responsive: [{
            breakpoint: 991,
            settings: {
                slidesToShow: 5,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 3,
            }
        }
        ],
    });


    // scrolling down
    $(".btn-scroll-down > button").on('click', function () {
        $('html, body').animate({
            scrollTop: $("#next_section").offset().top
        }, 1000);
    });

    $('.tabs button').on('click', function () {
        id = $(this).attr('id').split('-').pop();
        $('.tab-content').hide();
        $('#content-' + id).show();
        $('.tabs button').removeClass('active');
        $(this).addClass('active');
        //console.log(id); 
    });

    //mobile-menu
    var winWidth = $(window).width();

    // if (winWidth < 1025) {
    $('#menu-opener').on('click', function () {
        $('body').addClass('menu-open');
        $('.primary-menu-list').addClass('toggled');

        $('.btn-close-menu').on('click', function () {
            $('body').removeClass('menu-open');
            $('.primary-menu-list').removeClass('toggled');
        });
    });

    $('.overlay').on('click', function () {
        $('.primary-menu-list').removeClass('toggled');
        $('body').removeClass('menu-open');
    });

    // }
    //ul accessibility
    $('<button class="angle-down"></button>').insertAfter($('.mobile-navigation ul .menu-item-has-children > a'));
    $('.mobile-navigation ul li .angle-down').on('click', function () {
        $(this).next().slideToggle();
        $(this).toggleClass('active');
    });

    $('#menu-opener').on('click', function () {
        $('body').addClass('menu-open');
        $('.primary-menu-list').addClass('toggled');

        $('.btn-close-menu').on('click', function () {
            $('body').removeClass('menu-open');
            $('.primary-menu-list').removeClass('toggled');
        });
    });


    $(window).on("load, resize", function () {
        var viewportWidth = $(window).width();
        if (viewportWidth < 1025) {
            $('.overlay').on('click', function () {
                $('.primary-menu-list').removeClass('toggled');
                $('body').removeClass('menu-open');
            });
        }
        else if (viewportWidth > 1025) {
            $('body').removeClass('menu-open');
        }
    });

    if (winWidth > 1024) {
        $("#site-navigation ul li a").on('focus', function () {
            $(this).parents("li").addClass("focus");
        }).on('blur', function () {
            $(this).parents("li").removeClass("focus");
        });
    }

});
