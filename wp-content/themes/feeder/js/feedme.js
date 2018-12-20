(function() {
	'use strict';
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
