import React, { Component } from 'react';

import { select } from 'd3-selection';
import { scaleLinear } from 'd3-scale';

import { attachTooltipEvents } from  '../functions';

// import * as request from 'd3-request' // d3 submodule (contains d3.csv, d3.json, etc)

// create a Object with only the subset of functions/submodules/plugins that we need
const d3 = Object.assign(
	{},
	{		
		scaleLinear,		
		select
	}
);

class ConflictTimelineGraphBars extends Component {

	constructor(props) {
		super(props);
		this.graphRef = React.createRef();
		
	}

	componentDidMount() {
		this.drawBars();
	}

	componentDidUpdate(prevProps){
		this.drawBars();
	}

	drawBars(){

		const self = this;
		const margin = {top: 0, right: 0, bottom: 0, left: 0};
		const width = this.graphRef.current.getBoundingClientRect().width - margin.left - margin.right;
		const height = 77 - margin.top - margin.bottom;

		const x = d3.scaleLinear().range([0, width]);
		const y = d3.scaleLinear().range([height, 0]);

		const oldSVG = this.graphRef.current.querySelector('svg');

		if(oldSVG){
			oldSVG.parentNode.removeChild(oldSVG);
		}


		const svg = d3.select(this.graphRef.current).append('svg')
			.attr('width', width + margin.left + margin.right)
			.attr('height', height + margin.top + margin.bottom)
			.append('g')
			.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')');

		
		if (this.props.lang === 'ar' || this.props.lang === 'he') {
			x.domain([this.props.timeline.length, 0]);
		} else {
			x.domain([0, this.props.timeline.length]);
		}

		y.domain([0, 100]);

		const hasHeight = [];
		this.props.timeline.forEach((day,i)=>{
			if(day.percentage > 0){
				day.index = i;
				hasHeight.push(day);
			}
		});

		svg.selectAll('.bar')
			.data(hasHeight)
			.enter().append('rect')
			.attr('class', 'bar')
			.attr('x', function(d, i) {

				if(self.props.lang === 'ar' || self.props.lang === 'he'){
					return x(d.index) - Math.max(1, width / self.props.timeline.length);
				} else {
					return x(d.index);	
				}
				
			})
			.style('fill', function(d){
				if(d.day === '2018-03-17' && self.props.conflictslug && self.props.conflictslug.indexOf('turkish-military-in-iraq-and-syria') !== -1){
					//return 'red';
				}
			})
			.attr('width', Math.max(1, width / this.props.timeline.length))
			.attr('y', function(d) { 
				if(d.day === '2018-03-17' && self.props.conflictslug && self.props.conflictslug.indexOf('turkish-military-in-iraq-and-syria') !== -1){
					return 0;	
				}
				return y(d.percentage * 100);
			})
			.attr('height', function(d) {
				if(d.day === '2018-03-17' && self.props.conflictslug && self.props.conflictslug.indexOf('turkish-military-in-iraq-and-syria') !== -1){
					return 100;				
				}		
				return height - y(d.percentage * 100);
			});	

	}

	render() {

		if (!this.props.timeline) {
			return null;
		}		
		
		return (
			<div className="timeline-bars">				
				<div className="svg-container" ref={this.graphRef}></div>				
			</div>
		);		
	}
}


export default ConflictTimelineGraphBars;