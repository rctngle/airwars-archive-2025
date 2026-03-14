import React, { Component } from 'react';
import Slider, {Range} from 'rc-slider';
import moment from 'moment';

import ReactAnimationFrame from 'react-animation-frame';
import { getDateFromValue } from './functions';
import ConflictTimelineGraphBars from './conflict-timeline-graph-bars';

const Handle = Slider.Handle;

class ConflictTimelineGraph extends Component {

	constructor(props) {
		super(props);
		this.state = {
			autoplay: false,
		};


		this.handlePrevious = this.handlePrevious.bind(this);
		this.handleNext = this.handleNext.bind(this);
		this.handlePlayPause = this.handlePlayPause.bind(this);
		
		this.handleRangeChange = this.handleRangeChange.bind(this);
	
		
	}
	getDates(startDate, endDate) {
		const dates = [];
		let currentDate = startDate;
		const addDays = function(days) {
			const date = new Date(this.valueOf());
			date.setDate(date.getDate() + days);
			return date;
		};
		while (currentDate <= endDate) {
			dates.push(currentDate);
			currentDate = addDays.call(currentDate, 1);
		}
		return dates;
	}

	buildDays(){
		

		const days = {};
		let currDate = this.props.dateRange.min.clone();
		let lastDate = this.props.dateRange.max.clone();

		const jsDates = this.getDates(new Date(this.props.dateRange.minString), new Date(this.props.dateRange.maxString));
		jsDates.forEach(date=>{
			const day = date.toISOString().substring(0, 10);	

			days[day] = {
				day: day,
				civilians_killed_min: 0,
				militants_killed_min: 0,
				strikes: 0
			}
		});


		this.props.conflicts.forEach(conflict => {

			conflict.civcas_incidents.forEach(incident => {
				const day = incident.date.substring(0, 10);
				if(days[day]){

					days[day].civilians_killed_min += incident.civilians_killed_min;
					days[day].strikes++;
					days[day].militants_killed_min += incident.militants_killed_min;
					// How to do this?
					if(day === '2018-03-17' && this.props.conflictslug.indexOf('turkish-military-in-iraq-and-syria') !== -1){
						days[day].original_value = days[day].civilians_killed_min;
						days[day].civilians_killed_min = 1;

					}
			

				}				
			});	

			
		});


		let max = 0;
		let key;


		if(this.props.currentMap.indexOf('strike') !== -1){
			key = 'strikes';
		} else if (this.props.currentMap === 'civilian-fatalities'){
			key = 'civilians_killed_min';
		} else if(this.props.currentMap === 'militant-fatalities'){
			key = 'militants_killed_min';
		}


		for (let day in days) {
			if(days[day][key] > max){
				max = days[day][key];
			}			
		}


		for (let day in days) {
			if(days[day][key] > 0){
				days[day].percentage = (days[day][key] / max);	
			}			
		}

		this.setState({
			timeline: Object.values(days)
		});
	}
	componentDidMount() {
		this.buildDays();
		

	}

	componentDidUpdate(prevProps) {

		if (this.props.currentMap !== prevProps.currentMap) {
			this.buildDays();
		}
	}

	handleRangeChange(e) {
		clearTimeout(this.rangeTimeout);
		this.rangeTimeout = setTimeout(() => {
			this.props.onRangeChange(e);
		}, 500);
	}


	onAnimationFrame() {
		if (this.state.autoplay) {
			let sliderValue = this.props.sliderRange.value + 1;

			if (sliderValue < this.props.sliderRange.min) {
				sliderValue = this.props.sliderRange.min;
			}

			if (sliderValue > this.props.sliderRange.max) {
				sliderValue = this.props.sliderRange.min;	
			}

			this.props.onSliderValueChange(sliderValue);
			// this.setState({
			// 	sliderValue: Math.floor(sliderValue),
			// });
		}	

	}

	handlePlayPause() {
		this.setState({
			autoplay: !this.state.autoplay,
		});
	}

	handlePrevious() {

		let sliderValue = this.props.sliderRange.value - 1;

		if (sliderValue < this.props.sliderRange.min) {
			sliderValue = this.props.sliderRange.min;
		}

		if (this.state.autoplay) {
			this.setState({
				autoplay: false,
			});		
		}
		this.props.onSliderValueChange(sliderValue);
	}
	handleNext() {
		let sliderValue = this.props.sliderRange.value + 1;

		if (sliderValue > this.props.sliderRange.max) {
			sliderValue = this.props.sliderRange.min;
		}

		if (this.state.autoplay) {
			this.setState({
				autoplay: false,
			});		
		}
		this.props.onSliderValueChange(sliderValue);

	}

	render() {
		

		if (!this.state.timeline) {
			return null;
		}
		
		let playPauseValue = 'Pause';
		if(!this.state.autoplay){
			playPauseValue = 'Play Selection';
		}

		let startDayDisplay = moment(this.state.timeline[0].day).format('DD MMM YYYY');
		let endDateDisplay = moment(this.state.timeline[this.state.timeline.length-1].day).format('DD MMM YYYY');
		
		let reverse = false;

		if(this.props.lang === 'ar' || this.props.lang === 'he'){
			reverse = true;
		}

		if(this.props.lang === 'ar'){

			var startJsDate = new Date(this.state.timeline[0].day);
			var endJsDate = new Date(this.state.timeline[this.state.timeline.length-1].day);

			const options = {
				year: '2-digit',
				month: 'short',
				day: 'numeric',
			};
			endDateDisplay = startJsDate.toLocaleDateString('ar-EG', options);
			startDayDisplay = endJsDate.toLocaleDateString('ar-EG', options);

			
		}


		const conflict = this.props.conflicts[0];

		let assessmentWidth = (conflict.days.assessment_duration/this.props.dateRange.days) * 100;
		
		let monitoredStyles = {
			left: (conflict.days.assessment_offset/this.props.dateRange.days) * 100 + '%',
			width: assessmentWidth + '%'
		};


		
		if(this.props.lang === 'ar' || this.props.lang === 'he'){
			monitoredStyles.left = 'auto';
			monitoredStyles.right = (conflict.days.assessment_offset/this.props.dateRange.days) * 100 + '%';
		}		

		let assessmentUpToDate = true;
		const ui = this.props.conflicts[0].ui_terms;

		this.props.conflicts.forEach((conflict, i) => {					
			if (!conflict.date_range.assessment_up_to_date) {
				assessmentUpToDate = false;
			}
		});

		let monitoring = null;

		if (!assessmentUpToDate || assessmentWidth !== 100) {
			monitoring = (
				<div className="monitoring">
					<div className="bar"></div>
					<div className="description">{ui.civilian_casualty_reports_monitored_but_not_yet_assessed}</div>
				</div>
			);
		}		

		let yemenEvent;

		if(this.props.conflictslug.indexOf('us-forces-in-yemen') !== -1){
			if (this.props.lang == 'ar') {
				yemenEvent = <div className="yemen-event"><a href="/civilian-casualties/yemb001-november-3-2002">حادثة</a> واحدة في 2002 <i className="far fa-arrow-right"></i></div>;
			} else {
				yemenEvent = <div className="yemen-event"><i className="far fa-arrow-left"></i><span>1</span> <a href="/civilian-casualties/yemb001-november-3-2002">incident</a> in 2002</div>;
			}
		}


		const controls = null;
		// const controls = (
		// 	<div className="timeline-controls">
		// 		<div onClick={this.handlePrevious}><i className="fal fa-angle-double-left"></i></div>
		// 		<div className="pause-play" onClick={this.handlePlayPause}>{playPauseValue}</div>
		// 		<div onClick={this.handleNext}><i className="fal fa-angle-double-right"></i></div>
		// 	</div>
		// );

		let defaultMin = this.props.sliderRange.min;
		let defaultMax = this.props.sliderRange.max;
		
		if(this.props.conflictslug.indexOf('russian-military-in-syria') !== -1){
			// defaultMax = 1553;
		} else if(this.props.conflictslug.indexOf('us-forces-in-yemen') !== -1){
			defaultMin = 2580;
		}

		return (
			<div className="timeline-controls-container">
				<div className="timeline-container content">

					<div className="full">					
						{yemenEvent}
						<div className="overflow">
							<div className="timeline-block monitored" style={monitoredStyles}></div>				
							<ConflictTimelineGraphBars 
								currentMap={this.props.currentMap}
								lang={this.props.lang}
								conflictslug={this.props.conflictslug}
								conflict={conflict}
								timeline={this.state.timeline}
								sliderValue={this.props.sliderRange.value} 
							/>
						</div>

						<Range 
							step={0.05} 
							min={0} 
							max={this.props.dateRange.days} 
							reverse={reverse}
							defaultValue={[defaultMin, defaultMax]} handle={(props) => {
								
								const { value, dragging, index, ...restProps } = props;
								const date = getDateFromValue(this.props.dateRange, value);

								let dateFormatted = date.format('DD MMM YYYY');
								
								if(this.props.lang === 'ar'){
									var jsDate = new Date(date);
									const options = {
										year: '2-digit',
										month: 'short',
										day: 'numeric',
									};
									dateFormatted = jsDate.toLocaleDateString('ar-EG', options);
								}



								return (
									<Handle key={index} value={value} {...restProps}>
										<div className="triangle"></div>
										<div className="line"></div>
										<div className="handle-date">{dateFormatted}</div>
									</Handle>
								);
							}} onChange={this.handleRangeChange}
						/>
						<div className="start-end-labels">
							<div className="start date-label">{startDayDisplay}</div>
							{controls}
							<div className="end date-label">{endDateDisplay}</div>
						</div>
					</div>
					<div className="full bars-legend">				
						<div className="legend">
							{monitoring}
						</div>
					</div>
				</div>
				
			</div>
		);		
	}
}


export default ReactAnimationFrame(ConflictTimelineGraph, 1000);