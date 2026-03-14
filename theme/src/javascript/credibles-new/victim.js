import React from 'react';

export default function Victim(props) {

	let victim = props.victim;
	let classes = ['victim'];
	let victimImage = null;
	let gender, age;
	if(victim.victim_image){
		classes.push('has-image');
		//victimImage = victim.victim_image.sizes.medium.replace('localhost', 'org');
		victimImage = <div className="image" style={{backgroundImage: 'url('+victim.victim_image.sizes.medium.replace('localhost', 'org')+')'}}></div>;
	}

	
	


	if(victim.victim_gender !== ''){
		gender = <span>{victim.victim_gender}</span>;

	}
	if(victim.victim_age !== ''){
		if(victim.victim_exact_age !== ''){
			age = <span>{victim.victim_exact_age} years old</span>;	
		} else {
			age = <span>{victim.victim_age.label}</span>;
		}
		
	}
	return (
		<div style={{backgroundImage: 'url('+victimImage+')'}} className={classes.join(' ')}>
			
			<div className="text-content">
				<div className="name">{victim.victim_name}</div>
				<div className="labels">
					{gender}
					{age}
				</div>
			</div>
			{victimImage}
		</div>
	);
}
