import React from 'react';
import Config from '../config/config';

export default class CivcasMapSwitches extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		const somaliaConflictId = 59865;
		const libyaConflictId = 41467;
		let mapSwitches = null;

		let descriptions = {
			'civilian-fatalities': this.props.ui.map_desc_civilian_fatalities,
			'militant-fatalities': this.props.ui.map_desc_militant_fatalities,
			'strike-locations': this.props.ui.map_desc_strike_locations,
			'strike-target': this.props.ui.map_desc_strike_target,
			'belligerent': this.props.ui.map_desc_belligerent,
			'strikes': ''
		};


		if(this.props.country === 'yemen' || this.props.country === 'somalia') {
			let belligerentColor;
			if(this.props.country === 'somalia'){
				belligerentColor = Config.colors['us-forces'];
			} else if(this.props.country === 'yemen'){
				belligerentColor = Config.colors['us-forces-yemen'];
			} else if(this.props.country === 'libya'){
				belligerentColor = Config.colors['all-belligerents'];
			}
			mapSwitches = (
				<div>
					<p>{descriptions[this.props.currentMap]}</p>

					<div className="map-switches">
						<div style={{backgroundColor: (this.props.currentMap === 'civilian-fatalities') ? belligerentColor : '#FFF'}} className={(this.props.currentMap === 'civilian-fatalities') ? 'civilian-fatalities option active' : 'option'} onClick={()=>this.props.onMapChange('civilian-fatalities')}>{this.props.ui.map_switch_civilian_fatalities}</div>
						<div style={{backgroundColor: (this.props.currentMap === 'militant-fatalities') ? Config.colors['militants'] : '#FFF'}} className={(this.props.currentMap === 'militant-fatalities') ? 'militant-fatalities option active' : 'militant-fatalities option'} onClick={()=>this.props.onMapChange('militant-fatalities')}>{this.props.ui.map_switch_militant_fatalities}</div>
						<div className={(this.props.currentMap === 'strike-locations') ? 'strike-locations option active' : 'strike-locations option'} onClick={()=>this.props.onMapChange('strike-locations')}>{this.props.ui.map_switch_strike_locations}</div>
						<div className={(this.props.currentMap === 'strike-target') ? 'strike-target option active' : 'strike-target option'} onClick={()=>this.props.onMapChange('strike-target')}>{this.props.ui.map_switch_strike_target}</div>

					</div>
				</div>

			);
	

		} 
		// else if (this.props.conflictId === libyaConflictId){
		// 	mapSwitches = (
		// 		<div>
		// 			<p>{descriptions[this.props.currentMap]}</p>

		// 			<div className="map-switches">
		// 				<div style={{backgroundColor: (this.props.currentMap === 'civilian-fatalities') ? Config.colors['us-forces'] : '#FFF'}} className={(this.props.currentMap === 'civilian-fatalities') ? 'civilian-fatalities option active' : 'option'} onClick={()=>this.props.onMapChange('civilian-fatalities')}>Civilian Fatalities</div>						
		// 				<div className={(this.props.currentMap === 'belligerent') ? 'belligerent option active' : 'option'} onClick={()=>this.props.onMapChange('belligerent')}>Belligerent</div>
		// 			</div>
		// 		</div>

		// 	);
		// }

		return mapSwitches;
	}
}