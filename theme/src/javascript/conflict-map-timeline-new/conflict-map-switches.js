import React from 'react';
import Config from '../config/config';

export default class ConflictMapSwitches extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		let mapSwitches = null;
		
		let descriptions = {
			'civilian-fatalities': this.props.ui.map_desc_civilian_fatalities,
			'militant-fatalities': this.props.ui.map_desc_militant_fatalities,
			'strike-locations': this.props.ui.map_desc_strike_locations,
			'strike-target': this.props.ui.map_desc_strike_target,
			'belligerent': this.props.ui.map_desc_belligerent,
			'strikes': false
		};
	
		let belligerentColor;
		let switches;

		if(this.props.conflictslug.indexOf('us-forces-in-somalia') !== -1){
			belligerentColor = Config.colors['us-forces-in-somalia'];
			switches = (
				<div className="map-switches">
					<div style={{backgroundColor: (this.props.currentMap === 'civilian-fatalities') ? belligerentColor : '#FFF'}} className={(this.props.currentMap === 'civilian-fatalities') ? 'civilian-fatalities option active' : 'option'} onClick={()=>this.props.onMapChange('civilian-fatalities')}>{this.props.ui.map_switch_civilian_fatalities}</div>
					<div style={{backgroundColor: (this.props.currentMap === 'militant-fatalities') ? Config.colors['militants'] : '#FFF'}} className={(this.props.currentMap === 'militant-fatalities') ? 'militant-fatalities option active' : 'militant-fatalities option'} onClick={()=>this.props.onMapChange('militant-fatalities')}>{this.props.ui.map_switch_militant_fatalities}</div>
					<div className={(this.props.currentMap === 'strike-locations') ? 'strike-locations option active' : 'strike-locations option'} onClick={()=>this.props.onMapChange('strike-locations')}>{this.props.ui.map_switch_strike_locations}</div>
					<div className={(this.props.currentMap === 'strike-target') ? 'strike-target option active' : 'strike-target option'} onClick={()=>this.props.onMapChange('strike-target')}>{this.props.ui.map_switch_strike_target}</div>				</div>
			);
		} else if(this.props.conflictslug.indexOf('us-forces-in-yemen') !== -1){
			belligerentColor = Config.colors['us-forces-yemen'];
			switches = (
				<div className="map-switches">
					<div style={{backgroundColor: (this.props.currentMap === 'civilian-fatalities') ? belligerentColor : '#FFF'}} className={(this.props.currentMap === 'civilian-fatalities') ? 'civilian-fatalities option active' : 'option'} onClick={()=>this.props.onMapChange('civilian-fatalities')}>{this.props.ui.map_switch_civilian_fatalities}</div>
					<div style={{backgroundColor: (this.props.currentMap === 'militant-fatalities') ? Config.colors['militants'] : '#FFF'}} className={(this.props.currentMap === 'militant-fatalities') ? 'militant-fatalities option active' : 'militant-fatalities option'} onClick={()=>this.props.onMapChange('militant-fatalities')}>{this.props.ui.map_switch_militant_fatalities}</div>
					<div className={(this.props.currentMap === 'strike-locations') ? 'strike-locations option active' : 'strike-locations option'} onClick={()=>this.props.onMapChange('strike-locations')}>{this.props.ui.map_switch_strike_locations}</div>
					<div className={(this.props.currentMap === 'strike-target') ? 'strike-target option active' : 'strike-target option'} onClick={()=>this.props.onMapChange('strike-target')}>{this.props.ui.map_switch_strike_target}</div>
				</div>
			);
		} else if(this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){
			belligerentColor = Config.colors['all-belligerents-in-libya'];
			if(this.props.conflictslug.indexOf('2011')){
				belligerentColor = Config.colors['all-belligerents-in-libya-2011'];
			}
			
			switches = (
				<div className="map-switches">
					<div style={{backgroundColor: (this.props.currentMap === 'civilian-fatalities') ? belligerentColor : '#FFF'}} className={(this.props.currentMap === 'civilian-fatalities') ? 'civilian-fatalities option active' : 'option'} onClick={()=>this.props.onMapChange('civilian-fatalities')}>{this.props.ui.map_switch_civilian_fatalities}</div>
					<div className={(this.props.currentMap === 'strikes') ? 'strikes option active' : 'strikes option'} onClick={()=>this.props.onMapChange('strikes')}>{this.props.ui.map_switch_strikes}</div>
				</div>
			);
		}

		let description;
		if(descriptions[this.props.currentMap]){
			description = <p>{descriptions[this.props.currentMap]}</p>;	
		}
		
		mapSwitches = (
			<div>
				{description}
				{switches}			
			</div>

		);
	
		return mapSwitches;
	}
}