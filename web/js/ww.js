$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $("[data-toggle=popover]").popover({ html: true });
});

function toDate(dateStr) {
  var parts = dateStr.split(".");
  return new Date(parts[ 2 ], parts[ 1 ] - 1, parts[ 0 ]);
}

function Ru(obj) {
  obj.value = obj.value.replace(/[^а-яё\-]/ig, '');
}

function _calculateAge(birthday) { // birthday is a date
  console.log(birthday);
  var ageDifMs = Date.now() - birthday.getTime();
  var ageDate = new Date(ageDifMs); // miliseconds from epoch
  return Math.abs(ageDate.getUTCFullYear() - 1970);
}