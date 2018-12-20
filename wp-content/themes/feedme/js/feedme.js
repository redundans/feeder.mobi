(function() {
	'use strict';

	var section = document.querySelectorAll(".section");
	var sections = {};
	var i = 0;

	document.querySelector('#menu ul li:first-child a').setAttribute('class', 'current-menu-item');

	Array.prototype.forEach.call(section, function(e) {
		sections[e.id] = e.offsetTop;
	});

	window.onscroll = function() {
		var scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;

		for (i in sections) {
			if (sections[i] <= scrollPosition) {
				document.querySelector('.current-menu-item').setAttribute('class', ' ');
				document.querySelector('a[href*=' + i + ']').setAttribute('class', 'current-menu-item');
			}
		}
	};

})();

	function getTestMobi() {
		xhr = new XMLHttpRequest();
		xhr.open('POST', wp.ajax_url);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			if (xhr.status === 200) {
				let data = JSON.parse(xhr.responseText);
				if(data.error) {
					alert('Something went wrong!');
				} else {
					alert('The mobi has been mailed to your kindle!');
				}
			}
			else {
				console.log(xhr.status);
			}
		};
		xhr.send(encodeURI('action=run_test_schedule'));
	}
