import React, { Component } from 'react';
import { setRTLTextPlugin, getRTLTextPluginStatus } from 'mapbox-gl';
// import Config from '../config/config';
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token';

import { ZoomControl, RotationControl } from 'react-mapbox-gl';

import ReactMapboxGl from 'react-mapbox-gl';
import { Layer } from 'react-mapbox-gl';
import { Source } from 'react-mapbox-gl';
import moment from 'moment';


const mapConfig = {
	accessToken: MAPBOX_ACCESS_TOKEN,
	dragRotate: true,
	attributionControl: false,
	zoomControl: true,
	rotationControl: true,
	minZoom: 11.5,
	// maxZoom: 4.3
};



let Map = ReactMapboxGl(mapConfig);


function getKilledInjuredNumbers(features) {

	// const killedInjured = {
	// 	killed_min: 0,
	// 	killed_max: 0,
	// 	injured_min: 0,
	// 	injured_max: 0,
	// };

	// features.forEach(feature => {
	// 	if (feature.properties.incidents_included) {
	// 		feature.properties.incidents_included.forEach(incident) {
	// 			killedInjured.killed_min += incident.civilians_killed_min
	// 			killedInjured.killed_max += incident.civilians_killed_max
	// 			killedInjured.injured_min += incident.civilians_injured_min
	// 			killedInjured.injured_max += incident.civilians_injured_max

	// 		}
		
	// 	}
	// });

}
class Tooltip extends Component {
	constructor(props){
		super(props);
		this.handleMouseMove = this.handleMouseMove.bind(this);

		this.state = {
			mouseEvent: {offsetY: 0, offsetX: 0},
		};

		if(getRTLTextPluginStatus() === 'unavailable'){
			setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js');		
		}
	

	}
	componentDidMount(){
		window.addEventListener('mousemove', this.handleMouseMove);
	}
	componentWillUnmount(){
		window.removeEventListener('mousemove', this.handleMouseMove);
	}
	handleMouseMove(e){
		this.setState({
			mouseEvent: e
		});
	}

	
	render() {
		moment.locale('en');
		


		let position = {
			display: this.props.tooltipDisplay,
			top: this.state.mouseEvent.offsetY + 'px',
			left: this.state.mouseEvent.offsetX + 'px'
		};


		const tooltipClasses = ['neighbourhood-tooltip'];

		const sentence = <CasualtySentence uiTerms={this.props.uiTerms} features={this.props.features}/>;
		const neighbourhoodName = <NeighbourhoodName features={this.props.features}/>;
		let instructions = (
			<div className="tooltip__instructions">
				<span><i className="fad fa-hand-pointer"></i>&nbsp; {this.props.uiTerms.click_for_more_information}</span>
			</div>
		);
		
		if(sentence === null){
			instructions = '';
		}

		return (
			<div className={tooltipClasses.join(' ')} style={position}>
				
				<div>
					<label>{this.props.uiTerms.neighbourhood}</label>
					{neighbourhoodName}
				</div>
				<div>
					{sentence}
				</div>
				{instructions}

			</div>
		);
	}
}

function NeighbourhoodName(props){
	let nameEN = '';
	let nameAR = '';
	let join = '';
	if(props.features && props.features.length > 0){

		if(props.features[0].properties['Name_ENGLI'] !== 'null'){		
			nameEN = <span>{props.features[0].properties['Name_ENGLI']}</span>;	
			join = ', ';
		}
		if(props.features[0].properties['Name_ARABI'] !== 'null'){
			nameAR = <span>{props.features[0].properties['Name_ARABI']}</span>;
		}

	}
	
	return (
		<div className="neighbourhood-name">
			{nameEN}
			{join}
			{nameAR}
		</div>
	);
}

function CasualtySentence(props){
	let description = null;

	if(props.features){
		if(props.features[0].properties.incidents_included){
			const incidents = JSON.parse(props.features[0].properties.incidents_included);
		
			let amount_min = 0;
			let amount_max = 0;
			let amount;
			incidents.forEach(incident=>{
				amount_min += incident.civilians_killed_min;
				amount_max += incident.civilians_killed_max;
			});
			
			if(amount_min !== amount_max){
				amount = amount_min+'–'+amount_max;
			} else {
				amount = amount_min;
			}

			let injured_amount_min = 0;
			let injured_amount_max = 0;
			let injured_amount;
			incidents.forEach(incident=>{
				injured_amount_min += incident.civilians_injured_min;
				injured_amount_max += incident.civilians_injured_max;
			});

			if(injured_amount_min !== injured_amount_max){
				injured_amount = injured_amount_min+'–'+injured_amount_max;
			} else {
				injured_amount = injured_amount_min;
			}


			const mulitipleCivilians = (amount > 1 || amount_min !== amount_max || amount === 0);
			const multipleIncidents = (incidents.length > 1 || incidents.length === 0);


			// description = amount+' '+civilianLabel+' killed and '+injured_amount+' injured in '+incidents.length+' '+incidentLabel;

			let civiliansKilledInjuredSentence;
			if (!mulitipleCivilians && !multipleIncidents) {
				civiliansKilledInjuredSentence = props.uiTerms.civilian_killed_injured_incident;
			} else if (!mulitipleCivilians && multipleIncidents) {
				civiliansKilledInjuredSentence = props.uiTerms.civilian_killed_injured_incidents;
			} else if (mulitipleCivilians && !multipleIncidents) {
				civiliansKilledInjuredSentence = props.uiTerms.civilians_killed_injured_incident;
			} else if (mulitipleCivilians && multipleIncidents) {
				civiliansKilledInjuredSentence = props.uiTerms.civilians_killed_injured_incidents;
			}

			civiliansKilledInjuredSentence = civiliansKilledInjuredSentence.replace('{num_killed}', '<span class="amount">' + amount + '</span>');
			civiliansKilledInjuredSentence = civiliansKilledInjuredSentence.replace('{num_injured}', '<span class="amount">' + injured_amount + '</span>');
			civiliansKilledInjuredSentence = civiliansKilledInjuredSentence.replace('{num_incidents}', '<span class="amount">' + incidents.length + '</span>');

			description = <div dangerouslySetInnerHTML={{__html: civiliansKilledInjuredSentence}} />;
			
			// description = (
			// 	<div>
			// 		<span className="amount">{amount}</span> {civilianLabel} killed and <span className="amount">{injured_amount}</span> injured in {incidents.length} {incidentLabel}
			// 	</div>
			// );
		}
	}

	return (
		description
	);
}

function NeighbourhoodStats(props){

	let injured_amount = 0;
	let amount = 0;
	let incidents = [];
	if(props.features){
		if(props.features[0].properties.incidents_included){
			incidents = JSON.parse(props.features[0].properties.incidents_included);
		
			let amount_min = 0;
			let amount_max = 0;
			incidents.forEach(incident=>{
				amount_min += incident.civilians_killed_min;
				amount_max += incident.civilians_killed_max;
			});
			
			if(amount_min !== amount_max){
				amount = amount_min+'–'+amount_max;
			} else {
				amount = amount_min;
			}

			let injured_amount_min = 0;
			let injured_amount_max = 0;
			incidents.forEach(incident=>{
				injured_amount_min += incident.civilians_injured_min;
				injured_amount_max += incident.civilians_injured_max;
			});

			if(injured_amount_min !== injured_amount_max){
				injured_amount = injured_amount_min+'–'+injured_amount_max;
			} else {
				injured_amount = injured_amount_min;
			}

		}

	}

	return (
		<div className="sidebar-stats">
			<div>				
				{amount}
				<label>{props.uiTerms.killed}</label>
			</div>
			<div>				
				{injured_amount}
				<label>{props.uiTerms.injured}</label>
			</div>
			<div>				
				{incidents.length}
				{incidents.length === 1 && <label>{props.uiTerms.incident}</label>}
				{incidents.length > 1 && <label>{props.uiTerms.incidents}</label>}
				{incidents.length === 0 && <label>{props.uiTerms.incidents}</label>}
			</div>
		</div>
	);
}

class Sidebar extends Component {
	constructor(props){
		super(props);
		this.state = {
			currentSlide: 0,
		};

		this.handleNextSlide = this.handleNextSlide.bind(this);
		this.handlePrevSlide = this.handlePrevSlide.bind(this);

	}

	handleNextSlide() {
		let nextSlide = this.state.currentSlide+1;
		let numSlides = JSON.parse(this.props.features[0].properties.images).length;
		if (nextSlide > numSlides-1) {
			nextSlide = 0;
		}
		this.setState({
			currentSlide: nextSlide,
		});
	}


	handlePrevSlide() {
		let nextSlide = this.state.currentSlide-1;
		let numSlides = JSON.parse(this.props.features[0].properties.images).length;
		if (nextSlide < 0) {
			nextSlide = numSlides - 1;
		}
		this.setState({
			currentSlide: nextSlide,
		});
	}

	render(){
		const sidebarClasses = [];


		let incidentsRows = [];
		let images = [];
		let date = <h1 className="date">{this.props.uiTerms.gaza_may_10_20_2021}</h1>;
		let location = this.props.uiTerms.gaza;

		let introduction = null;

		if (this.props.lang === 'en') {
			introduction = (
				<div>
					<p>This interactive map illustrates the devastating civilian toll of the May 2021 war in Gaza. </p>
					<p>Our research found at least 151 and as many as 192 civilians in Gaza were likely killed by Israeli military actions. </p>
					<p>A further 15 to 20 civilians were killed as a result of Palestinian militant rockets falling short, whose deaths are also included in this map.  In Israel, 10 civilians were killed directly by rockets fired by Palestinian militants.</p>
					<p>Click on neighbourhoods to navigate our database of 128 individual assessments of civilian harm incidents in Gaza. The incidents are grouped by area: the higher the neighbourhood, the greater the number of civilians reported killed. Use Right Click to rotate the map.</p>
				</div>
			);
		} else if (this.props.lang === 'ar') {
			introduction = (
				<div>
					<p>توضح هذه الخريطة التفاعلية الخسائر المدنية الكبيرة في حرب مايو / أيار ٢٠٢١ في غزة</p>
					<p>خلال البحث الذي قمنا به وجدنا أن ١٥١ على الأقل وما يصل إلى ١٩٢ مدنياً قتلوا على الأرجح بسبب العمليات العسكرية الإسرائيلية على قطاع غزة.</p>
					<p>قُتل ما بين ١٥ إلى ٢٠ مدنياً آخر بسبب فشل في إطلاق صواريخ الفصائل الفلسطينية ، وتم أدراجهم في هذه الخريطة. في إسرائيل ، قُتل ١٠ مدنيين بشكل مباشر بصواريخ أطلقتها الفصائل الفلسطينية.</p>
					<p>انقر فوق الأحياء للتنقل في قاعدة بياناتنا المكونة من ١٢٨ تقييمًا فرديًا للحوادث التي الحقت الضرر بالمدنيين في قطاع غزة. يتم فرز الحوادث حسب المنطقة: فكلما ارتفع مؤشر الطبقات على الحي ، هذا يعني أنه زاد عدد الضحايا المدنيين المبلغ عنهم. استخدم "النقر بزر الفأرة الأيمن" لتدوير الخريطة.</p>
				</div>
			);
			
		} else if (this.props.lang === 'he') {
			introduction = (
				<div>
					<p>מפה אינטראקטיבית זו ממחישה את המחיר האזרחי הכבד של מלחמת מאי 2021 בעזה.</p>
					<p>המחקר שלנו מצא שמספר האזרחים שנהרגו בעזה עומד על 151 לכל הפחות ועד 192 כתוצאה מפעולות צבאיות ישראליות.</p>
					<p>בין 15 ל-20 אזרחים נוספים נהרגו כתוצאה משיגור רקטות כושל של קבוצות מיליטנטיות פלסטיניות, שגם מותם נכלל במפה זו. בישראל עשרה אזרחים נהרגו מירי ישיר של רקטות מידי פלגים חמושים פלסטינים .</p>
					<p>לחץ על השכונות כדי לנווט במאגר הנתונים שלנו הכולל 128 הערכות פרטניות של אירועי פגיעה באזרחים בעזה. האירועים מקובצים לפי אזורים: ככל שהשכונה גבוהה יותר, כך גדל מספר האזרחים שדווח כי נפגעו. השתמש בלחיצה ימנית כדי לסובב את המפה.</p>
				</div>
			);
		}		

		if(this.props.features){
			sidebarClasses.push('populated');

			location = <NeighbourhoodName features={this.props.features}/>;
			date = '';
			introduction = '';
			if(this.props.features[0].properties.images){
				images = JSON.parse(this.props.features[0].properties.images);
			}
			if(this.props.features[0].properties.incidents_included){
				const incidents = JSON.parse(this.props.features[0].properties.incidents_included);

				incidents.forEach((props, i)=>{

					let civcas = props.civilians_killed_min + '-' + props.civilians_killed_max;
					if(props.civilians_killed_max === props.civilians_killed_min){
						civcas = props.civilians_killed_max;
					}

					let injured = props.civilians_injured_min + '-' + props.civilians_injured_max;
					if(props.civilians_injured_max === props.civilians_injured_min){
						injured = props.civilians_injured_max;
					}		

					let date = moment(props.date).format('MMM. DD, YYYY');
					const dateParts = props.date.split(' ');
					let jsDate = false;
					if (dateParts.length > 0) {
						const jsDateStr = dateParts[0];
						jsDate = new Date(jsDateStr);
					}

					
					const options = {
						year: '2-digit',
						month: 'short',
						day: 'numeric',
					};
					if(this.props.lang === 'ar'){
						date = (jsDate) ? jsDate.toLocaleDateString('ar-EG', options) : '-';
					} else if (this.props.lang === 'he'){
						date = (jsDate) ? jsDate.toLocaleDateString('he-IL', options) : '-';
					}
					incidentsRows.push(
						<a target="blank" href={props.permalink} className="incident" key={`incident-${i}`}>
							<div className="date">{date}</div>
							<div className={`${props.grading} reporting`}>{this.props.uiTerms['grading_' + props.grading.toLowerCase()]}</div>
							
							<div className="casualties">{civcas}</div>
							<div className="injured">{injured}</div>
							<div className="uniquid">{props.unique_reference_code} &rarr;</div>
						</a>
					);
				});
			}
		}
		

		// const slideshow = [];
		// images.forEach((image, i)=>{
		// 	if(i < 15){

		// 		slideshow.push(
		// 		);
		// 	}
		// });


		let currentImage = null;
		let caption = null;
		if (images && images.length > 0) {
			const classes = ['slide'];
			if(images[this.state.currentSlide].portrait){
				classes.push('portrait');
			}
			caption = images[this.state.currentSlide].caption;
			currentImage = (

				<div className={classes.join(' ')} key={`slide-${this.state.currentSlide}`}>
					<img src={images[this.state.currentSlide].url}/>
				</div>
			);
		}
		let slideshowContainer;
		let stats;
		let incidentTable;

	
		if(currentImage){
			slideshowContainer = (
				<div className="slideshow">
					<div className="slideshow__slides">
						<div onClick={this.handlePrevSlide} className="slideshow__panel prev"></div>
						<div  onClick={this.handleNextSlide} className="slideshow__panel next"></div>
						{currentImage}
					</div>
					<div className="slideshow__controls">
						<div className="prev" onClick={this.handlePrevSlide}><i className="fal fa-long-arrow-left"></i></div>
						<div className="">{this.state.currentSlide+1} / {images.length}</div>
						<div className="next" onClick={this.handleNextSlide}><i className="fal fa-long-arrow-right"></i></div>


					</div>
					<div className="slideshow__caption">{caption}</div>
				</div>
			);	
		}
		if(this.props.features){
			stats = <NeighbourhoodStats features={this.props.features} uiTerms={this.props.uiTerms} />;
			incidentTable = (
				<div id="selected-features">
					<div className="sort">
						<div className="column-headers incident">
							<div className="date">{this.props.uiTerms.heading_date}</div>
							<div className="reporting">{this.props.uiTerms.heading_grading} <a href="/about/methodology/" target="_blank"><i className="far fa-info-circle" aria-hidden="true"></i></a></div>							
							<div className="casualties">{this.props.uiTerms.heading_min_max_civilian_deaths}</div>
							<div className="injured">{this.props.uiTerms.heading_min_max_civilians_injured}</div>
							<div className="uniquid">{this.props.uiTerms.heading_code}</div>
						</div>
					</div>
					<div className="incidents">						
						{incidentsRows}
					</div>
				</div>
			);
		}



		return (
			<div className={sidebarClasses.join(' ')} id="gaza-sidebar" onMouseEnter={this.props.onPointerEnter} onMouseLeave={this.props.onPointerLeave}>				
				<div className="sidebar__close" onClick={this.props.onClose}>×</div>
				<div className="sidebar__title">
					<h1>{this.props.uiTerms.civilian_casualties_in} {location}</h1>
					{date}

					<LanguageNav lang={this.props.lang} />
					{introduction}
				</div>
				
				{stats}			
				{slideshowContainer}
				{incidentTable}

				
			</div>
		);
	}
}

function LanguageNav(props) {

	const embedded = (window.location.href.indexOf('embedded') >= 0);
	return (
		<div className="langswitcher">
			<a className={(props.lang === 'en') ? 'active' : ''} href={`/conflict-data/civilian-casualties-gaza-may-2021-map/${(embedded) ? '?embedded=1' : ''}`}>English</a>
			<a className={(props.lang === 'ar') ? 'active' : ''} href={`/conflict-data/civilian-casualties-gaza-may-2021-map/?lang=ar${(embedded) ? '&embedded=1' : ''}`}>عربي</a>
			<a className={(props.lang === 'he') ? 'active' : ''} href={`/conflict-data/civilian-casualties-gaza-may-2021-map/?lang=he${(embedded) ? '&embedded=1' : ''}`}>עִברִית</a>
		</div>
	);
}

class HexMap extends Component {

	constructor(props){
		super(props);


		if(getRTLTextPluginStatus() === 'unavailable'){
			setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js');		
		}
		this.opts = {
			polySelected: false
		};


		let center = [34.3330,31.3558];
		if(this.props.lang === 'ar'){
			center = [34.4330,31.3558];
		}
		this.state = {
			zoom: [11.6384],
			center: center,
			pitch: [51.50],
			bearing: [0],
		};

		this.polyMouseDown = this.polyMouseDown.bind(this);
		this.polyMouseMove = this.polyMouseMove.bind(this);
		this.polyMouseLeave = this.polyMouseLeave.bind(this);
		this.handleStyleLoaded = this.handleStyleLoaded.bind(this);
		this.mapMouseDown = this.mapMouseDown.bind(this);
		this.handleMoveEnd = this.handleMoveEnd.bind(this);
		this.handleZoomEnd = this.handleZoomEnd.bind(this);

	}
	
	polyMouseDown(e) {
		if (e.features.length > 0) {
			this.opts.polySelected = e.features;			
			this.props.onFeaturesSelected(e.features);
		} else {
			this.opts.polySelected = false;
			this.props.onFeaturesSelected(false);
		}
	}
	mapMouseDown(map, e){
		var features = map.queryRenderedFeatures(e.point);

		if(features.length === 0){


			this.opts.polySelected = false;

			this.props.onFeaturesSelected(false);
			this.props.onTooltipDisplayChange('none');
			if (this.opts.hoveredStateId) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'neighbourhood-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}
		}

	}
	polyMouseMove(e){
		
		if (e.features.length > 0) {
			
			this.props.onTooltipDisplayChange('block');
			
			if (this.opts.hoveredStateId !== undefined || this.opts.hoveredStateId !== null) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'neighbourhood-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}

			this.opts.hoveredStateId = e.features[0].id;
			
			
			this.state.mapBoxMap.setFeatureState(
				{ source: 'neighbourhood-source', id: this.opts.hoveredStateId },
				{ hover: true }

			);

			this.props.onFeaturesChange(e.features);

		}

	}
	handleZoomEnd(map){
	}
	polyMouseLeave(map){

		if(!this.opts.polySelected){
			if (this.opts.hoveredStateId !== undefined || this.opts.hoveredStateId !== null) {
				this.state.mapBoxMap.setFeatureState(
					{ source: 'neighbourhood-source', id: this.opts.hoveredStateId },
					{ hover: false }
				);
			}
			this.props.onTooltipDisplayChange('none');
			this.opts.hoveredStateId = null;
		}

	}
	
	handleStyleLoaded(map){

		this.setState({
			mapBoxMap: map,
		});
		this.props.onLoad();

	//	this.rotateCamera(0);
	}
	// rotateCamera(timestamp) {
	// // clamp the rotation between 0 -360 degrees
	// // Divide timestamp by 100 to slow rotation to ~10 degrees / sec
	// 	this.state.mapBoxMap.rotateTo((timestamp / 100) % 360, { duration: 0 });
	// // Request the next frame of the animation.
	// 	requestAnimationFrame(this.rotateCamera);
	// }

	handleMoveEnd(e) {

		const mapCenter = e.getCenter();
		const center = [mapCenter.lng, mapCenter.lat];

		this.setState({
			zoom: [e.getZoom()],
			center: center,
			pitch: [e.getPitch()],
			bearing: [e.getBearing()],
		});
	}

	render() {

		const sourceGeoJson = {
			'type': 'geojson',
			'data': this.props.data
		};		

		const hexSource = <Source id='neighbourhood-source' geoJsonSource={sourceGeoJson} />;

		// let cmax = 0;
		// this.props.data.features.forEach(feature=>{							
		// 	if(feature.properties.civilians_killed_min > cmax){
		// 		cmax = feature.properties.civilians_killed_min;
		// 	}
		// });

		let cmax = 50;

		const neighbourhoods = <Layer
			id='neighbourhoods'
			sourceId='neighbourhood-source'
			type='fill-extrusion'
			onMouseDown={this.polyMouseDown}
			onMouseMove={this.polyMouseMove}
			onMouseLeave={this.polyMouseLeave}
			paint={{
			

				'fill-extrusion-color': [
					'case',
					['boolean', ['feature-state', 'hover'], false],
					'white',
					[
						'interpolate', ['linear'],
						['get', 'num_casualties'],
						0, '#090d10',
						1, '#182834',					
						Math.floor(cmax/2), '#446c94',
						cmax, '#5aadff'
					]
				],
				'fill-extrusion-opacity': 0.85,
				'fill-extrusion-height': [
					'interpolate', ['linear'],
					['get', 'num_casualties'],
					0, 0,					
					cmax, 1600
				]
			}}
			before='gaza-neighbourhoods-labels-bi-li'
		>
		</Layer>;

		// const neighbourhoodLabels = <Layer
		// 	id='neighbourhoods-labels'
		// 	sourceId='neighbourhood-source'
		// 	type='symbol'
		// 	paint={{
		// 		'text-color': '#FFF'
		// 	}}
		// 	layout={{
				
		// 		'text-field': '{polygonLabel}',
		// 		'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
		// 		'text-size': 12,
		// 	}}
			
		// >
		// </Layer>;

		return (
			<Map			
				onStyleLoad={this.handleStyleLoaded}
				scrollZoom={false}		
				style={'mapbox://styles/anecdote101/ckrlsmvho8t0117nhzp7jnmek?fresh=true'}
				zoom={this.state.zoom}
				center={this.state.center}
				onZoomEnd={this.handleZoomEnd}
				onMoveEnd={this.handleMoveEnd}
				pitch={this.state.pitch}
				bearing={this.state.bearing}
				attributionControl={false}
				onMouseDown={this.mapMouseDown}
				rotationControl={true}
				containerStyle={{
					height: '100%',
					width: '100vw',
				}}>
				
				{hexSource}
				{neighbourhoods}

				<RotationControl className="map__control rotation" position="bottom-right"/>
				<ZoomControl className="map__control" position="bottom-right"/>
			</Map>
			
		);
	}
	
}




export default class NeighbourhoodMap extends Component {

	constructor(props){
		super(props);

		

		this.state = {
			loaded: false,
			tooltipDisplay: 'none',
			data: this.props.data,
			killed_injured: getKilledInjuredNumbers(this.props.data.features),
			start: false,
			showTooltip: true,
			sidebarOpen: true,
		};
		this.handleTooltipDisplayChange = this.handleTooltipDisplayChange.bind(this);
		this.handleFeaturesChange = this.handleFeaturesChange.bind(this);
		this.handleFeaturesSelected = this.handleFeaturesSelected.bind(this);
		this.handlePointerEnter = this.handlePointerEnter.bind(this);
		this.handlePointerLeave = this.handlePointerLeave.bind(this);
		this.startMap = this.startMap.bind(this);
		this.handleLoaded = this.handleLoaded.bind(this);
		this.handleSidebarClose = this.handleSidebarClose.bind(this);

	}

	componentDidMount() {
		// getKilledInjuredNumbers(this.props);
				
	}

	handleTooltipDisplayChange(display) {
		
		this.setState({
			tooltipDisplay: display,
		});	
		
	}

	handleFeaturesChange(features) {
		this.setState({
			features: features,
		});
	}

	handlePointerEnter() {

		this.setState({
			showTooltip: false,
		});
	}

	handlePointerLeave() {
		this.setState({
			showTooltip: true,
		});
	}
	
	handleFeaturesSelected(features) {

		let currentFeaturesKeys = ['features'];
		if (features) {
			features.forEach(feature => {
				currentFeaturesKeys.push(feature.id);
			});
		}	

		this.setState({
			selectedFeatures: features,
			selectedFeaturesKey: currentFeaturesKeys.join('_'),
			sidebarOpen: true,
		});		
	}
	
	startMap(){
		
	}

	handleLoaded() {
		this.setState({
			loaded: true,
		});
	}

	handleSidebarClose() {


		this.setState({
			sidebarOpen: false
		});
	}
	render() {
		let cmax = 68;
		const uiTerms = this.props.data.ui_terms;

		return (
			<div className="map-container">
				{!this.state.loaded && <div className="loading-container"></div>}
				{this.state.showTooltip && <Tooltip tooltipDisplay={this.state.tooltipDisplay} uiTerms={uiTerms} features={this.state.features} />}
				
				{this.state.sidebarOpen && (
					<Sidebar key={this.state.selectedFeaturesKey} 
						uiTerms={uiTerms} 
						lang={this.props.lang} 
						features={this.state.selectedFeatures} 
						onPointerEnter={this.handlePointerEnter} 
						onPointerLeave={this.handlePointerLeave} 
						onClose={this.handleSidebarClose}
					/>
				)}
				<div className="gaza__legend">
					<div className="legend__description">{uiTerms.neighbourhood_height_civcas_legend}</div>					
					<div>
						<img src="/wp-content/themes/airwars-new/media/gaza-legend.svg"/>
					</div>
					<div className="legend__range">
						<div>{cmax}</div>
						<div>0</div>
					</div>
				</div>
				<HexMap 
					lang={this.props.lang}
					slug={this.props.slug}
					data={this.state.data || {}} 
					onTooltipDisplayChange={this.handleTooltipDisplayChange} 
					onFeaturesChange={this.handleFeaturesChange} 
					onFeaturesSelected={this.handleFeaturesSelected} 
					onLoad={this.handleLoaded}
				/>
			</div>
		);
	}


}