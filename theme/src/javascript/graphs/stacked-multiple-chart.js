import moment from 'moment';
import Config from '../config/config';
import 'moment/locale/ar';

import { select } from 'd3-selection';
import { timeParse, timeFormat } from 'd3-time-format';
import { max, descending } from 'd3-array';
import { scaleOrdinal, scaleBand, scaleLinear } from 'd3-scale';
import { axisBottom, axisRight} from 'd3-axis';
import { nest } from 'd3-collection';
import { json } from 'd3-fetch';

import { attachTooltipEvents } from  '../functions';

// import * as request from 'd3-request' // d3 submodule (contains d3.csv, d3.json, etc)

// create a Object with only the subset of functions/submodules/plugins that we need
const d3 = Object.assign(
	{},
	{
		timeParse,
		timeFormat,
		scaleBand,
		scaleLinear,
		nest,
		axisBottom,
		select,
		scaleOrdinal,
		max,
		descending,
		axisRight,
		json,
	},
);

function makeLegend(data, element, colorScheme){
	const legendEl = element.querySelector('.legend-controls .legend');


	for(var i in data.legend){
		const group = data.legend[i];
		


		let groupElement = document.createElement('div');
		groupElement.classList.add('group');
		if(group.tooltip){
			groupElement.classList.add('has-tooltip');
		}
		if(group.is_group){
			groupElement.classList.add('multilateral');
		}
		groupElement.classList.add(i);

		// ${group.is_group ? `
		// 	<span class="multilateral-label">(multilateral)</span>
		// ` : ''}


		groupElement.innerHTML = `
			<div class="color" style="background-color: ${colorScheme[i]};"></div>
			<div class="label">${group.label}
				${group.tooltip ? `
					<i class="far fa-info-circle"></i>
					<div class="tooltip"><div class="tooltip-content">${group.tooltip}</div></div>
				` : ''}
				
			</div>
		`;
		legendEl.appendChild(groupElement);
	}
	attachTooltipEvents(legendEl);
}

function ordinal_suffix_of(i) {
    var j = i % 10,
        k = i % 100;
    if (j == 1 && k != 11) {
        return i + "st";
    }
    if (j == 2 && k != 12) {
        return i + "nd";
    }
    if (j == 3 && k != 13) {
        return i + "rd";
    }
    return i + "th";
}

const renderChart = function(data, element, chartId, lang, breakpoint){
	if (!data.timeline) {
		return;
	}
	
	if(lang === 'en'){
		moment.locale('en');
	}

	if(lang === 'ar'){
		moment.locale('ar');
		element.classList.add('arabic-chart');	
	}

	if(lang === 'he'){
		moment.locale('he-IL');
		element.classList.add('hebrew-chart');	
	}

	
	let colorScheme = Config.colorSchemes[chartId];
	let padding = 15;
	if(chartId === 'libya-strikes-timeline' || chartId === 'libya-civcas-belligerents-timeline'){
		padding = 1;
	}
	let dateType = 'month';
	
	var parseMonthDate = d3.timeParse('%Y-%m');
	var parseDayDate = d3.timeParse('%Y-%m-%d');
	var parseYearDate = d3.timeParse('%Y');

	var formatDay = d3.timeFormat('%d');
	var formatYear = d3.timeFormat('%y');
	var formatFullyear = d3.timeFormat('%Y');
	var formatMonth = d3.timeFormat('%b');

	let xAxisKey = 'date';
	if(chartId === 'strikes-per-president' || chartId === 'civcas-per-president' || chartId === 'declared-strikes-per-president-coalition-iraq-syria'){
		xAxisKey = 'presidency';
	}

	var formatYearDate = function(d) { 
		return moment(d).format('YYYY');
	};

	var formatMonthDate = function(d) { 
		return moment(d).format('MMM YY');
	};

	var formatDayDate = function(d) { 
		return ordinal_suffix_of(formatDay(d))+' ' + formatMonth(d) + ' ' + formatYear(d);
	};

	let container = element.querySelector('.chart');
	container.innerHTML = '<div class="chart-tooltip"></div>';
	element.appendChild(container);

	let baseHeight = 400;
	let barPadding = 0.3;
	if(window.innerWidth >= Config.breakpoints.xsm){
		baseHeight = 500;
		barPadding = 0.2;
	}

	var margin = {top: 80, right: 0, bottom: 50, left: 0},
		width = container.offsetWidth - margin.left - margin.right,
		height = baseHeight - margin.top - margin.bottom;

	var y0 = d3.scaleBand()
		.rangeRound([height, 0])
		.padding(0);	


	var y1 = d3.scaleLinear();

	var y2 = d3.scaleLinear().rangeRound([height, 0]);

	var x = d3.scaleBand()
		.rangeRound([10, width])
		.padding(barPadding);
		

	


	var nest = d3.nest().key(function(d) { return d.group; });
	var nestDate = d3.nest().key(function(d) { 		
		return d[xAxisKey];
		
	});



	data.timeline.forEach(function(d) {
		if(d.month !== undefined){

			d.date = parseMonthDate(d.month);	
			dateType = 'month';
		} else if (d.day !== undefined){
			dateType = 'day';
			d.date = parseDayDate(d.day);
		} else if (d.year !== undefined){
			dateType = 'year';
			d.date = parseYearDate(d.year);

		} else {

			dateType = 'none';
		}
		
		d.value = +d.value;			
	});


	var xAxis = d3.axisBottom()
		.scale(x)
		.tickFormat(function(date, b, c){

			let formattedDate;

			if(dateType === 'day'){
				formattedDate = moment(date).format('D MMM YYYY');
			} else if(dateType === 'month'){
				formattedDate = moment(date).format('MMM YYYY');
			} else if (dateType === 'year'){
				formattedDate = moment(date).format('YYYY');
			} else {
				formattedDate = data.legend_presidencies[date].label;
			}
			return formattedDate;
		});

	var xAxis2 = d3.axisBottom()
		.scale(x)
		.tickFormat(function(date, b, c){
			let start = moment(data.legend_presidencies[date].start).format('DD MMM YYYY');
			let end = moment(data.legend_presidencies[date].end).format('DD MMM YYYY');
			return start+' – '+end;
		});

	var dataByGroup = nest.entries(data.timeline);

	dataByGroup.reverse();

	var dataByDate = nestDate.entries(data.timeline);


	let dateRange = dataByGroup[0].values.map(function(d) { return d[xAxisKey]; });



	if(lang === 'ar' || lang === 'he'){
		dateRange.reverse();
	}

	x.domain(dateRange);

	let eventsContainer = document.createElement('div');
	eventsContainer.classList.add('events-container');
	container.appendChild(eventsContainer);





	

	if(data.events){
		let totalStart = moment(data.timeline[0].date);
		let totalEnd = moment(data.timeline[data.timeline.length-1].date);

		data.events.forEach(function(event, i){
			let eventStart = moment(event.start, 'YYYY-MM-DD');
			let eventEnd = moment(event.end, 'YYYY-MM-DD');	


			const startFormat = eventStart.format(Config.dateFormat);
			const endFormat = eventEnd.format(Config.dateFormat);	
			let eventBar = document.createElement('div');

			let startMonthDays = eventStart.date();
			let endMonthDays = eventEnd.date();
			let extraStart = (startMonthDays / 31) * x.bandwidth();

			let extraEnd = (endMonthDays / 31) * x.bandwidth();

			
			eventBar.classList.add('event-bar');
			
			if(eventStart.isAfter(totalEnd) && eventEnd.isAfter(totalEnd)){
				eventBar.classList.add('hidden');
			}

			if(eventEnd.isBefore(totalStart)){
				eventBar.classList.add('hidden');
			}

			if(eventStart.isBefore(totalStart)){
				eventStart = totalStart;
			}
			if(eventEnd.isAfter(totalEnd)){
				eventEnd = totalEnd;
			}
			// if(event.title === 'Battle of Derna'){
			// }


			let right = width - x(eventStart.startOf('month').toDate()) + Math.round(extraStart);
			let left = x(eventEnd.startOf('month').toDate()) + Math.round(extraEnd);


			if(lang === 'en'){
				right = width - x(eventEnd.startOf('month').toDate()) + Math.round(extraEnd);
				left = x(eventStart.startOf('month').toDate()) + Math.round(extraStart);
			}
					
			if((left + 260) > width){
				eventBar.classList.add('right');
			}


			eventBar.innerHTML = `
				<div style="left: ${left}px; right:${right}px">
					<div>
						<span class="title">${event.title}</span>
						<span class="date">${startFormat} – ${endFormat}</span>
					</div>
				</div>			
			`;
			eventsContainer.appendChild(eventBar);
		});
	}



	var svg = d3.select(container).append('svg')
		.attr('width', width + margin.left + margin.right)
		.attr('height', height + margin.top + margin.bottom)
		.append('g')
		.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');


	y0.domain(dataByGroup.map(function(d) { 		
		return d.key;
	}));


	if(chartId === 'libya-strikes-timeline' || chartId === 'libya-civcas-belligerents-timeline'){
		// var colorRange = d3.scaleOrdinal(d3.schemeSpectral[11]);		
		// colorScheme = {};

		// y0.domain().forEach(function(group, i){
		// 	colorScheme[group] = colorRange(i);
		// });

		colorScheme = Config.colors.libya;

	}
	makeLegend(data, element, colorScheme);

	var months = {};

	dataByGroup.forEach(function(group){
		group.values.forEach(function(item){

			if(months[item[xAxisKey]] === undefined){
				months[item[xAxisKey]] = 0;
			}
			months[item[xAxisKey]] += item.value;
		});
	});
	var monthTotals = Object.keys(months).map(function (key) { return months[key]; });
	var max = d3.max(monthTotals);
	// if(chartId === 'militant-deaths-timeline'){
	// 	max = d3.max(data.timeline, function(d) { return d.value; });
	// }
	y1.domain([0, d3.max(data.timeline, function(d) { return d.value; })]).range([y0.bandwidth(), 0]);
	y2.domain([0, max]);
	
	var group = svg.selectAll('.group')
		.data(dataByGroup)
		.enter().append('g')
		.attr('class', 'group')
		.attr('transform', 'translate(0,0)');

		
	function scaleBandInvert(scale) {
		var domain = scale.domain();
		var paddingOuter = scale(domain[0]);
		var eachBand = scale.step();
		return function (value) {
			var index = Math.floor(((value - paddingOuter) / eachBand));
			return [index, domain[Math.max(0,Math.min(index, domain.length-1))]];
		};
	}

	const tooltip = container.querySelector('.chart-tooltip');
	let lastIndex = 0;
	let numChildren = x.domain().length;
	container.querySelector('svg').addEventListener('mousemove', function(e){
		var invert = scaleBandInvert(x)(e.offsetX);
		const index = invert[0];
		const date = invert[1];
		let xPosition = x(date) + (x.bandwidth() / 2);

		let dayData = dataByDate.filter(function(incident) { 

			return incident.key == date;
		})[0];


		tooltip.style.left = xPosition + 'px';
		tooltip.style.display = 'block';


		let formattedDate = moment(date).format('MMMM YYYY');

		if(dateType === 'day'){
			formattedDate = moment(date).format('Do MMM YYYY');

		} else if (dateType === 'year'){
			formattedDate = moment(date).format('YYYY');
		}

		let totalValue = 0;
		let totalLabel;
		if(dataByGroup.length === 1){
			tooltip.classList.add('bar-chart');
		}
		
		let tooltipTitle = formattedDate;

		if(chartId === 'civcas-grading-timeline'){

			let totalIncidentsMin = 0;
			let totalIncidentsMax = 0;

			dayData.values.forEach(function(incident){
				totalIncidentsMax += incident.max;
				totalIncidentsMin += incident.min;
			});

			totalLabel = 'Alleged Deaths';
			totalValue = totalIncidentsMin + ' – ' + totalIncidentsMax;
		} else if(chartId === 'coalition-isr-missions'){
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			totalLabel = 'Total Missions';

		} else if(chartId === 'civcas-belligerents-timeline'){
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			totalLabel = 'Alleged Deaths';
		} else if(chartId == 'militant-deaths-timeline'){
			let totalIncidentsMin = 0;
			let totalIncidentsMax = 0;
			dayData.values.forEach(function(incident){
				if(incident.group === 'alleged_strike'){
					
					totalIncidentsMax += incident.max;
				} else {
					totalIncidentsMin += incident.min;
					totalIncidentsMax += incident.max;
				}
			});
			totalLabel = 'Militant Deaths';
			
			totalValue = [totalIncidentsMin, totalIncidentsMax].join(' – ');

		} else if(chartId === 'libya-strikes-timeline'){
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			totalLabel = 'Total Strikes';

		} else if(chartId === 'libya-civcas-belligerents-timeline'){
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			totalLabel = 'Alleged Deaths (min)';			

		} else if(chartId === 'civcas-per-president'){

			tooltipTitle = data.legend_presidencies[dayData.key].label;
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});

		} else if (chartId === 'strikes-per-president' || chartId === 'declared-strikes-per-president-coalition-iraq-syria'){
			totalLabel = 'Total Strikes';
			tooltipTitle = data.legend_presidencies[dayData.key].label;
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});

		} else {
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			totalLabel = 'Total Events';
		}

		let lineHeight = (baseHeight - 50) + container.querySelector('.events-container').offsetHeight;

		if(data.unit){
			totalLabel = data.unit;	
		}
		let dayDataArray = [];
		for(let i in dayData.values){
			dayDataArray.push(dayData.values[i]);
		}

		
		dayDataArray.sort(function(x, y){
			return d3.descending(x.value, y.value);
		})
		tooltip.innerHTML = `
			<div>
				<div class="line"></div>
				<div class="inner-tooltip">
					<div class="date">${tooltipTitle}</div>
					<div class="total">
						<div class="grade">${totalLabel}</div>
						<div class="value">${totalValue}</div>
					</div>
					<div class="breakdown">
						${dayDataArray.map((item, i) => `
							${item.value > 0 ? `
								<div>
									<div class="grade ${item.group}">
										<div class="color" style="background-color: ${colorScheme[item.group]}"></div>
										${data.legend[item.group].label}
									</div>
									<div class="value">
										${item.min !== item.max ? `${item.min} – ${item.max}` : `${item.value}`}								
									</div>
								</div>
							` : ''}			
							
						`).join('')}
					</div>
				</div>
			</div>
		`;

		let elementIndex = index+1;
		if(lang === 'ar' || lang === 'he'){
			elementIndex = numChildren - elementIndex + 1;
		}
		svg.selectAll('svg g.group rect').classed('highlighted', false);
		svg.selectAll('svg g.group rect:nth-child('+elementIndex+')').classed('highlighted', true);


		// if(lastIndex !== index){			
		// 	svg.selectAll('svg g.group rect:nth-child('+(lastIndex+1)+')').classed('highlighted', false);
		// 	lastIndex = index;
		// }
		
	});

	container.querySelector('svg').addEventListener('mouseleave', function(){
		tooltip.style.display = 'none';
		svg.selectAll('rect').classed('highlighted', false);
	});


	group.selectAll('rect')
		.data(function(d) {		
			return d.values;
		})
		.enter().append('rect')
		.style('fill', function(d, i) {		
			return colorScheme[d.group];
		})
		.style('stroke', function(d, i) {
			return colorScheme[d.group];	
		})
		.attr('x', function(d) {
			return x(d[xAxisKey]);
		})
		.attr('class', function(d){;
			return d.group;
		})
		.attr('y', function(d, i) { 
			// if(chartId === 'militant-deaths-timeline'){
			// 	if(d.group === 'militants_killed_min'){
			// 		return height - (y2.range()[0]-y2(d.value));
			// 	}
			// }

			var dataSet = dataByGroup.map((dataItem) => { 
				return dataItem.values[i].value;
			});
			var transition = dataSet.splice(0, y0.domain().indexOf(d.group)+1).reduce((store, value) => {
				return store + value;				
			}, 0);
			return y2(transition);
		})

		.attr('width', x.bandwidth())
		.attr('height', function(d) {
			let height = y2.range()[0]-y2(d.value);
			if(d.value !== 0 && height === 0){
				height = 1;
			}
			return height;
		});

	svg.append('g')
		.attr('class', 'x axis')
		.attr('transform', 'translate(0,' + height + ')')
		.call(xAxis)
		.selectAll('text')
		.attr('y', function(){
			if(chartId === 'civcas-per-president' || chartId === 'strikes-per-president' || chartId === 'declared-strikes-per-president-coalition-iraq-syria'){
				if(breakpoint === 'none'){
					return 12;
				}
				return 15;
			}

			return 5;
		})
		.attr('x', 5)		
		.attr('transform', function(){
			if(chartId === 'civcas-per-president' || chartId === 'strikes-per-president' || chartId === 'declared-strikes-per-president-coalition-iraq-syria'){
				return '';
			}
			return 'rotate(45)';
		});

	if(chartId === 'civcas-per-president' || chartId === 'strikes-per-president' || chartId === 'declared-strikes-per-president-coalition-iraq-syria'){
		svg.append('g')
			.attr('class', 'x axis')
			.attr('transform', 'translate(0,' + height + ')')
			.call(xAxis2)
			.selectAll('text')
			.attr('y', function(){
				if(breakpoint === 'none'){
					return 22;
				} else if(breakpoint === 'xsm' || breakpoint === 'sm'){
					return 30;		
				} else {
					return 35;	
				}
			})
			.attr('x', 5);
	}

	let numTicks = 5;

	if(chartId === 'libya-civcas-belligerents-timeline' || chartId === 'libya-strikes-timeline'){
		numTicks = 1;
	}
	if(element.getAttribute('data-query') === 'lang=en&belligerent=palestinian-militants&country=israel&conflict=77037' || element.getAttribute('data-query') === 'lang=ar&belligerent=palestinian-militants&country=israel&conflict=77086' || element.getAttribute('data-query') === 'lang=he&belligerent=palestinian-militants&country=israel&conflict=80428'){
		numTicks = 3;
	}


	dataByGroup.forEach(function(item, i){
		let inverseIndex = ((dataByGroup.length-1) - i);
		let yTransform = (y0.bandwidth() * i) - (padding * inverseIndex);
		
		let multpleYAxisElements = svg.append('g')
			.attr('id', 'axis-'+i)
			.attr('class', 'y axis multiple')
			.attr('opacity', 0)
			.attr('transform', 'translate(0,' + yTransform + ')')
			.call(d3.axisRight(y1).ticks(numTicks).tickSize(width));
		multpleYAxisElements.selectAll('.tick text').attr('x', 0).attr('dy', -4);
	});

	var yAxis = d3.axisRight(y2).tickSize(width).tickFormat(x => Math.round(x));

	if(element.getAttribute('data-query') === 'lang=en&belligerent=palestinian-militants&country=israel&conflict=77037'){
		yAxis.ticks(max);
	}


	let yAxisElement = svg.append('g')
		.attr('class', 'y axis stacked')
		.call(yAxis);

	yAxisElement.selectAll('.tick text').attr('x', 0).attr('dy', -4);
	

	container.parentNode.querySelectorAll('input').forEach(function(el){
		el.addEventListener('change', change);
	});

	if(dataByGroup.length === 1){
		element.querySelector('.controls').style.display = 'none';
	}

	function change(){
		if (this.value === 'multiples') {
			this.parentNode.parentNode.classList.add('multiples');
			transitionMultiples();
		} else {
			this.parentNode.parentNode.classList.remove('multiples');
			transitionStacked();
		}
	}


	function transitionMultiples() {
		svg.selectAll('.axis.stacked.y').attr('opacity', '0');
		svg.selectAll('.axis.multiple').attr('opacity', '1');

		var t = svg.transition().duration(200);
		
		var	g = t.selectAll('.group').attr('transform', function(d, i) { 
			let groupPadding = y0(d.key) - (i*padding);
			return 'translate(0,' + groupPadding + ')';
		});

		let offset = 0;
		if(chartId === 'libya-civcas-belligerents-timeline' || chartId === 'libya-strikes-timeline'){
			offset = 5;
		}

		g.selectAll('rect').attr('y', function(d) { 
			return y1(d.value) - offset;
		});
		g.selectAll('rect').attr('height', function(d){
			return y0.bandwidth() - y1(d.value); 
		});
	}


	function transitionStacked(country) {
		var t = svg.transition().duration(200);			
		svg.selectAll('.axis.stacked.y').attr('opacity', '1');
		svg.selectAll('.axis.multiple').attr('opacity', '0');
		var	g = t.selectAll('.group').attr('transform', 'translate(0,0)');			
		g.selectAll('rect').attr('y', function(d,i) {
			var dataSet = dataByGroup.map((dataItem) => { 
				return dataItem.values[i].value;
			});
			var transition = dataSet.splice(0, y0.domain().indexOf(d.group)+1).reduce((store, value) => {
				return store + value;				
			}, 0);
			return y2(transition);
		}).attr('height', function(d){
			let height = y2.range()[0]-y2(d.value);
			if(d.value !== 0 && height === 0){
				height = 1;
			}
			return height;
		});
	}
};

const createStatckedMultipleChart = function(element, url, chartId, lang, breakpoint){

	d3.json(url).then(function(data){
		renderChart(data, element, chartId, lang, breakpoint);
	});
};






// function titleCase(str) {
// 	str = str.toLowerCase().split(' ');
// 	for (var i = 0; i < str.length; i++) {
// 		str[i] = str[i].charAt(0).toUpperCase() + str[i].slice(1); 
// 	}
// 	return str.join(' ');
// }


export default createStatckedMultipleChart;