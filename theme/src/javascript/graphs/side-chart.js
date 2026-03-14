import * as d3 from 'd3';
// import moment from 'moment';
// import Config from '../config/config';

function makeAxis(){
	const axis = document.createElement('div');
	axis.classList.add('percentage-axis');
	for(let i = 0; i <= 100; i+=10){
		let unit = '';
		if(i === 0 || i === 100){
			unit = '%';
		}
		let tick = document.createElement('div');
		tick.classList.add('tick');
		tick.innerHTML = `<span>${i}${unit}</span>`;
		axis.appendChild(tick);
	}
	return axis;
}

function renderSideChart(data, element, chartId){
	let percentageChartEl = document.createElement('div');
	percentageChartEl.classList.add('percentage-chart');
	var color = d3.scaleOrdinal(d3.schemeSpectral[9]);
	data.forEach(function(bar, i){
		const chartElement = document.createElement('div');
		let explain = document.createElement('p');
		if(i === 0){
			explain.innerText = 'The proportion of US to allied Coalition strikes in Iraq to June 2017.';
		} else {
			explain.innerText = 'A breakdown of declared allied-only strikes, again to June 2017.';
		}
		chartElement.appendChild(makeAxis());

		const percentageBarEl = document.createElement('div');
		percentageBarEl.classList.add('percentage-bar');



		bar.belligerents.sort(function(x, y){
			return d3.descending(x.strikes, y.strikes);
		});

		bar.belligerents.forEach(function(belligerent, i){
			let amount = belligerent.strikes;
			let piece = document.createElement('div');
			
			let percentage = (amount / bar.total) * 100;
			piece.classList.add('piece');
			piece.style.width = percentage + '%';
			piece.style.backgroundColor = color(i);
			percentageBarEl.appendChild(piece);
			piece.innerHTML = `
				<div class="info">
					<span class="label">${belligerent.belligerent}</span><br/>
					<span class="value"><span class="amount">${amount}</span><br/><span class="percentage">(${Math.round(percentage)}%)</span></span>
				</div>
			`;
		});

		chartElement.appendChild(percentageBarEl);
		chartElement.appendChild(explain);

		if(i === 0){
			let zoom = document.createElement('div');
			zoom.classList.add('zoom');

		}
		chartElement.classList.add('chart-element');
		percentageChartEl.appendChild(chartElement);
	});

	element.querySelector('.chart').appendChild(percentageChartEl);

}

const createSideChart = function(element, url, chartId){
	d3.json(url).then(function(data){
		renderSideChart(data, element, chartId);
	});
};

export default createSideChart;