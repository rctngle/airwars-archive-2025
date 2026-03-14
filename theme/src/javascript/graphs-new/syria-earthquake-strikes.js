import React, { useState, useEffect, useCallback, useRef, Fragment } from 'react'
import ReactMapGL, { Source, Layer, NavigationControl } from 'react-map-gl'
import MAPBOX_ACCESS_TOKEN from '../config/mapbox-token'

//mapboxgl.workerClass = require('worker-loader!mapbox-gl/dist/mapbox-gl-csp-worker').default

export default props => {

	const { data } = props
	const { map_data } = data
	const sliderRef = useRef(null)
	const [value, setValue] = useState(96.732)
	const [sliderWidth, setSliderWidth] = useState(0)

	const timestamps = Object.keys(map_data.histogram)

	const [currentTime, setCurrentTime] = useState(parseInt(timestamps[timestamps.length - 1]))
	const [currentEntries, setCurrentEntries] = useState([])
	const [currentFocusedIds, setCurrentFocusedIds] = useState([])
	const [currentFilters, setCurrentFilters] = useState({})

	const [hoverId, setHoverId] = useState(null)
	const [cursor, setCursor] = useState('auto')
		
	const days = []

	const values = Object.values(map_data.histogram)

	const maxValue = Math.max(...values)

	timestamps.forEach((timestamp, idx)=>{
		days.push(<div key={'day-'+idx} style={{height: Math.round((map_data.histogram[timestamp] / maxValue) * 100) + '%'}}></div>)
	})

	useEffect(() => {
		const entries = []
		map_data.strikes.map((entry, index) => {

			let matchesFilters = true
			if (Object.keys(currentFilters).length > 0) {
				for (let filter in currentFilters) {
					if (entry[filter] !== currentFilters[filter]) {
						matchesFilters = false
					}
				}
			}


			if (entry.timestamp <= currentTime && matchesFilters) {
				entries.push(entry)
			}
		})
		
		setCurrentFocusedIds([])
		setCurrentEntries(entries)


	}, [currentTime, currentFilters])

	
	const onMouseEnter = useCallback((e) => {
		setCursor('pointer')

		if (e.features.length > 0) {

			if (e.features[0].layer.id === 'single-point') {
				setHoverId(e.features[0].id)
			}
		} else {
			setHoverId(null)
		}
	}, [])

	const onMouseLeave = useCallback(() => {
		setCursor('auto')
		setHoverId(null)
	}, [])

	const formattedDate = new Date(currentTime * 1000).toLocaleDateString('en-GB', {
		day: 'numeric',		// numeric day
		month: 'short',		// full month name
		year: 'numeric'	 // numeric year
	})
	// const onZoom = useCallback((viewport) => {
	// 	console.log(viewport.viewState)
	// })
	useEffect(() => {
		if (sliderRef.current) {
			const width = sliderRef.current.offsetWidth;
			setSliderWidth(width);
		}
	}, []);
	
	const handleSliderChange = (e) => {

		let newTimestamp = parseInt(e.target.value, 0)
		const minTimestamp = parseInt(sliderRef.current.min, 0)
		const maxTimestamp = parseInt(sliderRef.current.max, 0)
		const totalRange = maxTimestamp - minTimestamp

		let percentageOfRange = ((newTimestamp - minTimestamp) / totalRange) * 100
		const sliderPhysicalWidth = sliderWidth
		let pixelPosition = (percentageOfRange / 100) * sliderPhysicalWidth
		const handleWidth = 80
		const minOffsetPixels = handleWidth / 2
		const maxOffsetPixels = sliderPhysicalWidth - handleWidth / 2

		if (pixelPosition < minOffsetPixels) {
			pixelPosition = minOffsetPixels
			percentageOfRange = (pixelPosition / sliderPhysicalWidth) * 100
			newTimestamp = minTimestamp + (percentageOfRange / 100) * totalRange
		} else if (pixelPosition > maxOffsetPixels) {
			pixelPosition = maxOffsetPixels
			percentageOfRange = (pixelPosition / sliderPhysicalWidth) * 100
			newTimestamp = minTimestamp + (percentageOfRange / 100) * totalRange
		}

		//sliderRef.style.setProperty('--value', percentageOfRange);
		// e.style.setProperty('--min', e.min == '' ? '0' : e.min);
		// e.style.setProperty('--max', e.max == '' ? '100' : e.max);


		setCurrentTime(e.target.value); 
		setValue(percentageOfRange);
	}

	const handleSelectIncidentIds = incidentIds => {
		setCurrentFocusedIds(incidentIds)
	}

	const handleFilterChange = (filter, value) => {
		const filters = { ...currentFilters }
		if (value) {
			filters[filter] = value
		} else {
			delete filters[filter]
		}

		setCurrentTime(parseInt(timestamps[timestamps.length - 1]))
		setCurrentFilters(filters)

	}

	const handleClearFilters = () => {
		setCurrentTime(parseInt(timestamps[timestamps.length - 1]))
		setCurrentFilters({})		
	}

	return (
		<div className="earthquakemap">
			<div className="earthquakemap__left">
				<div className="earthquakemap__slider">
					<div className="earthquakemap__sliderlegend">
						<span className="">strikes</span>
						<div className="">46</div>
						<div className="">0</div>
					</div>
					<div className="earthquakemap__sliderinner">
						
						<div className="earthquakemap__histogram">

							<div className="earthquakemap__marker"><span><span className="system">&larr;</span> Earthquake on 6 Feb</span></div>
							{days}
						</div>
						<div className="earthquakemap__sliderelement">
							<input className="styled-slider slider-progress" value={currentTime} ref={sliderRef} style={{'--value': currentTime, '--min': timestamps[0], '--max': timestamps[timestamps.length-1]}} type="range" min={timestamps[0]} max={timestamps[timestamps.length-1]} onChange={handleSliderChange}/>							
						</div>
						<div className="earthquakemap__datemarker" style={{left: value + '%'}}>{formattedDate}</div>
						<div className="earthquakemap__months">								
							<div>Feb</div>
							<div>Mar</div>
							<div>Apr</div>
							<div>May</div>
							<div>Jun</div>
							<div>Jul</div>
							<div>Aug</div>
							<div>Sep</div>
							<div>Oct</div>
							<div>Nov</div>
							<div>Dec</div>
						</div>
					</div>

				</div>

				<div>
					<Filters currentFilters={currentFilters} onFilterChange={handleFilterChange} onClear={handleClearFilters} filters={map_data.filters} />
				</div>

				<div className="earthquakemap__map">
					<Map 
						entries={currentEntries}
						map_data={map_data}
						cursor={cursor}
						hoverId={hoverId}
						onMouseEnter={onMouseEnter}
						onMouseLeave={onMouseLeave}
						onSelectIncidentIds={handleSelectIncidentIds}
					/>
				</div>
			</div>
		
			<div className="earthquakemap__legend">
				<div className="earthquakemap__legendinner">
					<div className="earthquakemap__gradient"></div>
					<div className="earthquakemap__legendlabel"><div>3.8</div><div>8.2</div></div>
				</div>
				<div>Earthquake Intensity (MMI)<br/>on 6 February 2023</div>
				<div className="earthquakemap__legendsource">
					Source: M7.8 – Pazarcik earthquake, Kahramanmaras earthquake sequence <a href="https://earthquake.usgs.gov/earthquakes/eventpage/us6000jllz/shakemap/intensity" target="_blank">USGS</a>
				</div>
			</div>
			<Sidebar content={data.post_data.content} title={data.post_data.title} entries={currentEntries} currentFocusedIds={currentFocusedIds} onSelectIncidentIds={handleSelectIncidentIds} />
		</div>
	)
}

function formatDate(input) {

	const timestamp = input * 1000;
	const date = new Date(timestamp);

	const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
	const formattedDate = `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`
	return formattedDate
}

const Filters = props => {

	const { filters, currentFilters } = props
	const selects = []
	const [open, setOpen] = useState(false)
	const filterClasses = ['earthquakemap__filters']
	if(open){
		filterClasses.push('open')
	}
	for (let filter in filters) {
		const classes = ['earthquakemap__select']
		if(currentFilters[filter]){
			classes.push('active')
		}	
		
		selects.push((
			<div key={`filter_${filter}`} className={classes.join(' ')}>				
				<label>{filters[filter].label}</label>
				<select value={currentFilters[filter] ? currentFilters[filter] : ''} onChange={e => props.onFilterChange(filter, e.target.value)}>
					<option value="">&mdash;</option>
					{filters[filter].options.map(option => {
						return (
							<option key={`filter_${filter}_${option}`}>{option}</option>
						)
					})}
				</select>
				
			</div>
		))
	}

	return (
		<div className={filterClasses.join(' ')}>
			<label>filter for:</label>
			<div className="earthquakemap__filtertoggle" onClick={e => setOpen(!open)}><span></span> Filters</div>
			
			{selects}
			<div className="earthquakemap__clear"><button onClick={props.onClear}><span>Clear Filters</span> ×</button></div>
	
		</div>
	)

}

const Sidebar = props => {

	const { entries, currentFocusedIds } = props

	const [open, setOpen] = useState(false)
	const classes = ['earthquakemap__sidebar']
	if(open){
		classes.push('open')
	}

	return (
		<Fragment>
			
			<div className={classes.join(' ')}>
				<div className="earthquakemap__introduction">
					<h2>{props.title}</h2>
					<p dangerouslySetInnerHTML={{__html: props.content }}></p>
				</div>
				<div className="earthquakemap__row earthquakemap__header">
					<div>Date</div>
					<div>Location</div>
					<div>Place<br/>Affected</div>
					<div>Belligerent</div>
				</div>

				

				<div className="earthquakemap__entries">
					
					{currentFocusedIds && currentFocusedIds.length > 0 && <div className="earthquakemap__clusterselectionlabel">(in selected cluster) <div><span onClick={e => props.onSelectIncidentIds([])}>Clear</span> ×</div></div>}
					{entries && entries.map((strike, idx) => {
						if (currentFocusedIds.length === 0 || currentFocusedIds.includes(strike.id)) {

							const harmDescription = (strike.harm_description) ? <div className="earthquakemap__rowextra">{strike.harm_description}</div> : null
							const description = (strike.description) ? <div className="earthquakemap__rowextra">{strike.description}</div> : null
							
							return (
								<div key={'strike'+idx} className="earthquakemap__row">
									<div>{formatDate(strike.timestamp)}</div>
									<div>{strike.community}, {strike.region}</div>
									<div>{strike.place_affected}</div>
									<div>{strike.tracked_belligerent}</div>
									{harmDescription}
									{description}
								</div>
							)
						}
					})}
				

				</div>

			</div>
			<div className="earthquakemap__toggle" onClick={e => setOpen(!open)}>
				{open ? <span className="system">&rarr;</span> : <span className="system">&larr;</span>}
			</div>
		</Fragment>
	)
}

const Map = props => {

	const { map_data, entries, cursor, hoverId, onMouseEnter, onMouseLeave } = props

	const [viewport, setViewport] = useState({
		latitude: 36.1,
		longitude: 37,
		zoom: 7.3
	})
	const geojson = {
		type: 'FeatureCollection',
		features: []
	}

	entries.forEach((entry, index) => {

		geojson.features.push({			
			'type': 'Feature',
			'geometry': {
				'type': 'Point',
				'coordinates': [parseFloat(entry.longitude_x), parseFloat(entry.latitude_y)]						
			},
			'id': index, 
			'properties': entry,
		})


	})

	const contourLayerStyle = {
		id: 'contours',
		type: 'fill',
		layout: {
			'visibility': 'visible',			
		},

		paint: {
			'fill-color': [
				'interpolate',
				['linear'],
				['get', 'PARAMVALUE'],
				3.8, "#3288bd",
				4.72, "#99d594",
				5.64, "#e6f598",
				6.56, "#fee08b",
				7.48, "#fc8d59",
				8.2, "#d53e4f"
			],
			'fill-opacity': 1
		}
	}

	const clusterLayerStyle = {
		id: 'clusters',
		type: 'circle',
		filter: ['has', 'point_count'],
		paint: {
			'circle-radius': [
				'interpolate',
				['linear'],
				['get', 'point_count'],
				2, 8,
				256, 24,
			],
			'circle-stroke-color': '#147dbd',
			'circle-stroke-width': 1,
			'circle-color': '#178fd8'

		}
	}

	const clusterLabelLayerStyle = {
		id: 'cluster-labels',
		type: 'symbol',
		filter: ['has', 'point_count'],
		paint: {
			'text-color': '#fff'
		},
		layout: {
			'text-field': '{point_count}',
			'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
			'text-size': 11,
			'text-allow-overlap': false
		}
	}

	const singlePointsStyle = {
		id: 'single-point',
		type: 'circle',
		filter: ['!has', 'point_count'],
		paint: {
			'circle-radius': [
				'interpolate',
				['linear'],
				['zoom'],
				8, 4,	// When zoom is 0, set circle radius to 20
				15, 7,  // When zoom is 10, set circle radius to 30
			],
			'circle-color': '#178fd8',
			'circle-stroke-color': '#147dbd',
			'circle-stroke-width': 1,
			'circle-opacity': [
				'case',
				['==', ['id'], hoverId],
				0.6,
				1 
			]
		}
	}



	const clusterProperties = {}

	return (
		<ReactMapGL
			initialViewState={viewport}
			width="100%"
			height="100%"
			minZoom={6}
			maxZoom={10.5}
			cursor={cursor}			
			logoPosition={'bottom-right'}
			attributionControl={false}
			mapStyle={'mapbox://styles/anecdote101/clrgg7zeh00it01qtegsne0r7?fresh=true'}
			mapboxAccessToken={MAPBOX_ACCESS_TOKEN}
			interactiveLayerIds={['clusters', 'single-point']}
			// maxBounds={[
			// 	[33, 33.85], [44, 38]
			// ]}
			onClick={e => {
				if(e.features && e.features.length > 0){

					if(e.features[0].layer.id === 'clusters'){
						
						const features = e.target.queryRenderedFeatures(e.point, { layers: ['clusters'] })
						const clusterId = features[0].properties.cluster_id
						const pointCount = features[0].properties.point_count

						e.target.getSource('markers-source').getClusterLeaves(clusterId, pointCount, 0, (error, clusterLeavesFeatures) => {
							const clusterIncidentIds = []
							clusterLeavesFeatures.forEach(clusterLeavesFeature => {
								clusterIncidentIds.push(clusterLeavesFeature.properties.id)
							})
							props.onSelectIncidentIds(clusterIncidentIds)
						})


					} else {
						const incidentId = e.target.queryRenderedFeatures(e.point, { layers: ['single-point'] })[0].properties.id
						props.onSelectIncidentIds([incidentId])	
					}
				} else {
					props.onSelectIncidentIds([])
				}
			}}
			onMouseEnter={onMouseEnter}
			onMouseLeave={onMouseLeave}
			//onZoom={onZoom}

		>
			<Source id="earthquake-source" type="geojson" data={map_data.shakemap_geojson}>
				<Layer beforeId="syria-admin-1-labels" {...contourLayerStyle} />
			</Source>
			
			<Source id="markers-source" clusterRadius={14} cluster={true} clusterProperties={clusterProperties} type="geojson" data={geojson} >
				<Layer {...clusterLayerStyle} />
				<Layer {...clusterLabelLayerStyle} />
				<Layer {...singlePointsStyle} />
			</Source>
			<NavigationControl showCompass={false} style={{filter: 'invert(1)'}} position={'top-left'} />
		</ReactMapGL>
	)
}