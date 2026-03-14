import Pikaday from 'pikaday'

let picker

export default () => {

	document.querySelectorAll('.citations__expand').forEach(expand=>{
		expand.addEventListener('click', e=>{
			if(document.querySelector('.citations')){
				document.querySelector('.citations').classList.toggle('citations--expanded')
			}
		})
	})

	const incidentDateInput = document.querySelector('input[name=incident_date')
	const firstIncident = document.querySelector('.civ')
	let defaultDate = dateFormatYMD(new Date())
	if (firstIncident) {
		defaultDate = firstIncident.dataset.incidentdate
	}

	picker = new Pikaday({ 
		field: incidentDateInput,
		format: 'D MMMM YYYY',
		defaultDate: new Date(defaultDate),
		onSelect: function(e){
			updateFilters()
		}
	})

	document.querySelectorAll('.incidentpreviews__filter').forEach(filterSet => {
		filterSet.querySelectorAll('input').forEach(input => {
			input.addEventListener('change', e => {
				updateInputs(e.target.value)
			})
		})
	})
	updateInputs()
	if(document.querySelector('.incidentpreviews .clear-filters')){
		document.querySelector('.incidentpreviews .clear-filters').addEventListener('click', e => {
			e.preventDefault()
			document.querySelectorAll('.incidentpreviews__filter').forEach(filterSet => {
				filterSet.querySelectorAll('input').forEach(input => {
					if (input.value === 'all') {
						input.checked = true
					} else {
						input.checked = false
					}
				})
			})

			incidentDateInput.value = ''

			updateInputs()
		})		
	}

	document.querySelectorAll('input[name=order_by]').forEach(input => {
		input.addEventListener('click', e => {
			const orderBy = e.target.value
			sortElements(orderBy)
		})
	})
	
}

export const setIncidentDate = date => {
	picker.setDate(date)
	updateFilters()
}


function sortElements(orderBy) {
	const parent = document.querySelector('.grid')
	const items = Array.from(parent.querySelectorAll('.civ'))

	items.sort((a, b) => {
		const aValue = a.getAttribute(`data-${orderBy}`)
		const bValue = b.getAttribute(`data-${orderBy}`)
		

		// Convert the values to dates for proper comparison
		const aDate = new Date(aValue)
		const bDate = new Date(bValue)
		
		// Sort in descending order
		return bDate - aDate
	})

	// Clear the parent and append sorted items
	parent.innerHTML = ''
	items.forEach(item => parent.appendChild(item))
}


const dateFormatYMD = date => {
	const year = date.getFullYear()
	const month = String(date.getMonth() + 1).padStart(2, '0')
	const day = String(date.getDate()).padStart(2, '0')
	return `${year}-${month}-${day}`
}

const updateInputs = (value) => {
	document.querySelectorAll('.incidentpreviews__filter').forEach(filterSet => {
		if (filterSet.querySelector('input[type=checkbox]')) {

			const all = filterSet.querySelector('input[value="all"]')
			const inputs = filterSet.querySelectorAll('input:not([value="all"])')
			const checked = filterSet.querySelectorAll('input:checked:not([value="all"])')

			if (checked.length === 0) {
				all.checked = true
			}

			if (checked.length > 0 && value !== 'all') {
				all.checked = false
			} 

			if (all.checked) {
				inputs.forEach(input => {
					input.checked = false
				})
			}

			updateFilters()

		}
	})

	updateFilters()
}

const updateFilters = () => {

	const filters = {}

	const addFilter = filter => {

		
		const field = filter.split('-').shift()
		if (!filters[field]) {
			filters[field] = []
		}
		filters[field].push(filter)
	}


	document.querySelectorAll('.incidentpreviews__filter').forEach(filterSet => {
		filterSet.querySelectorAll('input').forEach(input => {
			if (!['order_by'].includes(input.name)) {
				if (input.checked) {
					input.parentNode.classList.add('active')
				} else {
					input.parentNode.classList.remove('active')
				}

				if (input.type === 'checkbox') {
					if (input.checked) {
						if (input.value !== 'all') {
							addFilter(input.value)
						}
					}
				} else if (input.type === 'radio') {
					if (input.checked) {
						if (input.value !== 'all') {
							addFilter(input.value)
						}
					}
				}
			}
		})
	})

	const incidentDateInput = document.querySelector('input[name=incident_date')

	if (incidentDateInput && incidentDateInput.value && incidentDateInput.value !== '') {
		const ymd = dateFormatYMD(new Date(incidentDateInput.value))
		addFilter(`incident_date-${ymd}`)
	}
		
	const incidents = document.querySelectorAll('.civ')

	const matchesAtLeastOneFromEachCategory = (element, categories) => {
		return Object.values(categories).every(category => 
			category.some(cls => element.classList.contains(cls))
		)
	}

	incidents.forEach(incident => {
		if (matchesAtLeastOneFromEachCategory(incident, filters)) {
			incident.classList.add('filter-show')
			incident.classList.remove('filter-hide')
		} else {
			incident.classList.add('filter-hide')
			incident.classList.remove('filter-show')
		}
	})

	let numFilters = 0
	for (let field in filters) {
		numFilters += filters[field].length
	}


	if(document.querySelector('.incidentpreviews')){
		if (numFilters > 0) {
			document.querySelector('.incidentpreviews').classList.add('filters-applied')		
		} else {
			document.querySelector('.incidentpreviews').classList.remove('filters-applied')
		}

		document.querySelector('.incidentpreviews .num-results').textContent = document.querySelectorAll('.incidentpreviews .filter-show').length	
	}
	
}
