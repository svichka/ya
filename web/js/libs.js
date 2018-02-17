function Ru(obj) {
  if (obj.value.search(/[^а-яё\-]/ig) >= 0) {
    $(obj).parent().find('.form__text-error').text("Только кирилица");
  }else {
    $(obj).parent().find('.form__text-error').text("");
  }
  obj.value = obj.value.replace(/[^а-яё\-]/ig, '');
}

function Pass(obj) {
  if (obj.value.search(/[^a-zA-Z1-9]/ig) >= 0) {
    $(obj).parent().find('.form__text-error').text("Только латиница и числа");
  }else {
    $(obj).parent().find('.form__text-error').text("");
  }
  obj.value = obj.value.replace(/[^a-zA-Z1-9]/ig, '');

  if (obj.value.length > 15) {
    $(obj).parent().find('.form__text-error').text("Максимум 15 символов");
    obj.value = obj.value.substr(0, 15);
  }
}

function _calculateAge(birthday) { // birthday is a date
  console.log(birthday);
  var ageDifMs = Date.now() - birthday.getTime();
  var ageDate = new Date(ageDifMs); // miliseconds from epoch
  return Math.abs(ageDate.getUTCFullYear() - 1970);
}
