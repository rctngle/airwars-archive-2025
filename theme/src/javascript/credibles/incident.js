import React from 'react';

export default function Incident(props) {

	if (!props.entry) {
		return null;
	}

	let titleTokens = props.entry.post_title.split(' - ');
	let code = titleTokens[0];
	let date = titleTokens[1];

	let conceded;
	if(props.entry.civilian_deaths_conceded_min === props.entry.civilian_deaths_conceded_max){
		conceded = props.entry.civilian_deaths_conceded_max;
	} else {
		conceded = props.entry.civilian_deaths_conceded_min + '–' + props.entry.civilian_deaths_conceded_max;
	}

	let reported;
	if(props.entry.civilians_reported_killed_min === props.entry.civilians_reported_killed_max){
		reported = props.entry.civilians_reported_killed_max;
	} else {
		reported = props.entry.civilians_reported_killed_min + '–' + props.entry.civilians_reported_killed_max;
	}
	

	let named = 0;
	if (props.entry && props.entry.victims) {
		if(props.entry.victims.individuals){
			props.entry.victims.individuals.forEach(function(individual, idx){
				named++;
			});
		}
		if(props.entry.victims.groups){
			props.entry.victims.groups.forEach(function(group, idx){
				if(group.group_victims){
					group.group_victims.forEach(function(individual, i){		
						named++;
					});	
				}					
			});
		}
	}



	let civcasStatement = null;

	if (props.entry.civcas_statement) {
		let civcasStatementParagraph = <p>{props.entry.civcas_statement.statement}</p>;
		if(props.entry.civcas_statement.statement.length > 500){

			civcasStatementParagraph = <p>{props.entry.civcas_statement.statement.substring(0, 500)} <a href={props.entry.guid}>read more</a></p>;
		}
		civcasStatement = (
			<div className="description">
				<h2>Coalition Civialian Casualty Statement</h2>
				{civcasStatementParagraph}
			</div>
		);
	}

	let airwarsAssessment = null;
	if(props.entry.description){
		airwarsAssessment = (
			<div className="description airwars">
				<h2>Airwars Assessment</h2>
				<div dangerouslySetInnerHTML={{__html: props.entry.description}}></div>
			</div>
		);
	}


	return (
		<div className="incidents">
			<div className="inner">
				<div className="incident-header">
					<h1 className="code"><a target="blank" href={props.entry.guid}>{code}</a></h1>
					<h1 className="date">{date}</h1>
					<div className="close" onClick={props.onHome}><i className="fal fa-times"></i></div>
					<div className="location">{props.entry.location} </div>
					<div className="exact-location">
						<span>({props.entry.latlng.description}) </span>
						{props.entry.latlng.accuracy}
					</div>
				</div>
				<div className="totals incident">
					<div className="total has-tooltip">
						<div className="value">{conceded}</div>
						<div className="label">civilian deaths according to CJTFOIR</div>

					</div>
					<div className="total">
						<div className="value">{reported}</div>
						
						<div className="label">civilian deaths according to Airwars</div>
					</div>
					<div className="total">
						<div className="value">{named}</div>
						<div className="label">named civilian casualties</div>
					</div>
				</div>

				{civcasStatement}
				{airwarsAssessment}				
			</div>
		</div>
	);
}