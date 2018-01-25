function Ru(obj) {
  obj.value = obj.value.replace(/[^а-яё\-]/ig, '');
}

function Pass(obj) {
  obj.value = obj.value.replace(/[^a-zA-Z1-9]/ig, '');
}

function _calculateAge(birthday) { // birthday is a date
  console.log(birthday);
  var ageDifMs = Date.now() - birthday.getTime();
  var ageDate = new Date(ageDifMs); // miliseconds from epoch
  return Math.abs(ageDate.getUTCFullYear() - 1970);
}
