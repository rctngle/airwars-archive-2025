import React, { Component } from 'react';
import moment from 'moment';
import ConflictTimelineGraph from './conflict-timeline-graph';
import ConflictMap from './conflict-map';

import { getDateFromValue } from './functions';

export default class ConflictMapTimeline extends Component {

	constructor(props) {
		super(props);

		this.state = {
			mapTypes: {
				'all-belligerents-in-libya': ['civilian-fatalities', 'strikes'],
				'all-belligerents-in-libya-2011': ['civilian-fatalities', 'strikes'],
				'us-forces-in-somalia': ['civilian-fatalities', 'militant-fatalities', 'strike-locations', 'strike-target'],
				'us-forces-in-yemen': ['civilian-fatalities', 'militant-fatalities', 'strike-locations', 'strike-target']
			},
			mapsWithOptions: [
				'us-forces-in-yemen',
				'us-forces-in-somalia',
				'us-forces-in-yemen-arabic',			
				'all-belligerents-in-libya',
				'all-belligerents-in-libya-arabic',
				'all-belligerents-in-libya-2011',
				'all-belligerents-in-libya-2011-arabic'
			],
			currentMap: 'civilian-fatalities',
			conflictSlug: props.data.conflict_slug,			
		};

		
		if ((this.state.mapsWithOptions.indexOf(props.data.conflict_slug) !== -1) && this.state.mapTypes[props.data.conflict_slug]) {
			const mapTypeOptions = this.state.mapTypes[props.data.conflict_slug];
			// this.state.currentMap = mapTypeOptions[Math.floor(Math.random() * mapTypeOptions.length)];
			this.state.currentMap = mapTypeOptions[0];
		} else {
			this.state.currentMap = 'civilian-fatalities';
		}	

		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.handleRangeSliderChange = this.handleRangeSliderChange.bind(this);
		this.startMapCycle = this.startMapCycle.bind(this);
		this.stopMapCycle = this.stopMapCycle.bind(this);
		this.handleMapChange = this.handleMapChange.bind(this);
		this.handleSliderValueChange = this.handleSliderValueChange.bind(this);
	}

	componentDidMount() {

		const { data } = this.props;
		const { conflicts } = data;

		const dates = [];
		const stringDates = [];
	
		conflicts.forEach((timeline) => {			
			for (let d in timeline.date_range) {
				if(d !== 'country' && d !== 'assessment_up_to_date'){

					const date = moment(timeline.date_range[d], 'YYYY-MM-DD');
					dates.push(date);	
					stringDates.push(timeline.date_range[d]);
				}
				
			}
		});

		let min = moment.min(dates);
		let max = moment.max(dates);

		const offset = min.toDate().getTimezoneOffset();
		let mn = new Date(min.toDate().getTime() - (offset*60*1000));
		let minString = mn.toISOString().split('T')[0];

		let mx = new Date(max.toDate().getTime() - (offset*60*1000));
		let maxString = mx.toISOString().split('T')[0];


		const dateRange = {
			min: min,
			max: max,
			minString: minString,
			maxString: maxString,
			days: max.diff(min, 'days'),
		};


		let startKey = 'start';
		let endKey = 'end';
		// if(lang === 'ar'){
		// 	startKey = 'end';
		// 	endKey = 'start';
		// }

		conflicts.forEach((conflict) => {
			
			conflict.moment_range = {
				conflict_start: moment(conflict.date_range['conflict_'+startKey], 'YYYY-MM-DD'),
				conflict_end: moment(conflict.date_range['conflict_'+endKey], 'YYYY-MM-DD'),
				
				monitoring_start: moment(conflict.date_range['monitoring_'+startKey], 'YYYY-MM-DD'),
				monitoring_end: moment(conflict.date_range['monitoring_'+endKey], 'YYYY-MM-DD'),

				assessment_start: moment(conflict.date_range['assessment_'+startKey], 'YYYY-MM-DD'),
				assessment_end: moment(conflict.date_range['assessment_'+endKey], 'YYYY-MM-DD'),

				published_start: moment(conflict.date_range['published_'+startKey], 'YYYY-MM-DD'),
				published_end: moment(conflict.date_range['published_'+endKey], 'YYYY-MM-DD'),
			};

			conflict.days = {
				conflict_offset: conflict.moment_range.conflict_start.diff(dateRange.min, 'days'),
				conflict_duration: conflict.moment_range.conflict_end.diff(conflict.moment_range.conflict_start, 'days'),

				monitoring_offset: conflict.moment_range.monitoring_start.diff(dateRange.min, 'days'),
				monitoring_duration: conflict.moment_range.monitoring_end.diff(conflict.moment_range.monitoring_start, 'days'),

				assessment_offset: conflict.moment_range.assessment_start.diff(dateRange.min, 'days'),
				assessment_duration: conflict.moment_range.assessment_end.diff(conflict.moment_range.assessment_start, 'days'),
				
				published_offset: conflict.moment_range.published_start.diff(dateRange.min, 'days'),
				published_duration: conflict.moment_range.published_end.diff(conflict.moment_range.published_start, 'days'),										
			};
		});



		const sliderRange = {
			value: 0,
			min: 0,
			max: dateRange.days,
		};

		sliderRange.minDate = getDateFromValue(dateRange, sliderRange.min);
		sliderRange.maxDate = getDateFromValue(dateRange, sliderRange.max);		


		this.setState({
			conflicts: conflicts,
			dateRange: dateRange,
			sliderRange: sliderRange,
		});

	}

	handleStyleLoaded() {
		this.startMapCycle();
	}

	handleSliderValueChange(value) {
		const sliderRange = this.state.sliderRange;
		sliderRange.value = value;
		this.setState({
			sliderRange: sliderRange,
		});
	}

	handleSliderChange(e) {

		let endMarkerOverlap;

		if(e[1] > 93){
			endMarkerOverlap = true;
		}
	
		this.setState({
			endMarkerOverlap: endMarkerOverlap, 
			sliderRange: {
				value: e[0],
				min: e[0],
				max: e[1],
				minDate: getDateFromValue(this.state.dateRange, e[0]),
				maxDate: getDateFromValue(this.state.dateRange, e[1]),
			},
		});
	}

	handleRangeSliderChange(e) {
			
		let endMarkerOverlap;
		if(e[1] > 93){
			endMarkerOverlap = true;
		}
	
		this.setState({
			endMarkerOverlap: endMarkerOverlap, 
			sliderRange: {
				value: e[0],
				min: e[0],
				max: e[1],
				minDate: getDateFromValue(this.state.dateRange, e[0]),
				maxDate: getDateFromValue(this.state.dateRange, e[1]),
			},
		});
	}

	startMapCycle() {
		if (!this.props.data.conflict_post_id) {
			const mapCycleInterval = setInterval(() => {
				let nextMapIdx = this.state.mapIdx + 1;
				if (nextMapIdx > this.state.maps.length -1) {
					nextMapIdx = 0;
				}

				this.setState({
					mapIdx: nextMapIdx,
				});
			}, 4000);

			this.setState({
				mapCycleInterval: mapCycleInterval,
			});
		}		
	}

	stopMapCycle() {
		if (this.state.mapCycleInterval) {
			clearInterval(this.state.mapCycleInterval);
		}
	}

	handleMapChange(type) {
		this.setState({
			currentMap: type,
		});
	}

	render() {
		//
		if (this.state.conflicts === undefined) {
			return null;
		}

		let timeline;

		const excludeTimeline = [
			'israeli-military-in-iraq-syria',
			'israeli-military-in-syria-the-gaza-strip',
			'israeli-military-in-syria-the-gaza-strip-arabic',
			'israeli-military-in-syria-the-gaza-strip-hebrew'
		];

		if(excludeTimeline.indexOf(this.props.data.conflict_slug) === -1){
			timeline = (
				<ConflictTimelineGraph 
					{...this.state} 						
					lang={this.props.data.post_data.lang} 
					conflictSlug={this.props.data.conflict_slug} 
					onRangeChange={this.handleRangeSliderChange} 
					onSliderValueChange={this.handleSliderValueChange}
				/>
			);
		}
		let annotation = false;
		let conflictSlug = this.props.data.conflict_slug;

		//  && this.props.countrySlug === 'the-gaza-strip'
		if(conflictSlug === 'israeli-military-in-syria-the-gaza-strip-the-gaza-strip'){
	
			if (this.props.data.post_data.lang === 'en') {
				annotation = (
					<div className="annotation"><span>View actions from <a target="blank" href="/conflict/palestinian-militants-in-israel/">Palestinian Militants in Israel</a> over the same period</span> <i className="fal fa-long-arrow-right"></i></div>
				);	
			} else if (this.props.data.post_data.lang === 'ar') {
				annotation = (
					<div className="annotation"><span>عرض عمليات <a target="blank" href="/conflict-ar/palestinian-militants-in-israel-arabic/">الفصائل الفلسطينية في اسرائيل</a> خلال ذات المدة</span> <i className="fal fa-long-arrow-right"></i></div>
				);					
			} else if (this.props.data.post_data.lang === 'he') {
				annotation = (
					<div className="annotation"><span>צפה בפעולות של <a target="blank" href="/conflict-he/palestinian-militants-in-israel-hebrew/">הקבוצות המיליטנטיות הפלסטיניות בישראל</a> באותה התקופה</span> <i className="fal fa-long-arrow-right"></i></div>
				);				
			}
		}

		if (this.props.data.post_data.note) {
			annotation = <div className="annotation" dangerouslySetInnerHTML={{__html: this.props.data.post_data.note}}></div>
		}

		return (
			<div className="conflict-map-timeline">
				{!this.props.mapOnly && (
					<div className="postdata">
						<div className="content">
							<div className="full">
								<div className="postdata__content">
									<div>
										<h1>{this.props.data.post_data.title}</h1>
										<h1 className="subheading">{this.props.data.post_data.subtitle}</h1>
										<div dangerouslySetInnerHTML={{__html: this.props.data.post_data.content}}></div>
									</div>
									{this.props.data.post_data.translations.length > 0 && (
										<div>
											<h4>Languages</h4>
											<div className="langswitcher">
												<a href="?lang=en">English</a>
												{this.props.data.post_data.translations.map(translation => {
													return <a href={`?lang=${translation.value}`}>{translation.label}</a>
												})}
											</div>
										</div>
									)}
								</div>
							</div>
						</div>
					</div>
				)}
				
				{timeline}
				<ConflictMap 
					conflictId={this.props.data.conflict_post_id}
					{...this.state}
					postDataSlug={this.props.data.post_data.slug}
					lang={this.props.data.post_data.lang} 
					breakpoint={this.props.breakpoint} 
					conflictSlug={this.props.data.conflict_slug} 
					onStyleLoaded={this.handleStyleLoaded} 
					onMapMouseDown={this.stopMapCycle} 
					onMapChange={this.handleMapChange}
				/>
				{annotation}

			</div>
		);
	}
}
	
// <ConflictMap {...this.state} lang={this.props.data.post_data.lang} onStyleLoaded={this.handleStyleLoaded} onMapMouseDown={this.stopMapCycle} />
