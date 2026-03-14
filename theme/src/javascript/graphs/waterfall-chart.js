import * as d3 from 'd3';
// import moment from 'moment';
import Config from '../config/config';





function renderWaterfallChart(data, element, chartId){

	const chartElement = document.createElement('div');
	chartElement.classList.add('chart-element', 'waterfall-chart');

	let chartHeight = 450;

	// data.entries.sort(function(x, y){
	// 	return d3.descending(x.value, y.value);
	// });



	const stripContainer = document.createElement('div');
	stripContainer.classList.add('strip-container');
	stripContainer.style.height = chartHeight + 'px';

	const fullStrip = document.createElement('div');
	fullStrip.classList.add('strip', 'full-strip');

	// const breakdownStrip = document.createElement('div');
	// breakdownStrip.classList.add('strip', 'breakdown-strip');

	let totalLabel = 'civilian deaths';

	if(chartId === 'libya-percentage-strikes-per-belligerent'){
		totalLabel = 'strikes';
	}

	const label = document.createElement('div');
	const labelInner = document.createElement('div');
	label.classList.add('label');
	labelInner.innerHTML = '<span>' + data.total + ' total '+totalLabel+'</span>';
	label.appendChild(labelInner);

	
	const breakdownLabel = document.createElement('div');
	breakdownLabel.classList.add('breakdown-label');
	breakdownLabel.innerHTML = 'Break&shy;down by Belli&shy;gerent&nbsp;&nbsp;▶';
	fullStrip.appendChild(breakdownLabel);
	fullStrip.appendChild(label);

	stripContainer.appendChild(fullStrip);
	//stripContainer.appendChild(breakdownStrip);

	let top = 0;

	let stripWidth = 100/(data.entries.length-1);
	

	fullStrip.style.flex = '0 1 '+stripWidth + '%';
	//breakdownStrip.style.width = stripWidth + '%';

	let strip = document.createElement('div');
	let totalHeight = 0;
	data.entries.forEach(function(entry, i){

		if(entry.is_group === false){
			strip = document.createElement('div');
		}

		strip.style.flex = '0 1 '+stripWidth + '%';
		strip.classList.add('strip');

		



		let amount = entry.value;
		let piece = document.createElement('div');
		
		let percentage = (amount / data.total) * 100;


		let pieceHeight = Math.max(1, Math.round(chartHeight * (amount / data.total)));

		if(i === data.entries.length - 1){
			pieceHeight = chartHeight - totalHeight - 2;
		} else {
			totalHeight += pieceHeight;
		}
 		
		//let multilateralLabel;

		if(entry.is_group){
			strip.classList.add('group');	
			//multilateralLabel = document.createElement('div');
			const labelInner = document.createElement('div');
			labelInner.innerHTML = '(multi&shy;lateral)';
			//multilateralLabel.classList.add('multilateral', 'label');
			//multilateralLabel.appendChild(labelInner);
			//piece.appendChild(multilateralLabel);
		}

		//let p = document.createElement('div');
		//p.classList.add('percentage', 'label');
		let percentageLabel;
		if(percentage < 1){
			percentageLabel = '<1%';
		} else {
			percentageLabel = percentage.toFixed(1)+'%';
		}
		
		//piece.appendChild(p);

		const label = document.createElement('div');
		const labelInner = document.createElement('div');
		label.classList.add('label');
		let valueLabel;
		
		if(entry.value === 1){
			if(chartId === 'libya-percentage-strikes-per-belligerent'){
				valueLabel = 'strike';
			} else {
				valueLabel = 'death';
			}
		} else {
			if(chartId === 'libya-percentage-strikes-per-belligerent'){
				valueLabel = 'strikes';
			} else {
				valueLabel = 'deaths';
			}			
		}
		labelInner.innerHTML = '<span><span class="arrow">▲</span> '+data.legend[entry.group].label + '</span><br/><span class="mono">' + entry.value + ' '+valueLabel+'</span><br/><span class="mono">'+percentageLabel+'</span>';
		label.appendChild(labelInner);

		piece.appendChild(label);
		piece.classList.add('piece');

		piece.classList.add(entry.group);
		piece.style.backgroundColor = Config.colors.libya[entry.group];
		piece.style.top = top + 'px';
		piece.style.height = pieceHeight + 'px';

		strip.appendChild(piece);
			


		top += pieceHeight;
		
		stripContainer.appendChild(strip);


	});



	chartElement.appendChild(stripContainer);
	element.querySelector('.chart').appendChild(chartElement);

}

const createWaterfallChart = function(element, url, chartId){
	d3.json(url).then(function(data){
		renderWaterfallChart(data, element, chartId);
	});
};

export default createWaterfallChart;		