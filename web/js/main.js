

  var favSyr = { 
    popup: 
      { 
        options: { 
          overlay: { 
            css: { 
              backgroundColor: '#041100', opacity: 0.65 
            } 
          },
          afterOpen: function(data, el) {
              $('body').css('position' , 'fixed');
          },
          afterClose: function(data, el) {
              $('body').css('position' , 'static');              
          } 
        } 
      } 
    }
$(function () {
  $('input[type="file"]').change(function () {
    var text = $(this).val();
    $(this).next('.form__load-file-title').text(text.replace("C:\\fakepath\\" , ''));
  })
  $('#mask').mask('+7(999)999-99-99');
  $('.form__input_type_phone').mask('+7(999)999-99-99');
  $('.form__input_type_date').mask('99.99.9999');
  if ($("#jsScrollContent").length) {
    var scrollBlockHeight = $('body').height();
    if ($("#jsScrollContent").height() > scrollBlockHeight - 200) {
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
      $("#jsScrollContent").mCustomScrollbar({ setHeight: scrollBlockHeight - 200 });
    }
  }
  $('#menuButton').on('click', function () {
    $(this).toggleClass('mobile-menu-btn_active_close');
    $('#mainMenu').fadeToggle(0).toggleClass('main-menu_open');
    return false;
  });
  $('.existPopup').on('click', function () {
    $('#exist').arcticmodal(favSyr.popup.options);
    return false;
  });
  $('.profile__link-edit').on('click', function () {
    $('#showEditPP').arcticmodal(favSyr.popup.options);
    return false;
  });
  $('.regFale').on('click', function () {
    $.arcticmodal('close');
    $('#checkRegistrFale').arcticmodal(favSyr.popup.options);
    return false;
  });
  $('.password-reminder').on('click', function () {
    $('#restoreModal').arcticmodal(favSyr.popup.options);
    return false;
  });


  $('.jsOpenContent').click(function () {
    $('.jsContent').not($(this).next('.jsContent')).slideUp();
    $(this).next('.jsContent').slideToggle();
  });
  resizeElems();
});
$(window).resize(function () {
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
  var currentCleanHeight = currentHeight - $('#header').height() - $('#footer').height();
  var differenceWidth = currentWidth - initialWidth;
  var differenceHeight = currentCleanHeight - initialHeight;
  var widthPercentage = differenceWidth / widthProcent;
  var heightPercentage = differenceHeight / widthProcent;
  if (currentWidth < 800) return false;
  if (widthPercentage < heightPercentage) {
    html.css('fontSize', 10 + widthPercentage / 10);
  } else {
    html.css('fontSize', 10 + heightPercentage / 9.12);
  }
}