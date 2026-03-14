import { setIncidentDate } from '../conflict-incidents'

export default data => {

	const container = document.querySelector('[data-chart-id="gaza-incidents-monitored-published"]')
	const maxIncidents = Object.values(data).reduce((max, current) => {
		return current.monitored > max ? current.monitored : max
	}, 0)

	// const totalPublished = Object.values(data).reduce((total, current) => {
	// 	return total + current.published
	// }, 0)

	// const totalResearched = Object.values(data).reduce((total, current) => {
	// 	return total + current.researched
	// }, 0)

	// const totalMonitored = Object.values(data).reduce((total, current) => {
	// 	return total + current.monitored
	// }, 0)

	// document.querySelector('.gazastatus--published .gazastatus__value').innerText = totalPublished.toLocaleString('en-GB')
	// document.querySelector('.gazastatus--researched .gazastatus__value').innerText = (Math.floor(totalResearched / 10) * 10).toLocaleString('en-GB')+'+'
	// document.querySelector('.gazastatus--monitored .gazastatus__value').innerText = (Math.floor(totalMonitored / 100) * 100).toLocaleString('en-GB')+'+'
	// document.querySelector('.gazastatus__total .gazastatus__value').innerText = (Math.floor((totalMonitored + totalPublished + totalResearched) / 100) * 100).toLocaleString('en-GB') + '+'

	const keys = Object.keys(data)

	const startDate = formatDate(keys[0])
	const endDate = formatDate(keys[keys.length-1])

	document.querySelector('.gazastatus__start').innerText = startDate
	document.querySelector('.gazastatus__end').innerText = endDate

	for (const key of Object.keys(data)) {

		const day = document.createElement('div')
		day.classList.add('gazastatus__day')
		const monitored = document.createElement('div')
		const researched = document.createElement('div')
		const published = document.createElement('div')

		monitored.classList.add('gazastatus--monitored')
		researched.classList.add('gazastatus--researched')
		published.classList.add('gazastatus--published')

		monitored.style.height = (data[key].monitored_remaining / maxIncidents) * 100 + '%'
		published.style.height = (data[key].published / maxIncidents) * 100 + '%'
		researched.style.height = (data[key].researched_remaining / maxIncidents) * 100 + '%'

		day.appendChild(monitored)
		day.appendChild(researched)
		day.appendChild(published)
	
		container.querySelector('.gazastatus__days').appendChild(day)
		day.addEventListener('mouseenter', (event) => showTooltip(event, data[key], key))
		day.addEventListener('mouseleave', (event) => hideTooltip(event, data[key]))
		day.addEventListener('click', (event) => filterDay(key))
	}

	document.querySelector('.gazastatus').style.opacity = 1

}

function showTooltip(e, data, key){
	const bbox = e.currentTarget.getBoundingClientRect()
	const timelineBbox = document.querySelector('.gazastatus__timeline').getBoundingClientRect()
	document.querySelector('.gazastatus__activeday').style.display = 'block'
	document.querySelector('.gazastatus__activeday').style.left = Math.min(timelineBbox.width, bbox.left - timelineBbox.left - 65) + 'px'
	document.querySelector('.gazastatus__activeday').innerHTML = `
		<div class="gazastatus__daydate gazastatus__datevalue"><div>${formatDate(key)}</div><div>${data.monitored}</div></div>
		<div class="gazastatus__datevalue gazastatus--monitored"><div>monitored</div> <div>${data.monitored_remaining}</div></div>
		<div class="gazastatus__datevalue gazastatus--researched"><div>active review</div> <div>${data.researched_remaining}</div></div>
		<div class="gazastatus__datevalue gazastatus--published"><div>published</div> <div>${data.published}</div></div>
	`

}

function hideTooltip(e, data){
	document.querySelector('.gazastatus__activeday').style.display = 'none'
}

function filterDay(date){
	setIncidentDate(date)
}

function formatDate(str){
	const options = { 
		year: 'numeric', 
		month: 'short', 
		day: 'numeric' 
	}
	return  new Intl.DateTimeFormat('en-US', options).format(new Date(`${str} 00:00:01`))
}














