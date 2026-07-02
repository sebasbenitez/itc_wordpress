
function setProcessingVisualState(elem)
{
	elem.css('position','relative').append('<div class="showajax"></div>');
	jQuery('.showajax').css({
		left:'15px'
	});
}
function setProcessingCompletedVisualState()
{
	jQuery('.showajax').remove();
}

var wcabehelper = {
	serializeCSV: function(valStr)
	{
		var arr = valStr.split(',');
		var ser = 'a:' + String(arr.length) + ':{';
		var i = 0;
		arr.forEach(function (item) {
			item = item.trim();
			ser += 'i:' + String(i) + ';s:' + String(item.length) + ':"' + item + '";';
			i++;
		});
		ser += '}';

		return ser;
	},
	unserializeCSV: function(valStr)
	{
		if (valStr === 'a:1:{i:0;s:0:"";}') {
			return '';
		}
		var myRegexp = /:"(?<id>\d+)";/g;
		var m;
		var result = '';

		do {
			m = myRegexp.exec(valStr);
			if (m) {
				result += result.length > 0 ? ',' + m[1] : m[1];
			}
		} while (m);
		return result.length ? result : valStr;
	},

	serializeCSV2: function(valStr)
	{
		var arr = valStr.split(',');
		var ser = 'a:' + String(arr.length) + ':{';
		var i = 0;
		arr.forEach(function (item) {
			item = item.trim();
			ser += 'i:' + String(i) + ';i:' + item + ';';
			i++;
		});
		ser += '}';

		return ser;
	},
	unserializeCSV2: function(valStr)
	{
		if (valStr === 'a:1:{i:0;}') {
			return '';
		}
		var myRegexp = /i:(?<id>\d+);/g;
		var m;
		var result = '';
		var even = false;
		do {
			m = myRegexp.exec(valStr);
			if (m && even) {
				result += result.length > 0 ? ',' + m[1] : m[1];
			}
			even = !even;
		} while (m);
		return result.length ? result : valStr;
	},
};
