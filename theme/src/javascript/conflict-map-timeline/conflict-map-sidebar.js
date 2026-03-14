import React from 'react';
import moment from 'moment';

import Config from '../config/config';




class StrikeRow extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			open: false,
		};
	}

	render() {

		const classNames = ['incident'];
		if (this.state.open && !this.props.feature.properties.is_civcas_incident) {
			classNames.push('open');
		}
		if(this.props.feature.properties.is_civcas_incident){
			classNames.push('civcas');
		}

		let strikeLabel = this.props.ui.strike;
		if(this.props.totalAirstrikes > 1){
			strikeLabel = this.props.ui.strikes;
		}



		return (
			<div className={classNames.join(' ')} onClick={e => this.setState({ open: !this.state.open})}>
				<div className="expand"><i className="fas fa-caret-down"></i></div>
				<div className="uniquid">{this.props.reference}</div>
				<div className="num_strikes">{this.props.totalAirstrikes}</div>
				<div className="belligerent">{this.props.belligerents}</div>
				<div className="casualties">{this.props.civcas}</div>
				<div className="date">{this.props.date}</div>
				
				{this.state.open && (
					<div className="additional-info">
						<div>
							<div className="label">{this.props.ui.strike_details}:</div>
							<div className="lat-lng">{this.props.feature.properties.latitude}, {this.props.feature.properties.longitude}</div>
							<div className="location">{this.props.feature.properties.location_name}, {this.props.feature.properties.location_name_arabic}</div>
							<div className="accuracy">(accurate to {this.props.feature.properties.geolocation_accuracy})</div>

						</div>
						<div>
							<div className="label">{this.props.ui.type_of_strike}:</div>
							<div>{this.props.feature.properties.type_of_strike}</div>	
							<div>{this.props.totalAirstrikes} {strikeLabel}</div>						
						</div>
					</div>
				)}
			</div>

		);
	}
}

class IncidentRow extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			open: false,
		};
	}

	render() {

		const classNames = ['incident'];
		if (this.state.open) {
			classNames.push('open');
		}
		if(this.props.mod_statement){
			classNames.push('expandable');
		}


		return (
			<div className={classNames.join(' ')} onClick={e => this.setState({ open: !this.state.open})}>
				<div className="expand">
					{this.props.mod_statement && (
						<i className="fas fa-caret-down"></i>
					)}
				</div>
				<div className="uniquid"><span style={{color: this.props.color}}>{this.props.unique_reference_code}</span></div>
				<div className="belligerent">{this.props.belligerents}</div>
				<div className="casualties">{this.props.civilians_killed_max}</div>
				<div className="injured">{this.props.injured}</div>
				<div className="date">{this.props.date}</div>
				{this.state.open && this.props.mod_statement && (
					<div className="statement">
						<h5>MoD Statement</h5>
						<div dangerouslySetInnerHTML={{__html: this.props.mod_statement}}></div>
					</div>
				)}
				
			</div>

		);
	}
}


export default class ConflictMapSidebar extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		let selectedFeatures = [];

		let totalAirStrikes = 0;
		this.props.selectedFeatures.forEach((feature, idx) => {

			totalAirStrikes += feature.properties.total_airstrikes;
			let props = feature.properties;
			let gradingLabel = '';
			if(this.props.gradings[props.grading] !== undefined){
				gradingLabel = this.props.gradings[props.grading].label;
			}
			
			let date = moment(props.date).format('MMM. DD, YYYY');
			

			if(this.props.lang === 'ar'){
				const jsDate = new Date(props.date); 
				const options = {
					year: '2-digit',
					month: 'short',
					day: 'numeric',
				};
				date = jsDate.toLocaleDateString('ar-EG', options);
			}
			
			
			const belligerent = props.belligerentSlug;
			let color = Config.colors[belligerent];
			let belligerents = [props.belligerent];
			if(props.belligerents && this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){
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

			let injured = props.civilians_injured_min + '-' + props.civilians_injured_max;
			if(props.civilians_injured_max === props.civilians_injured_min){
				injured = props.civilians_injured_max;
			}


			const strikeStatuses = {
				single_source_claim: 'Single Source',
				declared_strike: 'Declared',
				contested_strike: 'Contested',
				likely_strike: 'Likely'
			};

			let row = (
				<div className="incident" key={`${feature.properties.unique_reference_code}-${belligerent}-${idx}`}>
					<div className="uniquid"><a style={{color: color}} target="blank" href={props.permalink.toString()}>{props.unique_reference_code}</a></div>
					<div className="belligerent">{belligerents}</div>
					<div className="reporting">{gradingLabel}</div>					
					<div className="casualties">{civcas}</div>
					<div className="date">{date}</div>
				</div>
			);
			if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictId === 41464){
				row = <IncidentRow 
					ui={this.props.ui}
					key={feature.properties.unique_reference_code + '_' + idx + '_' + belligerent}
					color={color}
					unique_reference_code={feature.properties.unique_reference_code}
					belligerents={belligerents}
					civilians_killed_max={props.civilians_killed_max}
					injured={injured}
					date={date}
					mod_statement={feature.properties.mod_statement}
				/>;

			} else if(this.props.currentMap === 'militant-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') === -1){

				let militantFatalities = props.militants_killed_min + '-' + props.militants_killed_max;
				if(props.militants_killed_min === props.militants_killed_max){
					militantFatalities = props.militants_killed_max;
				}

				let reference;
				if(props.permalink){
					reference = <a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a>;
				} else {
					reference = <span style={{color: '#999'}}>{this.props.ui.strike}</span>;
				}


				row = (
					<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
						<div className="uniquid">{reference}</div>
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
			} else if (this.props.currentMap === 'strikes' || (this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1) && this.props.currentMap === 'militant-fatalities'){
				let reference;
				if(props.permalink){
					reference = <a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a>;
				} else {
					reference = <span style={{color: '#999'}}>{this.props.ui.strike}</span>;
				}

				row = <StrikeRow 
					ui={this.props.ui}
					key={feature.properties.unique_reference_code+belligerent}
					feature={feature}
					reference={reference}
					totalAirstrikes={props.total_airstrikes}
					belligerents={belligerents}
					civcas={civcas}
					date={date}
				/>;

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




		let selectedFeaturesValue = selectedFeatures.length;

		let columnHeadings = [
			<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
			<div key="belligerent" className="belligerent">belligerent</div>,
			<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
			<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
			<div key="date" className="date">{this.props.ui.heading_date}</div>
		];


		if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictId === 41464){
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="belligerent" className="belligerent">belligerent</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="injured" className="injured">injured</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>,

			];
		} else if(this.props.currentMap === 'militant-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') === -1){
			incidentLabel = this.props.ui.militant_fatality_incidents_in_this_area;
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = this.props.ui.militant_fatality_incident_in_this_area;
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_militant_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];
		} else if(this.props.currentMap === 'militant-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){
			incidentLabel = this.props.ui.militant_fatality_incidents_in_this_area;
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = this.props.ui.militant_fatality_incident_in_this_area;
			}
			columnHeadings = [
				<div key="expand" className="expand"></div>,
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="num_strikes" className="num_strikes">{this.props.ui.num_strikes}</div>,
				<div key="belligerent" className="belligerent">belligerents</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_militant_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];	
		} else if (this.props.currentMap === 'strike-locations'){

			selectedFeaturesValue = totalAirStrikes;
			
			incidentLabel = this.props.ui.strikes_in_this_area;
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = this.props.ui.strike_in_this_area;
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="strike-status" className="strike-status">{this.props.ui.heading_strike_status}</div>,				
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];
		} else if (this.props.currentMap === 'strike-target'){

			incidentLabel = this.props.ui.strikes_with_a_known_target_in_this_area;
			if(this.props.selectedFeatures.length === 1){
				incidentLabel = this.props.ui.strike_with_a_known_target_in_this_area;
			}
			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="strike-target" className="strike-target">{this.props.ui.heading_strike_target}</div>,				
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>
			];			
		} else if(this.props.currentMap === 'strikes' || (this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1 && this.props.currentMap === 'militant-fatalities')){
			selectedFeaturesValue = totalAirStrikes;

			// incidentLabel = this.props.ui.strike_events_in_this_area;
			incidentLabel = this.props.ui.strikes_in_this_area;
			if(this.props.selectedFeatures.length === 1){
				// incidentLabel = this.props.ui.strike_event_in_this_area;
				incidentLabel = this.props.ui.strikes_in_this_area;
			}
			columnHeadings = [
				<div key="expand" className="expand"></div>,
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="num_strikes" className="num_strikes">{this.props.ui.num_strikes}</div>,
				<div key="belligerent" className="belligerent">{this.props.ui.belligerents}</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
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
					<div className="result incident"><span className="value">{selectedFeaturesValue}</span> {incidentLabel}</div>

					{selectedFeatures}
				</div>
				
			</div>
		);
	}
	
	slugify(str){
		return str.replace(/ /g, '-').toLowerCase();
	}
}
