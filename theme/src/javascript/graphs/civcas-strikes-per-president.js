import moment from 'moment';
const renderChart = function(data, element, chartId, lang){
	if(lang === 'en'){
		moment.locale('en');
	}
	const container = document.createElement('div');
	container.classList.add('presidencies');

	data.presidencies.forEach(term=>{
		const termContainer = document.createElement('div');
		const start = moment(term.start).format('DD MMM YYYY');
		const end = moment(term.end).format('DD MMM YYYY');
		termContainer.innerHTML = `
			<h1>${term.label}</h1>
			<p>(${start} – ${end})</p>
			<div class="values">
				<div>
					<h1>${term.strikes}</h1>
					Strikes
				</div>
				<div>
					<h1>${term.civcas}</h1>
					Civilian Casualties
				</div>
			</div>
		`;
		container.appendChild(termContainer);
	});
	element.querySelector('.chart').appendChild(container);
};

const createCivcasStrikesPerPresident = function(element, url, chartId, lang){
	fetch(url, {
		method: 'get',
		credentials: 'include',
	}).then((response) => {
		return response.json();
	}).then(function(data) {
		renderChart(data, element, chartId, lang);
	});
};


export default createCivcasStrikesPerPresident;