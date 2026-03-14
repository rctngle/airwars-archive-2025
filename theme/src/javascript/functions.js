import hasTouch from 'has-touch'

const attachTooltipEvents = function(element){
	element.querySelectorAll('i.far.fa-info-circle').forEach((el) => {
		let event = 'mouseenter'
		if(hasTouch){
			event = 'click'
		}
		el.addEventListener(event, () => {
			el.nextElementSibling.classList.add('visible')
		})
		el.addEventListener('mouseleave', () => {
			el.nextElementSibling.classList.remove('visible')
		})		
	})
}

export {
	attachTooltipEvents
}