import React from 'react';
import moment from 'moment';
import Slider, {Range} from 'rc-slider';
const Handle = Slider.Handle;

import Controls from './controls';

export default class Timeline extends React.Component {

	render() {
		moment.locale('en');

		const incidents = this.props.incidents;

		const timelineBars = [];
		let startDate;
		let endDate;
		let numDays = 0;
		let handleDate = null;


		if (incidents.length > 0) {
			let start = moment(incidents[0].post_date, 'YYYY-MM-DD');
			let end = moment(incidents[incidents.length-1].post_date, 'YYYY-MM-DD');
			startDate = start.format('MMMM DD, YYYY');
			endDate = end.format('MMMM DD, YYYY');
			numDays = end.diff(start,'days');

		}

		if (this.props.entry) {
			handleDate = <div className="handle-date">{this.props.entry.Date}</div>;
		}

		incidents.forEach((incident, idx) => {
			const percentage = (incident.civilian_deaths_conceded_max / 33) * 100;						
			let classes = ['day'];
			let overflow;
			if(idx == this.props.sliderValue){
				classes.push('shown');
			}

			incident.code = incident.post_title.split(' - ')[0];

			if (idx > 0 && idx < incidents.length - 1) {

				const prevIncident = incidents[idx-1];
				
				if (numDays >= 730) {
					const date = moment(incident.post_date, 'YYYY-MM-DD').format('YYYY');

					const prevDate = moment(prevIncident.post_date, 'YYYY-MM-DD').format('YYYY');
					if (prevDate !== date) {
						const dateFormatted = moment(incident.post_date, 'YYYY-MM-DD').format('YYYY');
						timelineBars.push(<div key={`time_marker_${incident.ID}`} className="time-marker"><span className={'date-'+dateFormatted}>{dateFormatted}</span></div>);
					}
				} else if (numDays >= 90) {
					// month markers
					const date = moment(incident.post_date, 'YYYY-MM-DD').format('MM');
					const prevDate = moment(prevIncident.post_date, 'YYYY-MM-DD').format('MM');
					if (prevDate !== date) {
						const dateFormatted = moment(incident.post_date, 'YYYY-MM-DD').format('MMM YY');
						timelineBars.push(<div key={`time_marker_${incident.ID}`} className="time-marker"><span>{dateFormatted}</span></div>);
					}
				} else {
					// day markers
					const date = moment(incident.post_date, 'YYYY-MM-DD').format('DD');
					const prevDate = moment(prevIncident.post_date, 'YYYY-MM-DD').format('DD');
					if (prevDate !== date) {
						const dateFormatted = moment(incident.post_date, 'YYYY-MM-DD').format('DD');
						timelineBars.push(<div key={`time_marker_${incident.ID}`} className="time-marker"><span>{dateFormatted}</span></div>);
					}
				}
			}
			timelineBars.push(<div className={classes.join(' ')} key={idx} style={{height: percentage+'%'}}>{overflow}</div>);

			
		});


		return (
			<div className="timeline-outer">
				<div className="timeline-controls-container">
					<div className="timeline-container">

						<div className="timeline-bars">
							<div className="grid-lines">
								<div className="bar"></div>
								<div className="bar"></div>
								<div className="bar"></div>
								<div className="bar"></div>
							</div>
							{timelineBars}
						</div>
						<Slider 
							step={1}
							min={0}
							max={incidents.length-1}
							value={this.props.sliderValue}
							onChange={this.props.onUISliderChange}
							handle={(props) => {
								const { value, dragging, index, ...restProps } = props;
								return (
									<Handle key={index} value={value} {...restProps}>
										<div className="triangle"></div>
										<div className="line"></div>
										{handleDate}
									</Handle>
								);						
							}} 
						/>

						<div className="start-end-labels">
							<div className="start date-label">{startDate}</div>
							<Controls onPrevious={this.props.onPrevious} onNext={this.props.onNext} />
							<div className="end date-label">{endDate}</div>
						</div>
					</div>
					
				</div>

			</div>
		);
	}
}