import React from 'react';

export default class CivcasMapBox extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			mouseX: 0,
			mouseY: 0,
			visible: false,
			showZoomMessage: false,
		};

		this.handleMouseMove = this.handleMouseMove.bind(this);
		this.handleMouseWheel = this.handleMouseWheel.bind(this);
	}

	componentDidMount() {
		window.addEventListener('mousemove', this.handleMouseMove);	
		document.addEventListener('mousewheel', this.handleMouseWheel);
		document.addEventListener('DOMMouseScroll', this.handleMouseWheel);
	}

	componentWillUnMount() {
		window.removeEventListener('mousemove', this.handleMouseMove);
		document.removeEventListener('mousewheel', this.handleMouseWheel);
		document.removeEventListener('DOMMouseScroll', this.handleMouseWheel);
	}



	render(){
		const classes = [];
		let labelText = '';


		if(!this.props.scrollZoomEnabled){
			classes.push('note');
			labelText = 'Click to interact';
		} else if(this.props.featureNumber > 0){
			labelText = this.props.featureNumber + ' incidents';
		}


		if (!this.state.visible) {
			return null;
		}

		return (
			<div id="area" className={classes.join(' ')} style={{ top: this.state.mouseY, left: this.state.mouseX }}>
				<div className="label">{labelText}</div>
			</div>
		);
	}

	handleMouseMove(e) {

		const mapContainer = document.querySelector('.map-container');
		const mapTop = mapContainer.getBoundingClientRect().top || mapContainer.getBoundingClientRect().y;
		const mapBot = mapTop + mapContainer.getBoundingClientRect().height;


		if (e.clientY >= mapTop && e.clientY <= mapBot && !e.target.closest('.map-controls-sidebar')) {
			this.setState({
				mouseX: e.clientX,
				mouseY: e.clientY - mapTop,
				visible: true,
			});
		} else {
			this.setState({
				visible: false,
			});
		}
	}

	handleMouseWheel(e) {

		// if (!this.props.scrollZoomEnabled) {
		// 	this.setState({
		// 		showZoomMessage: true,
		// 	});

		// 	setTimeout(() => {
		// 		this.setState({
		// 			showZoomMessage: false,
		// 		});
		// 	}, 3000);
		// }
	}
}
