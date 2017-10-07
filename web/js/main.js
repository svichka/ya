var favSyr = {
	popup : {
		options :   {
			overlay: {
				css: {backgroundColor: '#041100', opacity: 0.65}
			}
		}
	}
}
$(function(){
	if ($("#jsScrollContent").length) {
		var scrollBlockHeight = $('body').height();
		if ($("#jsScrollContent").height() > scrollBlockHeight-200) {
			if ($("#jsScrollContent").hasClass('table__content_winners') && $('body').height() > 650) {
				var heightBifference = $('body').height() - $('.form').height() - $('#header').height() - $('#footer').height() - 50;
				console.log(heightBifference)
				if (heightBifference > 250) {
					scrollBlockHeight = heightBifference + 200;
					console.log(scrollBlockHeight)
				}
			}
			if ($("#jsScrollContent").hasClass('faq__content')) {
				scrollBlockHeight = $('.main__left .form__wrapper').height() + 200;
			}
			$("#jsScrollContent").mCustomScrollbar({
				setHeight: scrollBlockHeight - 200
			});
		}
	}
	$('#fiosearch').on('input', function(){
		var enteredText = $(this).val().toLowerCase();
		$('.table_name').each(function(){
			$(this).closest('.table_item').css('display', 'none');
			if (~$(this).text().toLowerCase().indexOf(enteredText)) {
				$(this).closest('.table_item').css('display', 'block');
			}
		});
	});
	$('#menuButton').on('click', function(){
		$(this).toggleClass('mobile-menu-btn_active_close');
		$('#mainMenu').fadeToggle(0).toggleClass('main-menu_open');
		return false;
	});
	$('.existPopup').on('click', function(){
		$('#exist').arcticmodal(favSyr.popup.options);
		return false;
	});
	$('.profile__link-edit').on('click', function(){
		$('#updateModal').arcticmodal(favSyr.popup.options);
		return false;
	});
	$('.jsMoneyMobile').on('click', function(){
		$.arcticmodal('close');
		$('#mobileMoney').arcticmodal(favSyr.popup.options);
		return false;
	});

	$('.regFale').on('click', function(){
		$.arcticmodal('close');
		$('#checkRegistrFale').arcticmodal(favSyr.popup.options);
		return false;
	});
	$('.password-reminder').on('click', function(){
		$('#restoreModal').arcticmodal(favSyr.popup.options);
		return false;
	});

	$('.form__input_type_date').datetimepicker({
		lang: "ru",
		timepicker:false,
		format:'d.m.Y',
		maxDate:'+1970/01/01'
	});
	$('.form__input_type_phone').mask('+7(000)000-00-00');
	$('.jsOpenContent').click(function () {
		$('.jsContent').not($(this).next('.jsContent')).slideUp();
		$(this).next('.jsContent').slideToggle();
	});
	resizeElems();
});
$( window ).resize(function() {
	resizeElems();
});

function resizeElems() {
	var html = $('html');
	var initialWidth = 1440;
	var widthProcent = initialWidth / 100;
	var initialHeight = 900;
	var heightProcent = initialHeight / 100;
	var currentWidth = $(window).width();
	var currentHeight = $(window).height();
	if (currentHeight < 600) {
		currentHeight = 601;
	}
	var currentCleanHeight =  currentHeight - $('#header').height() - $('#footer').height();

	var differenceWidth =  currentWidth - initialWidth;
	var differenceHeight = currentCleanHeight - initialHeight;

	var widthPercentage = differenceWidth / widthProcent;
	var heightPercentage = differenceHeight / widthProcent;

	if (currentWidth < 800) return false;

	if (widthPercentage < heightPercentage) {
		html.css('fontSize', 10 + widthPercentage / 10);
	}
	else {
		html.css('fontSize', 10 + heightPercentage / 9.12);
	}

}