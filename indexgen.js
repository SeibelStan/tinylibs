/* содержание */

var index = '';
var indexContainer = $('#page-index');
var hLevel = 'H0';

if($('article').find(':header').length > 1) {
	$('#page-index-wrap').fadeIn();
	$($('article').find(':header')).each(function() {
		var self = $(this);
		self.attr('id', self.html());
		if(self.get(0).nodeName > hLevel)
			index += '<ul>';
		if(self.get(0).nodeName < hLevel)
			index += '</ul>';
		index += '<li>' + '<a href="#' + self.html() + '">' + self.html() + '</a>';
		hLevel = self.get(0).nodeName;
	});

	indexContainer.prepend(index);
}
