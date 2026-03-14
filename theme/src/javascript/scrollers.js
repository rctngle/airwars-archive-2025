import ScrollBooster from 'scrollbooster'
import animateScrollTo from 'animated-scroll-to'
//import hasTouch from 'has-touch'
const scrollboosters = []

export default function createScrollers() {

	const scrollers = document.querySelectorAll('.scroller')

	scrollers.forEach(strip=>{

		const scrollerContainer = strip.querySelector('.scroller__outer')
		scrollboosters.push(
			new ScrollBooster({ 
				viewport: scrollerContainer,
				scrollMode: 'native', 
				direction: 'horizontal',
				bounce: true,
				onUpdate: state => {},
				onPointerDown: () => {
					scrollerContainer.classList.add('grabbing')
				},
				onPointerUp: () => {
					scrollerContainer.classList.remove('grabbing')	
				}
			})
		)

		document.querySelector('.scroller__rightarrow').addEventListener('click', e => {
			const scrollAmount = 400
			const scrollLeft = scrollerContainer.scrollLeft

			animateScrollTo([scrollLeft + scrollAmount, null], {
				cancelOnUserAction: true,
				elementToScroll: scrollerContainer,
			})

		})

		document.querySelector('.scroller__leftarrow').addEventListener('click', e => {

			const scrollAmount = 400
			const scrollLeft = scrollerContainer.scrollLeft	
			
			animateScrollTo([scrollLeft - scrollAmount, null], {
				cancelOnUserAction: true,
				elementToScroll: scrollerContainer,
			})

		})

		
	})



	
	
	

}

export function updateScrollers() {
	setTimeout(e=>{
		scrollboosters.forEach(scrollbooster=>{
			scrollbooster.updateMetrics()
		})	
	}, 400)
}