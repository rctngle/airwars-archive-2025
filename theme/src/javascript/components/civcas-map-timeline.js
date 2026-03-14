import React from 'react';

import Store from '../stores/store';
import Actions from '../actions/actions';
import CivcasTimeline from './civcas-timeline';
import CivcasMap from './civcas-map';
import Config from './../config/config';

export default class CivcasMapTimeline extends React.Component {

	constructor(props) {

		super(props);
		let sliderRange = {
			min: 70,
			max: 97,
		};

		let country;

		let endMarkerOverlap = false;


		if(this.props.conflictId === 59790 || this.props.conflictId === 59865){
		
			sliderRange.max = 100;
			endMarkerOverlap = true;
			country = 'somalia';
		} else if(this.props.conflictId === 41467 || this.props.conflictId === 41472){
			country = 'libya';
			sliderRange = {
				min: 0,
				max: 100
			};
		} else if (this.props.conflictId === 67815 || this.props.conflictId === 67823){

			country = 'yemen';
			if(this.props.lang === 'en'){
				sliderRange = {
					min: 65.3,
					max: 100
				};
			} else {
				sliderRange = {
					min: 0,
					max: 34.7
				};
			}
			if(window.outerWidth < Config.breakpoints.xsm){
				sliderRange.max = 100;
			}		
		} else {

			if(window.outerWidth < Config.breakpoints.xsm){
				sliderRange = {
					min: 50,
					max: 70
				};
			} else if(this.props.lang === 'ar'){
				sliderRange = {
					min: 3,
					max: 30
				};
			}			
		}


		this.state = {
			sliderRange: sliderRange,
			endMarkerOverlap: endMarkerOverlap,
			mapCycleInterval: undefined,
			mapIdx: 0,
			country: country,
			maps: [{
				id: 'libya',
				lat: 30.653,
				lng: 18.203,
			}, {
				id: 'iraq-syria',
				lat: 33.582,
				lng: 41.095,
			}, {
				id: 'yemen',
				lat: 15.2107,
				lng: 47.9411
			}, {
				id: 'somalia',
				lat: 5.543,
				lng: 47.711
			}, {
				id: 'israel',
				lat: 32.0879,
				lng: 34.7622
			}, {
				id: 'ukraine',
				lat: 49.243,
				lng: 37.057
			}

			// {
			// 	id: 'pakistan',
			// 	lat: 30.152,
			// 	lng: 69.688,
			// }, {
			// 	id: 'yemen',
			// 	lng: 15.624,
			// 	lat: 47.511
			// }
			]
		};
		this.handleChange = this.handleChange.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.handleSliderChange = this.handleSliderChange.bind(this);
		this.startMapCycle = this.startMapCycle.bind(this);
		this.stopMapCycle = this.stopMapCycle.bind(this);
		
	}

	componentDidMount() {
		Store.addChangeListener(this.handleChange);
		Actions.fetchMapTimlineConflicts(this.props.conflictId, this.props.lang);
	}

	componentWillUnMount() {
		Store.removeChangeListener(this.handleChange);
		this.stopMapCycle();
	}

	startMapCycle() {
		if (!this.props.conflictId) {
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

	handleChange() {

		const position = Store.getStartSliderDate();

		if(position.start){

			let sliderRange = this.state.sliderRange;

			let key = 'min';
			if(this.props.lang === 'ar'){
				key = 'max';
			}

			sliderRange[key] = Store.getValueFromDate(position.start);

			if(window.outerWidth < Config.breakpoints.xsm){
				sliderRange = {
					min: 50,
					max: 70
				};
			}

			this.setState({
				sliderRange: sliderRange
			});	
		}
		

		const sliderRange = this.state.sliderRange;
			

		sliderRange.minDate = Store.getDateFromValue(sliderRange.min);
		sliderRange.maxDate = Store.getDateFromValue(sliderRange.max);


		this.setState({
			conflicts: Store.getMapTimelineConflicts(),
			dateRange: Store.getDateRange(this.props.lang),
			sliderRange: sliderRange
		});
		
	}

	render() {
		if (this.state.conflicts === undefined) {
			return null;
		}

		return (
			<div>
				<CivcasTimeline {...this.state} lang={this.props.lang} onSliderChange={this.handleSliderChange} />
				<CivcasMap {...this.state} lang={this.props.lang} onStyleLoaded={this.handleStyleLoaded} onMapMouseDown={this.stopMapCycle} />
			</div>
		);
	}

	handleStyleLoaded() {
		this.startMapCycle();
	}

	handleSliderChange(e) {
		
		clearTimeout(this.rangeTimeout);
		this.rangeTimeout = setTimeout(() => {
			let endMarkerOverlap;
			if(e[1] > 93){
				endMarkerOverlap = true;
			}

			this.setState({
				endMarkerOverlap: endMarkerOverlap, 
				sliderRange: {
					min: e[0],
					max: e[1],
					minDate: Store.getDateFromValue(e[0]),
					maxDate: Store.getDateFromValue(e[1]),
				},
			});
		}, 500);
	}
}
