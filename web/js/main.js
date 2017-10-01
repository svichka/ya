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
				var heightВifference = $('body').height() - $('.form').height() - $('#header').height() - $('#footer').height() - 50;
				console.log(heightВifference)	
				if (heightВifference > 250) {
					scrollBlockHeight = heightВifference + 200;
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
		console.log(enteredText);
		$('.table__name').each(function(){
			$(this).closest('.table__item').css('display', 'none');
			if (~$(this).text().toLowerCase().indexOf(enteredText)) {
				$(this).closest('.table__item').css('display', 'block');
			}
		});
	});
	$('#menuButton').on('click', function(){
		$(this).toggleClass('mobile-menu-btn_active_close');
		$('#mainMenu').fadeToggle(0).toggleClass('main-menu_open');
		return false;
	});
	$('.profile__link-edit').on('click', function(){
		$('#editProfile').arcticmodal(favSyr.popup.options);
		return false;
	});
	$('.button_regcheck').on('click', function(){
		$('#checkRegistr').arcticmodal(favSyr.popup.options);
		return false;
	});
	$('.password-reminder').on('click', function(){
		$('#passwordReminder').arcticmodal(favSyr.popup.options);
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
});
$( window ).resize(function() {
	resizeElems();
});

function resizeElems() {
	if (!$('#resizeBlock').length) return false;
	var windowHeight = $(window).height();
	var contentHeight = $('#header').height() + $('#main').height() + $('#footer').height();
	var mainContentHeight = $('#main').children().height();
	console.log(windowHeight);
	console.log(contentHeight);
	if (windowHeight < contentHeight) {
		$('html').css('font-size', parseInt($('html').css('font-size')) - 0.5);
		resizeElems();
	}
}