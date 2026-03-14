import React from 'react';

export default class ConflictMapBox extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			mouseX: 0,
			mouseY: 0,
			visible: false,
			showZoomMessage: false,
		};

		this.handleMouseMove = this.handleMouseMove.bind(this);
	}

	componentDidMount() {
		window.addEventListener('mousemove', this.handleMouseMove);	

	}

	componentWillUnMount() {
		window.removeEventListener('mousemove', this.handleMouseMove);
	}



	render(){
		const classes = [];
		let labelText = '';

		if (!this.state.visible) {
			return null;
		}

		if(!this.props.scrollZoomEnabled){
			classes.push('note');
			labelText = 'Click to interact';
		} else if(this.props.featureNumber > 0){
			labelText = this.props.featureNumber + ' incidents';
		}

		return (
			<div id="area" className={classes.join(' ')} style={{ top: this.state.mouseY, left: this.state.mouseX }}>
				<div className="label">{labelText}</div>
			</div>
		);
	}

	handleMouseMove(e) {


		const mapContainer = e.target.closest('.map-container');
		if (mapContainer) {
			const mapTop = mapContainer.getBoundingClientRect().top || mapContainer.getBoundingClientRect().y;
			const mapBot = mapTop + mapContainer.getBoundingClientRect().height;

			const mapLeft = mapContainer.getBoundingClientRect().left || mapContainer.getBoundingClientRect().x;
			const mapRight = mapLeft + mapContainer.getBoundingClientRect().width;

			if (e.clientY >= mapTop && e.clientY <= mapBot && e.clientX >= mapLeft && e.clientX <= mapRight && !e.target.closest('.map-controls-sidebar')) {
				this.setState({
					mouseX: e.clientX - mapLeft,
					mouseY: e.clientY - mapTop,
					visible: true,
				});
			} else {
				this.setState({
					visible: false,
				});
			}
		} else {
			this.setState({
				visible: false,
			});
		}
	}
}
