import React from 'react';
import moment from 'moment';

import Config from '../config/config';

export default class CivcasMapSidebar extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		//const somaliaConflictId = 59865;

		let selectedFeatures = [];
		this.props.selectedFeatures.forEach((feature) => {

			let props = feature.properties;
			let gradingLabel = '';
			if(this.props.gradings[props.grading] !== undefined){
				gradingLabel = this.props.gradings[props.grading].label;
			}
			let date = moment(props.date).format('MMM. DD, YYYY');
			const belligerent = props.belligerentSlug;
			let color = Config.colors[belligerent];
			if(this.props.country === 'yemen'){
				color = '#798ebb';
			}
			let belligerents = [props.belligerent];

			if(props.belligerents && props.country === 'libya'){
				const belligerent_list = JSON.parse(props.belligerents);
				belligerents = [];
				belligerent_list.forEach((b, i)=>{
					belligerents.push(<span style={{color: Config.colors.libya_map[b.slug]}} key={'belligerent-'+b.slug+'-'+i}>{b.name_short}</span>);
				});
			}

			let civcas = props.civilians_killed_min + '-' + props.civilians_killed_max;
			if(props.civilians_killed_max === props.civilians_killed_min){
				civcas = props.civilians_killed_max;
			}


			const strikeStatuses = {
				single_source_claim: 'Single Source',
				declared_strike: 'Declared',
				contested_strike: 'Contested',
				likely_strike: 'Likely'
			};




			let row = (
				<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
					<div className="uniquid"><a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a></div>
					<div className="belligerent">{belligerents}</div>
					<div className="reporting">{gradingLabel}</div>					
					<div className="casualties">{civcas}</div>
					<div className="date">{date}</div>
				</div>
			);

			if(this.props.currentMap === 'militant-fatalities'){
				
				let militantFatalities = props.militants_killed_min + '-' + props.militants_killed_max;
				if(props.militants_killed_min === props.militants_killed_max){
					militantFatalities = props.militants_killed_max;
				}

				row = (
					<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
						<div className="uniquid"><a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a></div>
						<div className="reporting">{gradingLabel}</div>
						<div className="casualties">{militantFatalities}</div>
						<div className="date">{date}</div>
					</div>
				);
			} else if(this.props.currentMap === 'strike-locations'){
				let locationColor = Config.colors.declared;
				if(props.strike_status !== 'declared_strike'){
					locationColor = Config.colors.alleged;
				}

				row = (
					<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
						<div className="uniquid"><a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a></div>
						<div className="strike-status" style={{color: locationColor}}>{strikeStatuses[props.strike_status]}</div>
						<div className="casualties">{civcas}</div>
						<div className="date">{date}</div>
					</div>
				);
			} else if (this.props.currentMap === 'strike-target'){

				let targets = JSON.parse(props.strike_targets);
				let targetEls = [];
				targets.forEach((target)=>{
					const targetEl = <span key={`target_${target.slug}`} style={{ color: Config.colors[target.slug]}}>{target.name}</span>;
					targetEls.push(targetEl);
				});

				row = (
					<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
						<div className="uniquid"><a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a></div>

						<div className="strike-target">{targetEls}</div>
						<div className="casualties">{civcas}</div>
						<div className="date">{date}</div>
					</div>
				);				
			} else if (this.props.currentMap === 'strikes'){
				let reference;
				if(props.permalink){
					reference = <a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a>
				} else {
					reference = <span style={{color: '#999'}}>Strike</span>
				}
				row = (
					<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
						<div className="uniquid">{reference}</div>
						<div className="belligerent">{belligerents}</div>
						<div className="reporting">{gradingLabel}</div>					
						<div className="casualties">{civcas}</div>
						<div className="num_strikes">{props.total_airstrikes}</div>
						<div className="date">{date}</div>
					</div>
				)
			}

			selectedFeatures.push(row);
		});
	
		let incidentLabel = this.props.ui.civilian_casualty_incidents_in_this_area;
		if(this.props.selectedFeatures.length === 1){
			incidentLabel = this.props.ui.civilian_casualty_incident_in_this_area;
		}

		const classes = [];
		if(selectedFeatures.length === 0){
			classes.push('hidden');
		}
		classes.push(this.props.currentMap);
		let columnHeadings = [
			<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
			<div key="belligerent" className="belligerent">belligerent</div>,
			<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
			<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
			<div key="date" className="date">{this.props.ui.heading_date}</div>
		];


		if(this.props.currentMap === 'militant-fatalities'){
			incidentLabel = 'militant fatality incidents in this area';
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = 'militant fatality incident in this area';
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_militant_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];
		} else if (this.props.currentMap === 'strike-locations'){
			incidentLabel = 'strikes in this area';
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = 'strike in this area';
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="strike-status" className="strike-status">{this.props.ui.heading_strike_status}</div>,				
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];
		} else if (this.props.currentMap === 'strike-target'){
			
			incidentLabel = 'strikes with a known target in this area';
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = 'strike with a known target in this area';
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="strike-target" className="strike-target">{this.props.ui.heading_strike_target}</div>,				
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];			
		} else if(this.props.currentMap === 'strikes'){
			incidentLabel = 'strikes in this area';
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = 'strike in this area';
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="belligerent" className="belligerent">belligerent</div>,
				<div key="strike-status" className="strike-status">{this.props.ui.heading_strike_status}</div>,								
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="num_strikes" className="num_strikes">Num Strikes</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];	
		}

		
		return (
			<div className={classes.join(' ')} id="selected-features">

				<div onClick={this.props.onCloseWindow} className="close"><i className="fal fa-times"></i></div>
				<div className="sort">
					<div className="column-headers incident">{columnHeadings}</div>
				</div>
				<div className="incidents">
					<div className="result incident"><span className="value">{selectedFeatures.length}</span> {incidentLabel}</div>

					{selectedFeatures}
				</div>
				
			</div>
		);
	}
	
	slugify(str){
		return str.replace(/ /g, '-').toLowerCase();
	}
}
