$(function() {


    if ($.fn.owlCarousel) {
        $('.js-header-slider').owlCarousel({
            items: 1,
            dots: true,
            autoplay: true
        })
    }
    $('#regModal').on('shown.bs.modal', function(e) {
        $('body').addClass('modal-open').css({
            'padding-right': '17px'
        });
    }) 
    $('#regModal').on('hidden.bs.modal', function(e) {
        $('body').css({
            'padding-right': '0'
        });
    }) 
    if ($.fn.datetimepicker) {
        $('.js-date').datetimepicker({
            timepicker: false,
            format: 'd.m.Y',
            lang: 'ru',
            closeOnDateSelect:true
        });
    }
    $('.js-week-title').click(function() {
        $(this).next('.js-toggle-table').toggle().find('.js-week-table-scroll').scrollbar("resize");;
        var toggler = $(this).find('.week-toggler');        
        if (toggler.text() == '-') toggler.text('+')
            else toggler.text('-');
    }) 
    if ($.fn.mask) {
        $('input[type="tel"]').mask('+7(999) 999-99-99')
    }
    $('.js-file-input').on('change', function() {
        var name = $(this).val();
        if (name) {
            $(this).closest('.js-file-wrapper').find('span').text(name);
        }
    }) 
    $('.js-header').click(function() {
        $('.js-body').not($(this).closest('.js-wrap').find('.js-body')).slideUp();
        $(this).closest('.js-wrap').find('.js-body').slideToggle();
    }) 
    $('.js-menu-toggler').click(function() {
        $('body').toggleClass('visible-menu');
    }) 

    $('.faq-title').click(function(){        
        var el  = $(this).closest('.faq-item').find('.faq-body');
        el.toggle();
        $('.faq-body').not(el).hide();
        $(".js-faq-wrapper").scrollbar("resize");
        $(".js-faq-wrapper").scrollbar("scroll", el);
    })



    if ($.fn.scrollbar) {


        $(window).on('resize' , function(){
            if ($(this).width() < 751){
                $(".js-faq-wrapper").scrollbar("destroy");
                $(".js-week-table-scroll").scrollbar("destroy");
            } else {
                $(".js-faq-wrapper").scrollbar("destroy");
                $(".js-week-table-scroll").scrollbar("destroy");
                $(".js-faq-wrapper").scrollbar({
                    handleSize : 25,
                    duration : 0.1
                });
                $('.js-week-table-scroll').each(function(){
                    $(this).scrollbar({
                        handleSize : 25,
                        duration : 0.1
                    });
                })
            }
        })

        if ($(window).width() > 751){
            $(".js-faq-wrapper").scrollbar({
                handleSize : 25,
                duration : 0.1
            });
            $('.js-week-table-scroll').each(function(){
                $(this).scrollbar({
                    handleSize : 25,
                    duration : 0.1
                });
            })
        }
    }

    $('.js-get-code').click(function(){
        $(this).find('.js-code').toggle();
        return false;
    })


    $('#City , #Street , #HouseNumber').blur(function () {
        if (!$(this).attr('data-selected')) {            
            $(this).val('');
        }
    }).keypress(function () {
        $(this).removeAttr('data-selected');
    })

    // // FIAS 
    // if ($.fn.suggestions) {
    //     var dadataToken = '0cd26ad806163ab4b224da78ce1b8d211f9f495d',
    //             dadataServiceUrl = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/';
    //     var byName = function(name) {
    //         return $('[name=' + name + ']');
    //     };
    //     var editForm = $('#Street').get(0) ? $($('#Street').get(0).form) : $();
    //     $('#City').suggestions({
    //         serviceUrl: dadataServiceUrl,
    //         token: dadataToken,
    //         type: 'ADDRESS',
    //         hint: false,
    //         bounds: 'city-settlement',
    //         constraints: {
    //             locations: { country: "Россия" },
    //         },
    //         onSelect: function (suggestion) {
    //             byName('FiasRegionId').val(suggestion.data.region_fias_id);
    //             byName('FiasCityId').val(suggestion.data.fias_id);
    //             byName('Region').val(suggestion.data.region);
    //             $('#RegionInput').attr('value', suggestion.data.region);
    //             //
    //             var city = suggestion.data.city ? suggestion.data.city : suggestion.data.settlement;
    //             $(this).val(city);
    //             $(this).attr('data-selected', city);
    //             reinitStreet();
    //         }
    //     });
    // }

})