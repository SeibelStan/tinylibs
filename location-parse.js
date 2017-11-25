location.getAttr = function (attr) {
	var regA = new RegExp('[?|&]' + attr + '=(.+?)(&|$)');
	var regB = new RegExp('[?|&]' + attr + '(&|$)');
	if(location.search.match(regA)) {
		var match = regA.exec(location.search);
		return match[1];
	}
	else if(location.search.match(regB)) {
		return true;
	}
	else {
		return false;
	}
}

location.setAttr = function (attr, val) {
	var regA = new RegExp('([?|&])' + attr + '=(.+?)(&|$)');
	var regB = new RegExp('([?|&])' + attr + '(&|$)');
	if(location.search.match(regA)) {
		location.href = location.href.replace(regA, '$1' + attr + '=' + val + '$3');
	}
	else if(location.search.match(regB)) {
		location.href = location.href.replace(regB, '$1' + attr + '=' + val + '$2');
	}
	else {
		if(location.search) {
			if(location.search.length > 1) {
				location.href += attr + '=' + val;
			}
			else {
				location.href += '&' + attr + '=' + val;
			}
		}
		else {
			location.href += '?' + attr + '=' + val;
		}
	}
}
