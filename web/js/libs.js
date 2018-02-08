function Ru(obj) {
  if (obj.value.search(/[^а-яё\-]/ig) >= 0) {
    showMessage("Только кирилица");
  }
  obj.value = obj.value.replace(/[^а-яё\-]/ig, '');
}

function Pass(obj) {
  if (obj.value.search(/[^a-zA-Z1-9]/ig) >= 0) {
    showMessage("Только латиница и числа");
  }
  obj.value = obj.value.replace(/[^a-zA-Z1-9]/ig, '');

  if (obj.value.length > 15) {
    showMessage("Максимум 15 символов");
    obj.value = obj.value.substr(0, 15);
  }
}

function _calculateAge(birthday) { // birthday is a date
  console.log(birthday);
  var ageDifMs = Date.now() - birthday.getTime();
  var ageDate = new Date(ageDifMs); // miliseconds from epoch
  return Math.abs(ageDate.getUTCFullYear() - 1970);
}
