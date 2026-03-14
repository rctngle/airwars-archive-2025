import React, { useState, useEffect, useRef } from 'react';
import ConflictDataHeader from '../conflict-data/conflict-data-header'
import moment from 'moment'
import Config from '../config/config'



import { select } from 'd3-selection';
import { timeParse, timeFormat } from 'd3-time-format';
import { max, descending } from 'd3-array';
import { scaleOrdinal, scaleBand, scaleLinear, scaleTime } from 'd3-scale';
import { axisBottom, axisRight} from 'd3-axis';
import { nest } from 'd3-collection';


// import * as request from 'd3-request' // d3 submodule (contains d3.csv, d3.json, etc)

// create a Object with only the subset of functions/submodules/plugins that we need
const d3 = Object.assign(
	{},
	{
		timeParse,
		timeFormat,
		scaleBand,
		scaleLinear,
		scaleTime,
		nest,
		axisBottom,
		select,
		scaleOrdinal,
		max,
		descending,
		axisRight,
	}
)


export default function StackedMultipleGrpah(props) {

	const [chartType, setChartType] = useState('stacked')
	const [tooltipX, setTooltipX] = useState(0)

	let colorScheme = Config.colorSchemes[props.data.post_data.slug];
	
	if(colorScheme === undefined){
		colorScheme = Config.colorSchemes['civcas-grading-timeline']
	}

	const keys = Object.keys(props.data.legend)
	return (
		<div className="content">
			<div className="full">
				<div className="chart-container">
					<div className="chart-information">
						<ConflictDataHeader data={props.data.post_data} />
						<div className="legend-controls">
							<Legend colorScheme={colorScheme} legend={props.data.legend} uiTerms={props.data.ui_terms}/>
							{keys.length > 1 && <ChartTypeSelector chartType={chartType} uiTerms={props.data.ui_terms} onChange={val => setChartType(val)} />}
						</div>
					</div>

					<Chart colorScheme={colorScheme} breakpoint={props.breakpoint} data={props.data} chartType={chartType} onMouseMove={x => setTooltipX(x)} />
					
				</div>
			</div>
		</div>
	);
}

function Tooltip(props) {

	

	return <div className="chart-tooltip">TOOLTIP</div>
}

function Legend(props) {
	const legendEntries = []
	for (let key in props.legend) {
		
		const tooltip = props.legend[key].tooltip
		
		legendEntries.push(
			<div 
				key={`legend_item_${key}`} 
				className={`legend-item ${key}`}
			>
				<div className="color" style={{backgroundColor: props.colorScheme[key]}}></div>
				<div className="label">{props.legend[key].label}</div>
				{tooltip && <i className="far fa-info-circle" aria-hidden="true"></i>}
				{tooltip && <div className="tooltip"><div className="tooltip-content">{tooltip}</div></div>}
			</div>
		)
	}

	return (
		<div className="legend">
			<h4>{props.uiTerms.chart_legend}</h4>
			{legendEntries}
		</div>
	)
}

function ChartTypeSelector(props) {

	const { uiTerms } = props
	const options =[
		{ label: uiTerms.stacked, value: 'stacked', tooltip: uiTerms.best_for_comparing_total_totals_over_time },
		{ label: uiTerms.multiples, value: 'multiples', tooltip: uiTerms.best_for_comparing_an_individual_group_over_time },
	]


	return (
		<div className="controls">
			<h4>{props.uiTerms.view_this_chart_as}</h4>
			<div className="buttons">
				{options.map(option => {
					return (
						<div key={`key_`+option.value} className="control">
							<button
								key={`chart_type_option_${option.value}`} 
								className={option.value === props.chartType ? 'active' : undefined}
								style={{color: option.value === props.chartType ? '#fff' : '#000'}} 
								onClick={e => props.onChange(option.value)}
							>
								{option.label}
							</button>
						</div>
					)
				})}
			</div>

			<div className="annotation">
				<i className="far fa-info-circle"></i>
				<span dangerouslySetInnerHTML={{__html: options.find(option => option.value === props.chartType).tooltip}}></span>
			</div>
		</div>
	)
}

// function ordinal_suffix_of(i) {
// 	var j = i % 10,
// 		k = i % 100;
// 	if (j == 1 && k != 11) {
// 		return i + 'st';
// 	}
// 	if (j == 2 && k != 12) {
// 		return i + 'nd';
// 	}
// 	if (j == 3 && k != 13) {
// 		return i + 'rd';
// 	}
// 	return i + 'th';
// }

function Chart(props) {


	const [tooltipContent, setTooltipContent] = useState(null)
	const [eventsContent, setEventsContent] = useState(null)
	const element = useRef(null)
	const tooltipEl = useRef(null)
	const tooltipClasses = ['chart-tooltip']

	const data = props.data
	const chartId = props.data.post_data.slug
	const lang = props.data.post_data.lang
	const breakpoint = props.breakpoint

	const classNames = ['chart']
	


	useEffect(() => {

		if(lang === 'en'){
			moment.locale('en');
		}

		if(lang === 'ar'){
			moment.locale('ar');
			classNames.push('arabic-chart');	
		}

		if(lang === 'he'){
			moment.locale('he-IL');
			classNames.push('hebrew-chart');	
		}

		
		const colorScheme = props.colorScheme

		let padding = 15;
		if(chartId === 'libya-strikes-timeline' || chartId === 'libya-civcas-belligerents-timeline'){
			padding = 1;
		}
		let dateType = 'month';
		
		var parseMonthDate = d3.timeParse('%Y-%m');
		var parseDayDate = d3.timeParse('%Y-%m-%d');
		var parseYearDate = d3.timeParse('%Y');

		// var formatDay = d3.timeFormat('%d');
		// var formatYear = d3.timeFormat('%y');
		// var formatFullyear = d3.timeFormat('%Y');
		// var formatMonth = d3.timeFormat('%b');

		let xAxisKey = 'date';


		// var formatYearDate = function(d) { 
		// 	return moment(d).format('YYYY');
		// };

		// var formatMonthDate = function(d) { 
		// 	return moment(d).format('MMM YY');
		// };

		// var formatDayDate = function(d) { 
		// 	return ordinal_suffix_of(formatDay(d))+' ' + formatMonth(d) + ' ' + formatYear(d);
		// };

		// let container = element.querySelector('.chart');
		// container.innerHTML = '<div className="chart-tooltip"></div>';
		// element.appendChild(container);

		let baseHeight = 400;
		let barPadding = 0.3;
		if(window.innerWidth >= Config.breakpoints.xsm){
			baseHeight = 500;
			barPadding = 0.2;
		}

		var margin = {top: 80, right: 0, bottom: 50, left: 0},
			width = element.current.offsetWidth - margin.left - margin.right,
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


		data.graph.forEach(function(d) {
			if(data.key_type == 'month'){
				d.date = parseMonthDate(d.key);	
				dateType = 'month';
			} else if (data.key_type == 'day'){
				dateType = 'day';
				d.date = parseDayDate(d.key);
			} else if (data.key_type == 'year'){
				dateType = 'year';
				d.date = parseYearDate(d.key);
			} else {
				d.date = d.key;
				dateType = 'none';
			}
			
			d.value = +d.value;			
		});

		var xAxis = d3.axisBottom()
			.scale(x)
			.tickFormat(function(value, b, c){
				
				let axisLabel;

				if(dateType === 'day'){
					axisLabel = moment(value).format('D MMM YYYY');
				} else if(dateType === 'month'){
					axisLabel = moment(value).format('MMM YYYY');
				} else if (dateType === 'year'){
					axisLabel = moment(value).format('YYYY');
				} else {
					axisLabel = data.keys[value].label;
				}
				return axisLabel;

			});

		var xAxis2 = d3.axisBottom()
			.scale(x)
			.tickFormat(function(value, b, c){
				let start = moment(data.keys[value].label.start).format('DD MMM YYYY');
				let end = moment(data.keys[value].label.end).format('DD MMM YYYY');
				return start+' – '+end;
			});

		var dataByGroup = nest.entries(data.graph);
		dataByGroup.reverse();
		var dataByDate = nestDate.entries(data.graph);
		let dateRange = dataByGroup[0].values.map(function(d) { return d[xAxisKey]; });

		if(lang === 'ar' || lang === 'he'){
			dateRange.reverse();
		}

		var xEvents = d3.scaleTime().domain([dateRange[0], dateRange[dateRange.length-1]]).range([0, width]);

		
		x.domain(dateRange);
		
		if(data.events){

			let totalStart = moment(data.graph[0].date);
			let totalEnd = moment(data.graph[data.graph.length-1].date);
			const events = []
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


				let right = width - x(eventStart.toDate()) + Math.round(extraStart);
				let left = x(eventEnd.toDate()) + Math.round(extraEnd);


				if(lang === 'en'){
					right = width - xEvents(eventEnd.toDate()) + (x.bandwidth() / 2);
					left = xEvents(eventStart.toDate());
				}

				const eventBarClasses = ['event-bar']
				if((left + 260) > width){
					eventBarClasses.push('right');
				}

				events.push(
					<div className={eventBarClasses.join(' ')} key={`key_${event.title}`}>
						<div style={{left: left+'px', right: right+'px'}}>
							<div>
								<span className="title">{event.title}</span>
								<span className="date">{startFormat} – {endFormat}</span>
							</div>
						</div>
					</div>			
				
				)
			})
			setEventsContent(events)
		}

		const oldSvg = element.current.querySelector('svg')
			
		if(oldSvg){
			element.current.removeChild(oldSvg)	
		}

		var svg = d3.select(element.current).append('svg')
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
		//makeLegend(data, element, colorScheme);

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
		// 	max = d3.max(data.graph, function(d) { return d.value; });
		// }
		y1.domain([0, d3.max(data.graph, function(d) { return d.value; })]).range([y0.bandwidth(), 0]);
		y2.domain([0, max]);		

		var group = svg.selectAll('.group')
			.data(dataByGroup)
			.enter().append('g')
			.attr('class', 'group')

			.attr('transform', function(d, i){

				if(props.chartType === 'stacked'){
					return 'translate(0, 0)';
				} else {
					let groupPadding = y0(d.key) - (i*padding);
					return 'translate(0,' + groupPadding + ')';
				}
			});

			
		function scaleBandInvert(scale) {
			var domain = scale.domain();
			var paddingOuter = scale(domain[0]);
			var eachBand = scale.step();
			return function (value) {
				var index = Math.floor(((value - paddingOuter) / eachBand));
				return [index, domain[Math.max(0,Math.min(index, domain.length-1))]];
			};
		}

		
		//const tooltip = container.querySelector('.chart-tooltip');
		//let lastIndex = 0;
		//let numChildren = x.domain().length;
		
		element.current.addEventListener('mousemove', function(e){
			var invert = scaleBandInvert(x)(e.offsetX);
			//const index = invert[0];
			const date = invert[1];
			let xPosition = x(date) + (x.bandwidth() / 2);

			let dayData = dataByDate.filter(function(incident) { 

				return incident.key == date;
			})[0];


			tooltipEl.current.style.left = xPosition + 'px';
			tooltipEl.current.style.display = 'block';


			let formattedDate = moment(date).format('MMMM YYYY');

			if(dateType === 'day'){
				formattedDate = moment(date).format('Do MMM YYYY');

			} else if (dateType === 'year'){
				formattedDate = moment(date).format('YYYY');
			}

			let totalValue = 0;
			let totalLabel;
			if(dataByGroup.length === 1){
				tooltipClasses.push('bar-chart');
			}
			
			let tooltipTitle = formattedDate;
			dayData.values.forEach(function(incident){
				totalValue += incident.value;
			});
			
			if(dayData.values[0].hasOwnProperty('min') && dayData.values[0].hasOwnProperty('max')){
				let min = 0;
				let max = 0;

				dayData.values.forEach(function(incident){
					max += incident.max;
					min += incident.min;
				});

				totalLabel = 'Alleged Deaths';
				totalValue = min + ' – ' + max;
			}
			
			if(chartId.indexOf('president') !== -1){
				tooltipTitle = data.keys[dayData.key].label
			}


			


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

			setTooltipContent(
				<div>
					<div className="line"></div>
					<div className="inner-tooltip">
						<div className="date">{tooltipTitle}</div>
						<div className="total">
							<div className="grade">{totalLabel}</div>
							<div className="value">{totalValue}</div>
						</div>
						<div className="breakdown">
							{dayDataArray.map((item, i) => {
								if (item.value > 0) {
									return (
										<div key={`key_${i}`}>
											<div className={['grade', item.group].join(' ')}>
												<div className="color" style={{backgroundColor: colorScheme[item.group]}}></div>
												{data.legend[item.group].label}
											</div>
											<div className="value">
												{item.min !== item.max ? `${item.min} – ${item.max}` : `${item.value}`}								
											</div>
										</div>

									)
								}	
							})}
						</div>
					</div>
				</div>
			)

			// let elementIndex = index+1;
			// if(lang === 'ar' || lang === 'he'){
			// 	elementIndex = numChildren - elementIndex + 1;
			// }

			// svg.selectAll('svg g.group rect').classed('highlighted', false);
			// svg.selectAll('svg g.group rect:nth-child('+elementIndex+')').classed('highlighted', true);


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
			.attr('class', function(d){
				return d.group;
			})
			.attr('y', function(d, i) { 
				if(props.chartType === 'stacked'){
					var dataSet = dataByGroup.map((dataItem) => { 
						return dataItem.values[i].value;
					});
					var transition = dataSet.splice(0, y0.domain().indexOf(d.group)+1).reduce((store, value) => {
						return store + value;				
					}, 0);
					return y2(transition);
				} else {

					let offset = 0;
					if(chartId === 'libya-civcas-belligerents-timeline' || chartId === 'libya-strikes-timeline'){
						offset = 5;
					}
					return y1(d.value) - offset;
				}
				// if(chartId === 'militant-deaths-timeline'){
				// 	if(d.group === 'militants_killed_min'){
				// 		return height - (y2.range()[0]-y2(d.value));
				// 	}
				// }

				
			})

			.attr('width', x.bandwidth())
			.attr('height', function(d) {
				if(props.chartType === 'stacked'){
					let height = y2.range()[0]-y2(d.value);
					if(d.value !== 0 && height === 0){
						height = 1;
					}
					return height;
				} else {
					return y0.bandwidth() - y1(d.value); 
				}


			});

		svg.append('g')
			.attr('class', 'x axis')
			.attr('transform', 'translate(0,' + height + ')')
			.call(xAxis)
			.selectAll('text')
			.attr('y', function(){
				if(chartId.indexOf('president') !== -1){
					if(breakpoint === 'none'){
						return 12;
					}
					return 15;
				}

				return 5;
			})
			.attr('x', 5)		
			.attr('transform', function(){
				if(chartId.indexOf('president') !== -1){
					return '';
				}
				return 'rotate(45)';
			})
			.style('text-anchor', function(){
				if(chartId.indexOf('president') !== -1){
					return 'middle';
				}
			});

		if(chartId.indexOf('president') !== -1){
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
						return 30;	
					}
				})
				.attr('x', 5)
				.style('text-anchor', function(){
					if(chartId.indexOf('president') !== -1){
						return 'middle';
					}
				});
		}

		let numTicks = 5;

		if(chartId === 'libya-civcas-belligerents-timeline' || chartId === 'libya-strikes-timeline'){
			numTicks = 1;
		}
		if(element.current.getAttribute('data-query') === 'lang=en&belligerent=palestinian-militants&country=israel&conflict=77037' || element.current.getAttribute('data-query') === 'lang=ar&belligerent=palestinian-militants&country=israel&conflict=77086' || element.current.getAttribute('data-query') === 'lang=he&belligerent=palestinian-militants&country=israel&conflict=80428'){
			numTicks = 3;
		}


		dataByGroup.forEach(function(item, i){
			let inverseIndex = ((dataByGroup.length-1) - i);
			let yTransform = (y0.bandwidth() * i) - (padding * inverseIndex);
			
			let multpleYAxisElements = svg.append('g')
				.attr('id', 'axis-'+i)
				.attr('class', 'y axis multiple')
				.attr('opacity', function(){
			
					if(props.chartType === 'stacked'){
						return 0;
					} else {
						return 1;
					}
			
				})
				.attr('transform', 'translate(0,' + yTransform + ')')
				.call(d3.axisRight(y1).ticks(numTicks).tickSize(width));
			multpleYAxisElements.selectAll('.tick text').attr('x', 0).attr('dy', -4);
		});

		var yAxis = d3.axisRight(y2).tickSize(width).tickFormat(x => Math.round(x));

		if(element.current.getAttribute('data-query') === 'lang=en&belligerent=palestinian-militants&country=israel&conflict=77037'){
			yAxis.ticks(max);
		}


		let yAxisElement = svg.append('g')
			.attr('class', 'y axis stacked')
			.attr('opacity', e=>{
				if(props.chartType === 'stacked'){
					return 1;
				} else {
					return 0;
				}
			})
			.call(yAxis);

		yAxisElement.selectAll('.tick text').attr('x', 0).attr('dy', -4);
		






	}, [props.chartType])
	

	return (
		<div className={classNames.join(' ')}>
			<div className="events-container">{eventsContent}</div>
			<div ref={element}></div>			
			<div className={tooltipClasses.join(' ')} ref={tooltipEl}>{tooltipContent}</div>
		</div>
	)
}