$(function () {

  var products = {

    '1': {
      1: {'image': '1L_J7-ananas.png', 'name': 'Ананас', 'value': '0,97'},
      2: {'image': '1L_J7-granat.png', 'name': 'Гранат', 'value': '0,97'},
      3: {'image': '1L_J7-grapefr.png', 'name': 'Грейпфрут', 'value': '0,97'},
      4: {'image': '1L_J7-lime-lichi.png', 'name': 'Лайм-гуава', 'value': '0,97'},
      5: {'image': '1L_J7-persik.png', 'name': 'Персик', 'value': '0,97'},
      6: {'image': '1L_J7-mango-marakuya.png', 'name': 'Апельсин, маракуйа', 'value': '0,97'},
      7: {'image': '1L_J7-red-orange.png', 'name': 'Апельсин', 'value': '0,97'},
      8: {'image': '1L_J7-apple.png', 'name': 'Яблоко', 'value': '0,97/ 02'},
      9: {'image': '1L_J7-tomat.png', 'name': 'Томат', 'value': '	0,97/ 02'},
      10: {'image': '1L_J7-vishnya.png', 'name': 'Вишня', 'value': '0,97/ 02'},
      11: {'image': '1L_J7-multi.png', 'name': 'Мультифрукт', 'value': '0,97/ 02'},
      12: {'image': '1L_J7-orange.png', 'name': 'Апельсин', 'value': '0,97/ 02'},
      13: {'image': '1l-lemon.png', 'name': 'Лимон', 'value': '1 / 0,385'},
      14: {'image': '1l-orange.png', 'name': 'Апельсин', 'value': '1 / 0,385'},
      15: {'image': '09L_j7tonus-Orange_front.png', 'name': 'Апельсин', 'value': '1,45 / 0,9'},
      16: {'image': '09L_j7tonus-Orange-Banan_front.png', 'name': 'Апельсин, банан', 'value': '1,45 / 0,9'},
      17: {'image': '09L_j7tonus-Peach-Apple-Orange_front.png', 'name': 'Персик, апельсин', 'value': '1,45 / 0,9'},
      18: {'image': '09L_j7tonus-Grapefruit_Grape_front.png', 'name': 'Виноград, грейпфут', 'value': '1,45 / 0,9'},
      19: {'image': '09L_j7tonus-VegesMix-front.png', 'name': 'Овощной микс', 'value': '1,45 / 0,9'},
      20: {'image': '09L_j7tonus-Tomat-front.png', 'name': 'Томат', 'value': '1,45 / 0,9'},
      21: {'image': '09L_j7tonus-Citrus_mix_front.png', 'name': 'Цитрусовый микс', 'value': '1,45 / 0,9'},
      22: {'image': '09L_j7tonus-ApplePomegrante_front.png', 'name': 'Гранат, яблоко', 'value': '1,45 / 0,9'},
      23: {'image': 'Apple.png', 'name': 'Ягодный микс', 'value': '0,97'},
      24: {'image': 'Apple.png', 'name': 'Яблоко', 'value': '0,97'},
      25: {'image': 'Multifruit.png', 'name': 'Мультифрукт', 'value': '0,97'},
    },
    '2': {
      1: {'image': '1L_LS_volsheb-skazka_front.png', 'name': 'Волшебная сказка', 'value': '0,95'},
      2: {'image': '1L_LS_klubnika-front.png', 'name': 'Клубничное настроение', 'value': '0,95'},
      3: {'image': '1L_LS_granat_front.png', 'name': 'Гранатовый сезон', 'value': '1,93 / 0,95'},
      4: {'image': '1L_LS_vinograd_front.png', 'name': 'Виноград и яблоко', 'value': '1,93 / 0,95'},
      5: {'image': '1L_LS_grape-lemon-lime_front.png', 'name': 'Грейпфрут, лимон и лайм', 'value': '1,93 / 0,95'},
      6: {'image': '1L_LS_nektarin-front.png', 'name': 'Солнечный нектарин', 'value': '1,93 / 0,95 / 0,485 / 0,2'},
      7: {'image': '1L_LS_apple-abrikos-grusha_front.png', 'name': 'Абрикосовая груша', 'value': '1,93 / 0,95'},
      8: {'image': '1L_LS_orange-mango_front.png', 'name': 'Апельсиновое манго', 'value': '1,93 / 0,95 / 0,2'},
      9: {'image': '1L_LS_tomat_front.png', 'name': 'Спелый томат', 'value': '1,93 / 0,95'},
      10: {'image': '1L_LS_zemlyanichnoe-leto_front.png', 'name': 'Земляничное лето', 'value': '1,93 / 0,95 / 0,2'},
      11: {
        'image': '1L_LS_vishnya-chereshnya_front.png',
        'name': 'Вишневая черешня',
        'value': '1,93 / 0,95 / 0,2'
      },
      12: {'image': '1L_LS_multi_front.png', 'name': 'Мультифрукт', 'value': '1,93 / 0,95 / 0,485 / 0,2'},
      13: {'image': '1L_LS_apple_front.png', 'name': 'Яблоко', 'value': '1,93 / 0,95 / 0,485 / 0,2'},
      14: {'image': 'Apple3D.png', 'name': 'Яблоко', 'value': '0,3'},
      15: {'image': 'CherrySweetCherry3D.png', 'name': 'Вишневая черешня', 'value': '0,3'},
      16: {'image': 'Multifruit3D.png', 'name': 'Мультифрукт', 'value': '0,3'},
      17: {'image': 'WildStrawberrySummer3D.png', 'name': 'Земляничное лето', 'value': '0,3'}
    },
    '4': {
        1: {'image': '1l_YA_cherry-front.png', 'name': 'Вишня', 'value': '0,97 / 0,2'},
        2: {'image': '1L_YA_green_apple_front.png', 'name': 'Осветленное яблоко', 'value': '0,97 / 0,2'},
        3: {'image': '1L_YA_orange_front.png', 'name': 'Апельсин', 'value': '0,97 / 0,2'},
        4: {'image': '1l_YA_persik_front.png', 'name': 'Персик', 'value': '0,97 / 0,2'},
        5: {'image': '1l_YA_tomat_front.png', 'name': 'Томат', 'value': '0,97 / 0,2'},
        6: {'image': '1l_YA_grapef_front.png', 'name': 'Грейпфрут', 'value': '0,97'},
        7: {'image': '1l_YA_multi_front.png', 'name': 'Мультифрукт', 'value': '0,97'},
        8: {'image': '1l_YA_apple_front.png', 'name': 'Яблоко с мякотью', 'value': '0,97'},
        9: {'image': '1l_YA_ananas_front.png', 'name': 'Ананас', 'value': '0,97'},
        10: {'image': '1l_YA_mango_front.png', 'name': 'Манго', 'value': '0,97'},
        11: {'image': '1l_YA_vinograd_front.png', 'name': 'Виноград', 'value': '0,97'},
        12: {'image': '1l_YA_mandarin_front.png', 'name': 'Мандарин', 'value': '0,97'},
        13: {'image': '1l_YA_mexican_mix-front.png', 'name': 'Мексиканский микс', 'value': '0,97'}
    },
    '3': {
        1: {'image': '1L_FS_apple_front.png', 'name': 'Яблоко ', 'value': '1,93 / 0,95 / 0,485 / 0,3 / 0,2'},
        2: {'image': '1L_FS_multi_front.png', 'name': 'Мультифрукт', 'value': '1,93 / 0,95 / 0,485 / 0,3 / 0,2'},
        3: {'image': '1L_FS_orange_front.png', 'name': 'Апельсин', 'value': '1,93 / 0,95 / 0,485 / 0,3 / 0,2'},
        4: {'image': '1L_FS_peach_front.png','name': 'Персик-яблоко','value': '1,93 / 0,95 / 0,485 / 0,3 / 0,2'},
        5: {'image': '1L_FS_tomat_front.png', 'name': 'Томат', 'value': '1,93 / 0,95 / 0,485 / 0,2'},
        6: {'image': '1L_FS_vinograd_front.png', 'name': 'Виноград-яблоко', 'value': '1,93 / 0,95 / 0,2'},
        7: {'image': '1L_FS_vishnya_front.png', 'name': 'Яблоко-вишня-рябина', 'value': '1,93 / 0,95 / 0,2'},
        8: {'image': '1L_FS_yagodi_front.png', 'name': 'Ягоды-яблоко', 'value': '1,93 / 0,95 / 0,2'},
        9: {'image': '1L_Edge_FS_apple-ananans_F.png', 'name': 'Ананас-яблоко', 'value': '0,95'},
        10: {'image': '1L_FS_abrikos_front.png', 'name': 'Абрикос-яблоко', 'value': '0,95'},
        11: {'image': '1L_FS_red_apple_front.png', 'name': 'Яблоко с мякотью', 'value': '0,95 / 0,2'},
        12: {'image': '1L_FS-SY_Mors_klukva_F.png', 'name': 'Морс Клюква', 'value': '1,45 / 0,95 / 0,3'},
        13: {'image': '1L_FS-SY_Mors_yagod-sbor_F.png', 'name': 'Морс Ягодный сбор', 'value': '1,45 / 0,95'},
        14: {'image': '1L_FS-SY_Mors_ZiB_F.png', 'name': 'Морс Земляника-Брусника', 'value': '1,45 / 0,95'},
        15: {'image': '03L_FS_mors.png', 'name': 'Морс Клюква-Рябина', 'value': '0,3'},
        16: {'image': '1L_FS_kompot-visnya_F.png', 'name': 'Компот Вишневый', 'value': '1,93 / 0,95'},
        17: {'image': '1L_FS_kompot-sad-yagodi_F.png', 'name': 'Компот Садовые ягоды', 'value': '1,93 / 0,95'},
    }
  };

  function initOwl(type, products) {
    if ($.fn.owlCarousel) {
      $(".js-owl-block").trigger('destroy.owl.carousel');
      $('.slider-item').remove();

      var prod = products[type];
      $('.js-owl-block').empty();
      for (var i in prod) {
        $('.js-owl-block').append('<div class="slider-item"><div class="slider-item-img" style="background-image: url(../images/sku/' + type + '/' + prod[i]['image'] + ')"></div><div class="slider-item-title">' + prod[i]['name'] + '</div><div class="slider-item-post-title">' + prod[i]['value'] + '</div></div>');
      }
      $('.js-owl-block').owlCarousel({
        items: 6,
        dots: false,
        nav: true,
        navText: false,
        autoplay: true,
        responsive: {
          0: {
            items: 2
          }
          ,
          500: {
            items: 4
          },
          767: {
            items: 5
          },
          992: {
            items: 5
          },
          1200: {
            items: 6
          }

        }
      });
    }
  }

  if ($.fn.owlCarousel) {
    $('.js-header-slider').owlCarousel({
      items: 1,
      dots: true,
      autoplay: true
    })
  }

  if ($.fn.datetimepicker) {
    $('.js-datapicker').datetimepicker({
      timepicker: false,
      format: 'd.m.Y',
      lang: 'ru'
    });
  }
  $('#regModal').on('shown.bs.modal', function (e) {
    $('body').addClass('modal-open').css({'padding-right': '17px'});
    console.log('sdf');
  });
  $('#regModal').on('hidden.bs.modal', function (e) {
    $('body').css({'padding-right': '0'});
  });

  $('.js-open-week').click(function () {
    $(this).closest('.c-table-wrap').find('.js-hide').toggle();
    if ($(this).text() === '-') {
      $(this).text('+');
    }
    else{
      $(this).text('-');
      }
    $(this).toggleClass('opened-icon');
  });

  if ($.fn.mask) {
    $('input[type="tel"]').mask('+7(999) 999-99-99')
  }

  $('.js-file').on('change', function () {
    var name = $(this).val();
    if (name) {
      $(this).closest('.js-file-wrapper').find('span').text(name);
    }
  });

  $('.js-header').click(function () {
    $('.js-body').not($(this).closest('.js-wrap').find('.js-body')).slideUp();
    $(this).closest('.js-wrap').find('.js-body').slideToggle();
  });

  $('.js-mobile-toggler').click(function () {
    $('.js-mobile-menu').toggleClass('visible-menu');
  });

  initOwl('1', products);

  $('.js-product').click(function () {
    var type = $(this).data('type');
    $('.js-product').removeClass('active');
    $(this).addClass('active');
    initOwl(type, products);
    return false;

  })

});