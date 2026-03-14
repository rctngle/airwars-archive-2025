export default function() {

	if (document.querySelector('article.report .langswitcher')) {

		window.addEventListener('hashchange', function() {
			const lang = window.location.hash.replace('#', '');
			
			document.querySelectorAll('.langswitcher a').forEach(navItem => {
				if (navItem.getAttribute('href').indexOf(lang) >= 0) {
					navItem.classList.add('active');
				} else {
					navItem.classList.remove('active');
				}
			});

			document.querySelectorAll('article.report').forEach(report => {

				report.querySelectorAll('.lang').forEach(langEl => {
					langEl.classList.add('hidden');
				});

				report.querySelectorAll('.lang.lang-'+lang).forEach(langEl => {
					langEl.classList.remove('hidden');
				});

				// report.querySelector('.lang').classList.add('hidden');
				// report.querySelector('.lang.'+lang).classList.remove('hidden');

			});
			
		});		

	}

	// document.querySelectorAll('article.report').forEach(report => {
			
	// });
}

