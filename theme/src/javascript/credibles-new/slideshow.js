import React from 'react';

export default class Slideshow extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			idx: 0,
		};
	}

	componentDidMount() {
		const interval = setInterval(() => {
			
			let idx = this.state.idx + 1;
			if (idx > this.props.media.length - 1) {
				idx = 0;
			}
			
			this.setState({
				idx: idx,
			});

		}, 2000);

		this.setState({
			interval: interval,
		});

	}

	componentWillUnmount() {
		clearInterval(this.state.interval);
	}

	render() {
		
		return (
			<div className="media ground-level">
				<div className="counter">{this.state.idx+1}/{this.props.media.length}</div>
				<img src={this.props.media[this.state.idx].media_image.sizes.large.replace('localhost', 'org')} />				
				
			</div>	
		);
	}
}