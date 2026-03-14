import React from 'react';
import Victim from './victim';

export default function Victims(props) {
	if (!props.entry) {
		return null;
	}

	let civcasLabel = '';
	const victims = [];

	if(props.entry.victims.total > 0){
		civcasLabel = <h1>Civilian Casualties <span>from incident <span className="code"><a href="{entry.guid}">{props.entry.code}</a></span></span></h1>;

		if(props.entry.victims.individuals){
			props.entry.victims.individuals.forEach(function(individual, idx){
				let victim = <Victim key={idx} victim={individual}/>;
				victims.push(victim);
			});
		}
		if(props.entry.victims.groups){
			props.entry.victims.groups.forEach(function(group, idx){
				if(group.group_victims){
					let groupVictims = [];
					group.group_victims.forEach(function(individual, i){		
										
						let victim = <Victim key={idx+'-'+i} victim={individual}/>;
						//victims.push(victim);
						groupVictims.push(victim);
					});	
					victims.push(<div key={'family'+idx} className="family">
						<h4>Family members</h4>
						<div className="victims">{groupVictims}</div>
					</div>);
				}					
			});
		}

	}


	return (
		<div className="all-victim-container">
			{civcasLabel}
			{victims}
		</div>
	);
}