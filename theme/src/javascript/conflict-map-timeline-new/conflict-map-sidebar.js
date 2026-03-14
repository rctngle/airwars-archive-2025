import React from 'react';
import moment from 'moment';

import Config from '../config/config';




// class StrikeRow extends React.Component {

// 	constructor(props) {
// 		super(props);
// 		this.state = {
// 			open: false,
// 		};
// 	}

// 	render() {

// 		const classNames = ['incident'];
// 		if (this.state.open && !this.props.feature.properties.civcas) {
// 			classNames.push('open');
// 		}
		
		

		





// 		return (
// 			<div className={classNames.join(' ')} onClick={e => this.setState({ open: !this.state.open})}>
// 				<div className="expand"><i className="fas fa-caret-down"></i></div>
// 				
// 				<div className="num_strikes">{this.props.totalAirstrikes}</div>
// 				<div className="belligerent">{this.props.belligerents}</div>
// 				<div className="casualties">{this.props.civcas}</div>
// 				<div className="date">{this.props.date}</div>
				
				
// 			</div>

// 		);
// 	}
// }

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
		if(this.props.expandable){
			classNames.push('expandable');
		}

		let strikeLabel;
		if(this.props.isStrikeRow){

			if(this.props.civcas){
				classNames.push('civcas');
			}
			strikeLabel = this.props.ui.strike;
			if(this.props.totalAirstrikes > 1){
				strikeLabel = this.props.ui.strikes;
			}
		}
		return (
			<div className={classNames.join(' ')} onClick={e => this.setState({ open: !this.state.open})}>


				{(this.props.expandable || this.props.postDataSlug == 'british-ekia') && 
					
					<div className="expand">
						{(this.props.shahed || this.props.mod_statement || this.props.isStrikeRow) && (
							<i className="fas fa-caret-down"></i>
						)}
					</div>
				}

				{this.props.reference && <div className="uniquid">{this.props.reference}</div>}
				
				{!this.props.reference && 
					<div className="uniquid">
						{(this.props.permalink && this.props.postDataSlug !== 'british-ekia' && this.props.postDataSlug !== 'shahed-map') && <a style={{color: this.props.color}} target="blank" href={this.props.permalink.toString()}>{this.props.unique_reference_code}</a>}
						{(!this.props.permalink || this.props.postDataSlug === 'british-ekia' || this.props.postDataSlug == 'shahed-map') && <span style={{color: this.props.color}}>{this.props.unique_reference_code}</span>}
					</div>
				}
				{this.props.shahed && 
					<React.Fragment>
						<div className="location">{this.props.feature.properties.location}</div>
						<div className="accuracy">{this.props.feature.properties.geolocation_accuracy}</div>						
						<div className="launched">{this.props.feature.properties.shahed_launched}</div>

					</React.Fragment>
				}
				
				{this.props.showGrading && <div className="grading">{this.props.grading}</div>}
				{this.props.showBelligerents && <div className="belligerent">{this.props.belligerents}</div>}
				{this.props.targetEls && <div className="strike-target">{this.props.targetEls}</div>}			
				{this.props.strikeStatus && <div className="strike-status">{this.props.strikeStatus}</div>}			
				{this.props.militantFatalities && <div className="militant-fatalities">{this.props.militantFatalities}</div>}			
				{this.props.civcas !== undefined && <div className="casualties">{this.props.civcas}</div>}
				{this.props.showInjured && <div className="injured">{this.props.injured}</div>}
				
				<div className="date">{this.props.date}</div>

				{this.state.open && this.props.shahed && (
					<div className="statement">
						<div className="source">
							<h5>Source</h5>
							<a href={this.props.feature.properties.url} target="_blank"><span>{this.props.feature.properties.source}</span> <span className="arrow">↗</span></a>
						</div>

						<div>{this.props.feature.properties.note}</div>
					</div>
				)}
				{this.state.open && this.props.mod_statement && (
					<div className="statement">
						<h5>MoD Statement</h5>
						<div dangerouslySetInnerHTML={{__html: this.props.mod_statement}}></div>
					</div>
				)}
				
				{this.state.open && this.props.isStrikeRow && (

					<div className="additional-info">
						<div>
							<div className="label">{this.props.ui.strike_details}:</div>
							<div className="lat-lng">{this.props.feature.properties.latitude}, {this.props.feature.properties.longitude}</div>
							{this.props.feature.location_name && <div className="location">{this.props.feature.properties.location_name}, {this.props.feature.properties.location_name_arabic}</div>}							
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
				'single-source-claim': 'Single Source',
				'declared-strike': 'Declared',
				'contested-strike': 'Contested',
				'likely-strike': 'Likely'
			};

			// let row = (
			// 	<div className="incident" key={`${feature.properties.unique_reference_code}-${belligerent}-${idx}`}>
			// 		<div className="uniquid">
			// 			{props.permalink && <a style={{color: color}} target="blank" href={props.permalink.toString()}>{props.unique_reference_code}</a>}
			// 			{!props.permalink && <span style={{color: color}}>{props.unique_reference_code}</span>}
			// 		</div>
			// 		{/*<div className="belligerent">{belligerents}</div>*/}
			// 		<div className="reporting">{gradingLabel}</div>					
			// 		<div className="casualties">{civcas}</div>
			// 		<div className="injured">{injured}</div>
			// 		<div className="date">{date}</div>
			// 	</div>
			// );

			let row;
			let showBelligerents = false;
			let showGrading = true;
			let showInjured = true;
			let reference = null;
			let isStrikeRow = false;
			let expandable = false;
			let targetEls = false;
			let strikeStatus = false;
			let militantFatalities = false;
			let isShahed = false;
			if(this.props.postDataSlug === 'shahed-map'){
				isShahed = true
				expandable = true;
				showInjured = false;
				showGrading = false;
				color = '#00c0ae';
			} else if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictId === 73867){
				if (!feature.properties.civcas) {
					row = null;
				}
				showBelligerents = true;
				showInjured = false;
			} else if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){
				showInjured = false;
				showBelligerents = true;

			} else if(this.props.currentMap === 'militant-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') === -1){

				militantFatalities = props.militants_killed_min + '-' + props.militants_killed_max;
				if(props.militants_killed_min === props.militants_killed_max){
					militantFatalities = props.militants_killed_max;
				}

				if(props.permalink){
					reference = <a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a>;
				} else {
					reference = <span style={{color: '#999'}}>{this.props.ui.strike}</span>;
				}
				showInjured = false;
				civcas = undefined;
				// row = (
				// 	<div className="incident" key={feature.properties.unique_reference_code+belligerent}>
				// 		<div className="uniquid">{reference}</div>
				// 		<div className="reporting">{gradingLabel}</div>
				// 		<div className="casualties">{militantFatalities}</div>
				// 		<div className="date">{date}</div>
				// 	</div>

				// );
			} else if(this.props.currentMap === 'strike-locations'){
				let locationColor = Config.colors.declared;
				if(props.strike_status !== 'declared-strike'){
					locationColor = Config.colors.alleged;
				}
				showGrading = false;
				showInjured = false;
				strikeStatus = <div className="strike-status" style={{color: locationColor}}>{strikeStatuses[props.strike_status]}</div>
				
			} else if (this.props.currentMap === 'strike-target'){
				targetEls = [];
				if(props.targeted_belligerents){
					let targets = JSON.parse(props.targeted_belligerents);
					
					
					targets.forEach((target)=>{					
						const targetEl = <span key={`target_${target.slug}`} style={{ color: Config.colors[target.slug]}}>{target.name}</span>;
						targetEls.push(targetEl);
					});	
				}
				
				
				showGrading = false;
				showInjured = false;

			} else if (this.props.currentMap === 'strikes' || (this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1) && this.props.currentMap === 'militant-fatalities'){
				if(props.permalink){
					reference = <a style={{color: color}} target="blank" href={props.permalink}>{props.unique_reference_code}</a>;
				} else {
					isStrikeRow = true;
					reference = <span style={{color: '#999'}}>{this.props.ui.strike}</span>;
				}
				expandable = true;
				showBelligerents = true;
				showInjured = false;
				showGrading = true;

			}	

			if (feature.properties.mod_statement) {
				expandable = true;
			}



			row = <IncidentRow 
				feature={feature}
				expandable={expandable}
				ui={this.props.ui}
				strikeStatus={strikeStatus}
				key={feature.properties.unique_reference_code + '_' + idx + '_' + belligerent}
				color={color}
				targetEls={targetEls}
				showGrading={showGrading}
				showInjured={showInjured}
				showBelligerents={showBelligerents}
				belligerents={belligerents}
				isStrikeRow={isStrikeRow}
				grading={gradingLabel}
				reference={reference}
				permalink={props.permalink}
				postDataSlug={this.props.postDataSlug}
				unique_reference_code={feature.properties.unique_reference_code}				
				civcas={civcas}
				injured={injured}
				date={date}
				militantFatalities={militantFatalities}
				totalAirstrikes={props.total_airstrikes}
				mod_statement={feature.properties.mod_statement}
				shahed={isShahed}
			/>;

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
			<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
			<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
			<div key="injured" className="injured">injured</div>,
			<div key="date" className="date">{this.props.ui.heading_date}</div>
		];


		if(this.props.postDataSlug === 'shahed-map'){


			columnHeadings = [		
				<div key="expand"></div>,					
				<div key="uniquid" className="uniquid">code</div>,
				<div key="reporting" className="location">location</div>,
				<div key="accuracy" className="accuracy">location<br/>accuracy</div>,
				<div key="casualties" className="launched">Shahed<br/>launched</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>,

			];

		} else if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictId === 41464){
			let headingCode = this.props.ui.heading_code
			if(this.props.postDataSlug == 'british-ekia'){
				headingCode = 'munition'
			}

			columnHeadings = [								
				<div key="uniquid" className="uniquid">{headingCode}</div>,
				<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
				<div key="injured" className="injured">injured</div>,
				<div key="date" className="date">{this.props.ui.heading_date}</div>,

			];

			if(this.props.postDataSlug == 'british-ekia'){
				columnHeadings.unshift(<div key="expand"></div>)
			}
		} else if(this.props.currentMap === 'civilian-fatalities' && this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){

			columnHeadings = [
				<div key="uniquid" className="uniquid">{this.props.ui.heading_code}</div>,
				<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
				<div key="belligerent" className="belligerent">belligerents</div>,
				<div key="casualties" className="casualties">{this.props.ui.heading_min_max_civilian_deaths}</div>,
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
				<div key="reporting" className="reporting">{this.props.ui.heading_grading}</div>,
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
