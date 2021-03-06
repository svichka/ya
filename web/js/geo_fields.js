$(document).ready(function () {
	function htmlReplacer(oldElement, newElement)
	{
		if (newElement) {
			if (oldElement.is('select') && newElement.is('input[type="hidden"]')) {
				oldElement.parent().replaceWith(newElement);
			} else if (oldElement.is('input[type="hidden"]') && newElement.is('select')) {
				oldElement.replaceWith(newElement.parent());
			} else {
				oldElement.replaceWith(newElement);
			}
			return newElement;
		}
	}
	var $country = $('#' + formPrefix + '_countrycode'),
	$region = $('#' + formPrefix + '_regionguid'),
	$city = $('#' + formPrefix + '_cityguid'),
	$csrf = $('#' + formPrefix + '__token');
	$country.on('change', function () {
		var $form = $(this).closest('form');
		var data = {};
		data['ajax'] = 'Y';
		data[$csrf.attr('name')] = $csrf.val();
		data[$country.attr('name')] = $country.val();
		$.ajax({
			url : $form.attr('action'),
			type: $form.attr('method'),
			data : data,
			success: function(html) {
				var newRegion = $(html).find('#' + formPrefix + '_regionguid'),
				newCity = $(html).find('#' + formPrefix + '_cityguid');
				$region = htmlReplacer($region, newRegion);
				$city = htmlReplacer($city, newCity);
			}
		});
	});
	$region.on('change', function () {
		var $form = $(this).closest('form');
		var data = {};
		data['ajax'] = 'Y';
		data[$csrf.attr('name')] = $csrf.val();
		data[$country.attr('name')] = $country.val();
		data[$region.attr('name')] = $region.val();
		$.ajax({
			url : $form.attr('action'),
			type: $form.attr('method'),
			data : data,
			success: function(html) {
				var newCity = $(html).find('#' + formPrefix + '_cityguid');
				$city = htmlReplacer($city, newCity);
			}
		});
	});
});