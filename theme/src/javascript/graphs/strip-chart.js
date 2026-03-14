import * as d3 from 'd3';
// import moment from 'moment';
import Config from '../config/config';

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


function shuffle(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}

function renderStripChart(data, element, chartId){
	
//	var color = d3.scaleOrdinal(d3.schemeSpectral[11]);
 	var colors = ['#2750ae','#466cb8','#6089bf','#7aa6c0','#96c4b9','#cfe146','#e3b551','#e88955','#e05d56','#cb3451','#a51f48'];
	const chartElement = document.createElement('div');
	chartElement.classList.add('chart-element', 'percentage-chart');
	chartElement.appendChild(makeAxis());

	const percentageBarEl = document.createElement('div');
	percentageBarEl.classList.add('percentage-bar');

	data.entries.sort(function(x, y){
		return d3.descending(x.value, y.value);
	});
	let overflowList = document.createElement('div');
	overflowList.classList.add('overflow-list');
	let offsetCounter = 0;

	data.entries.forEach(function(entry, i){
		let amount = entry.value;
		let piece = document.createElement('div');
		
		let percentage = (amount / data.total) * 100;
		let color = Config.colors.libya[entry.group];


		if(Array.isArray(color) === true){
			color = '#FFF';
			piece.classList.add('diagonal-stripe-4');
		} else if (entry.group === 'contested' || entry.group === 'unknown'){
			//piece.classList.add('lightstripe');
		}
		piece.classList.add('piece');
		piece.style.width = percentage + '%';
		piece.style.backgroundColor = color;


		percentageBarEl.appendChild(piece);
		const label = data.legend[entry.group].label;
		
		let overflow = false;

		if(percentage < 3){
			overflow = true;
			offsetCounter++;
			piece.classList.add('narrow');
			let overflowLabel = document.createElement('div');
			overflowLabel.innerHTML = amount + ' – ' + label;
			overflowList.appendChild(overflowLabel);
			overflowLabel.style.color = color;
		}
		

		piece.innerHTML = `
			<div class="info">
				<span class="label">${label}</span><br/>
				<span class="value"><span class="amount">${amount}</span><br/><span class="percentage">(${Math.round(percentage)}%)</span></span>
				${overflow ? `<div style="margin-top: ${offsetCounter * 14}px; border-color: ${color}" class="overflow"><div style="background-color: ${color}; height: ${offsetCounter * 14}px" class="line-up"></div></div>` : ``}
			</div>
		`;
	});

	chartElement.appendChild(percentageBarEl);
	chartElement.appendChild(overflowList);
	element.querySelector('.chart').appendChild(chartElement);

}

const createStripChart = function(element, url, chartId){
	d3.json(url).then(function(data){
		renderStripChart(data, element, chartId);
	});
};

export default createStripChart;	