import React from 'react';
import Slideshow from './slideshow';

export default function MapMeta(props) {

	let latlng = props.entry.latlng.description;
	let accuracy = props.entry.latlng.accuracy;

	let media = [];

	if(props.entry.ground_level_media){
		media = media.concat(props.entry.ground_level_media);
	}

	if(props.entry.geolocation_media){
		media = media.concat(props.entry.geolocation_media);
	}

	let slideshow = null;
	if (media.length > 0) {
		slideshow = (
			<div className="media-container">
				<Slideshow media={media} />
			</div>
		);
	}
	

	return (
		<div className="map-meta">
			<div className="lat-lng">
				<div className="value">({latlng})</div>
				<div className="location">{props.entry.location}</div>
				<div className="accuracy">{accuracy}</div>
			</div>
			{slideshow}
		</div>
	);
}