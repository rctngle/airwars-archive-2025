import React from 'react';

export default function Controls(props) {
	return (
		<div className="timeline-controls">
			<div onClick={props.onPrevious}><i className="fal fa-angle-double-left"></i></div>
			<div onClick={props.onNext}><i className="fal fa-angle-double-right"></i></div>
		</div>
	);
}