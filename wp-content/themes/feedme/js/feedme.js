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
        console.log(i);
        document.querySelector('.current-menu-item').setAttribute('class', ' ');
        document.querySelector('a[href*=' + i + ']').setAttribute('class', 'current-menu-item');
      }
    }
  };
})();
