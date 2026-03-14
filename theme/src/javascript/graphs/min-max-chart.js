import * as d3 from 'd3';

const renderChart = function(data, element, chartId, lang){

};

const createMinMaxChart = function(element, url, chartId, lang){
	

	d3.json(url).then(function(data){
		renderChart(data, element, chartId, lang);
	});

};

export default createMinMaxChart;