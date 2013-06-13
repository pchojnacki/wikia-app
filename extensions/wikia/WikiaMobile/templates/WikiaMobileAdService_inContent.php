<script>window.addEventListener('load', function () {
	require(['ads', 'sloth'], function (ads, sloth) {

		function findPos(obj) {
			var curtop = 0;

			if (obj.offsetParent) {
				do {
					curtop += obj.offsetTop;
				} while (obj = obj.offsetParent)
				return curtop;
			}
		}

		var MIN_ZEROTH_SECTION_LENGTH = 1000,
			firstSection = document.getElementsByClassName('collSec')[0];

		if(firstSection && findPos(firstSection) > MIN_ZEROTH_SECTION_LENGTH){

			firstSection.insertAdjacentHTML('beforebegin', '<div id=wkAdInContent></div>');

			sloth({
				on: document.getElementById('wkAdInContent'),
				threshold: 500,
				callback: function(adWrapper){
					ads.setupSlot({
						name: 'MOBILE_TOP_LEADERBOARD',
						size: '300x250',
						wrapper: adWrapper,
						init: function(found){
							if(found){
								adWrapper.className = 'show';
							}
						}
					});
				}
			})
		}
	});
});</script>
