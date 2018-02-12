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
            lang: 'ru'
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
    $('.js-mobile-toggler').click(function() {
        $('.js-mobile-menu').toggleClass('visible-menu');
    }) 

    $('.faq-title').click(function(){
        $('.faq-body').hide();
        $(this).closest('.faq-item').find('.faq-body').show();
    })

    if ($.fn.scrollbar) {

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

    $('.js-get-code').click(function(){
        $(this).find('.js-code').toggle();
        return false;
    })

})