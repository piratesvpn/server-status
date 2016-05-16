
var Monitor = function(type, options) {

	var timeout = 2000;
	var timer;
	var endpoint = '/api.php';

	var empty = function(element) {
		while(element.firstChild) element.removeChild(element.firstChild);
	};

	var find = function(match) {
		return document.querySelector(match);
	};

	var request = function(url, callback) {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);
		xhr.callback = callback;

		xhr.onload = function() {
			if(xhr.status == 200) {
				xhr.callback(xhr.responseText);
			}
		};

		xhr.send();
	};

	var update = function() {
		var url = endpoint + '?type=' + type;
		request(url, render);
	};

	var render = function(response) {
		var data = JSON.parse(response),
			template = find(options.template),
			container = find(options.container),
			rendered;

		empty(container);

		if(Array.isArray(data)) {
			for(var i = 0; i < data.length; i++) {
				rendered = Mustache.render(template.innerHTML, data[i]);
				container.innerHTML += rendered;
			}
		}
		else {
			rendered = Mustache.render(template.innerHTML, data);
			container.innerHTML = rendered;
		}
	};

	var tick = function() {
		update();
		timer = setTimeout(function() { tick(); }, timeout);
	};

	tick();

};
