import React from 'react';
import Config from '../config/config';

export default class ConflictMapLegend extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		const classes = ['map-legend'];

		if(this.props.taxonomies.length === 0){
			classes.push('hidden');
		}
		let legendItems = [];

		this.props.taxonomies.forEach((item)=>{
			let backgroundColor = Config.colors[item.slug];
			if(this.props.conflictslug.indexOf('all-belligerents-in-libya') !== -1){
				backgroundColor = Config.colors.libya[item.slug];
			}			
			legendItems.push(
				<div key={'legend-item_'+item.slug}>
					<div key={item.slug} style={{backgroundColor: backgroundColor}} className="icon"></div>
					<div key={'label_'+item.slug} className="label">{item.name}</div>
				</div>
			);
		});
		//<div className="legend-label">{this.props.ui.legend} </div>
		return (
			<div className={classes.join(' ')}>
				
				<div className="legend-items">
					{legendItems}
				</div>
			</div>
		);
	}
	slugify(str){
		return str.replace(/ /g, '-').toLowerCase();
	}
}