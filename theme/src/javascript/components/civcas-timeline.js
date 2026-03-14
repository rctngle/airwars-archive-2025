import React from 'react';
import Store from '../stores/store';
// import Actions from '../actions/actions';
import Slider, {Range} from 'rc-slider';
import Config from '../config/config';
import moment from 'moment';


const Handle = Slider.Handle;

const handle = (props) => {
	const { value, dragging, index, ...restProps } = props;
	const date = Store.getDateFromValue(value);

	let dateFormatted = date.format('DD MMM YYYY');
	if (dateFormatted == '19 Jan 2017') {
		dateFormatted =  '20 Jan 2017'
	}

	return (
		<Handle key={index} value={value} {...restProps}>
			<div className="triangle"></div>
			<div className="line"></div>
			<div className="handle-date">{dateFormatted}</div>
		</Handle>
	);
};

class Bar extends React.Component {
	constructor(props){
		super(props);
	}
	
	render() {

		const conflict = this.props.conflict;



		let assessmentWidth = (conflict.days.assessment_duration/this.props.dateRange.days) * 100;
		let assessmentLeft = (conflict.days.assessment_offset/this.props.dateRange.days) * 100;

		const conflictWidth = (conflict.days.conflict_duration/this.props.dateRange.days) * 100;
		const conflictLeft = (conflict.days.conflict_offset/this.props.dateRange.days) * 100;

		const monitorWidth = (conflict.days.monitoring_duration/this.props.dateRange.days) * 100;
		const monitorLeft = (conflict.days.monitoring_offset/this.props.dateRange.days) * 100;


		//let belligerent = (conflict.taxonomies.belligerent_terms.length > 0) ? conflict.taxonomies.belligerent_terms[0].slug : 'all-belligerents';
		let belligerent;

		if(conflict.slug.indexOf('libya') === -1){
			belligerent = conflict.taxonomies.belligerent_terms[0].slug;
		} else {
			if(conflict.title.indexOf('2011') === -1){
				belligerent = 'all-belligerents-in-libya';
			} else {
				belligerent = 'all-belligerents-in-libya-2011';
			}
			
		}



		let belligerentColor = Config.colors[belligerent];

		if(this.props.country === 'yemen' || (conflict.conflict_id === 67815 || conflict.conflict_id === 67823)){
			belligerentColor = Config.colors['us-forces-in-yemen'];
		}

		if(conflict.title.indexOf('Somalia') !== -1){
			belligerentColor = Config.colors['us-forces-in-somalia'];	
		}

		let conflictLink = this.props.conflict.permalink;
		const classNames = ['conflict'];
		let barTitle = conflict.title;

		if(conflict.title.indexOf('2011') !== -1){

			if(this.props.lang == 'ar'){
				barTitle = '2011 ليبيا';
			} else {
				barTitle = 'Libya 2011';	
			}
			
		} 
		let pieces = [];
		let barHeight = 20;
		
		let publishedBackgroundColor = belligerentColor;
		
		if(this.props.lang === 'ar'){
			if (!conflictLink.includes('somalia')) {
				conflictLink = conflictLink.replace(/\/$/, "");
				conflictLink = conflictLink.replace('/conflict',  '/conflict-ar');
				conflictLink = conflictLink + '-arabic';
			}

		}



	
		return (

			<div style={{height: barHeight+'px'}} data-conflict-id={conflict.conflict_id} data-conflict-slug={conflict.slug} className={classNames.join(' ')}>
				<div className="conflict" style={{left: conflictLeft + '%', width: conflictWidth + '%'}}>
					<span>Conflict duration</span>
				</div>
				<div className="monitoring" style={{left: monitorLeft + '%', width: monitorWidth + '%'}}>
					<span>Airwars monitoring</span>
				</div>
				<div className="published" style={{left: assessmentLeft + '%', width: assessmentWidth + '%', backgroundColor: publishedBackgroundColor, borderColor: belligerentColor}}>
					<div className="piece-container">{pieces}</div>
					<a href={conflictLink}><span>{barTitle}</span></a>
				</div>
				<div className="off start" style={{left: 0, width: this.props.sliderRange.min + '%'}}></div>
				<div className="off end" style={{left: this.props.sliderRange.max + '%', right: 0}}></div>
			</div>
		);
	}
}

class Histogram extends React.Component {
	constructor(props){
		super(props);
	}
	
	render() {

		const conflict = this.props.conflict;
		
		const numMonths = conflict.civcas_by_belligerent[0].timeline.length;
		const pieceWidth = 100/numMonths;

		
		const monthTotals = [];
		conflict.civcas_by_belligerent.forEach((belligerent, i)=>{

			
			belligerent.timeline.forEach((month, j)=>{
				if(i === 0){
					monthTotals[j] = month.value;
				} else {
					monthTotals[j]+=month.value;
				}
			});
			
		});

		const pieces = [];
		monthTotals.forEach((month, i)=>{
			
			let pieceClass;
			let label;
			
			let innerPiece;

			if(this.props.index === i){
				pieceClass = 'selected';
			}
			pieces.push(
				<div className={pieceClass} key={`piece-${i}`} style={{height: month+'px', width: pieceWidth+'%'}}></div>
			);
		});


		return (
			<div className="total-histogram">
				<div className="piece-container">{pieces}</div>
				<div className="off start" style={{left: 0, width: this.props.sliderRange.min + '%'}}></div>
				<div className="off end" style={{left: this.props.sliderRange.max + '%', right: 0}}></div>
			</div>
		);
	}
}

export default class CivcasTimeline extends React.Component {

	constructor(props) {
		super(props);
		this.state = {};
		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.hoverBars = this.hoverBars.bind(this);
	}


	render() {
		
		const bars = [];
		const classes = ['content'];

		if(this.props.endMarkerOverlap){
			classes.push('end-marker-overlap');
		}

		let assessmentUpToDate = true;
		let isLibya = false;
		this.props.conflicts.forEach((conflict, i) => {

			// if(this.props.country === 'libya'){
			// 	isLibya = true;
			// 	conflict.civcas_by_belligerent.forEach((belligerent, idx)=>{

			// 		bars.push(<Bar 
			// 			key={`conflict_${belligerent.belligerent}_${i}`} 
			// 			conflict={conflict} 
			// 			belligerent={belligerent}
			// 			dateRange={this.props.dateRange} 
			// 			sliderRange={this.props.sliderRange}
			// 			index={this.state.index}
			// 		/>);						
			// 	});
			// } else {
			bars.push(<Bar 
				country={this.props.country}
				key={`conflict_${i}`} 
				conflict={conflict}
				lang={this.props.lang} 
				dateRange={this.props.dateRange} 
				sliderRange={this.props.sliderRange} 
			/>);	
			//}
			

			if (!conflict.date_range.assessment_up_to_date) {
				assessmentUpToDate = false;
			}
			if(this.props.country === 'yemen'){
				// assessmentUpToDate = false;
			}
		});


		const ui = this.props.conflicts[0].ui_terms;

		
		let yemenEvent;
		if(this.props.country === 'yemen'){
			if (this.props.lang == 'ar') {
				yemenEvent = <div className="yemen-event"><a href="/civilian-casualties/yemb001-november-3-2002">حادثة</a> واحدة في 2002 <i className="far fa-arrow-right"></i></div>;
			} else {
				yemenEvent = <div className="yemen-event"><i className="far fa-arrow-left"></i><span>1</span> <a href="/civilian-casualties/yemb001-november-3-2002">incident</a> in 2002</div>;
			}
		}	


		let monitoring = null;
		if (!assessmentUpToDate) {
			monitoring = (
				<div className="monitoring">
					<div className="bar"></div>
					<div className="description">{ui.civilian_casualty_reports_monitored_but_not_yet_assessed}</div>
				</div>
			);
		}


		classes.push('conflict-number-'+(bars.length-2));
		
		let totalHistogram;
		// if(isLibya){
		// 	totalHistogram = 
		// 		<Histogram
		// 			conflict={this.props.conflicts[0]} 
		// 			dateRange={this.props.dateRange} 
		// 			sliderRange={this.props.sliderRange}
		// 			index={this.state.index}
		// 		/>;
		// }

		return (
			<div className={classes.join(' ')}>
				<div className="full" id="timeline" >
					<div className="slider-container">
						{yemenEvent}
						<div className="total-date-range">
							<div>{this.props.dateRange.min.format('MMM YYYY')}</div>
							<div>{this.props.dateRange.max.format('MMM YYYY')}</div>
						</div>
						<Range 
							step={0.05} 
							min={0} 
							max={100} 
							defaultValue={[this.props.sliderRange.min, this.props.sliderRange.max]} 
							handle={handle} 
							onChange={this.handleSliderChange} 
						/>						
					</div>

					<div id="timeline-bars">
						{bars}
					</div>
					{totalHistogram}
				</div>
				<div className="full bars-legend">
					
					<div className="legend">
						{monitoring}
					</div>
				</div>
			</div>
		);
	}
	hoverBars(e){
		const target = e.target || e.srcElement;
		const rect = target.getBoundingClientRect();
		let offsetX = e.clientX - rect.left;
		let percentage = Math.max(offsetX / target.offsetWidth, 0);
		const numMonths = this.props.conflicts[0].civcas_by_belligerent[0].timeline.length;		
		const index = Math.round(numMonths*percentage);
		this.setState({
			index: index
		});
	}
	handleSliderChange(e) {
		this.props.onSliderChange(e);
	}
}

