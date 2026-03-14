import { tns } from 'tiny-slider/src/tiny-slider'

export default function createSliders(breakpoint) {
	document.querySelectorAll('.slider').forEach((slideshow, sidx)=>{

		let autoplay = false
		
		setTimeout(e=>{
			tns({
				nav: true,

				container: slideshow.querySelector('.slides'),
				slideBy: 'page',
				autoplay: autoplay,
				controls: true,

				// autoplayHoverPause: true,
				//autoplayText: ['<i class="fa-sharp fa-play"></i>', '<i class="fa-sharp fa-pause"></i>'],
				// navContainer: slideshow.querySelector('.tiny-slider__nav'),
				// controlsContainer: slideshow.querySelector('.tiny-slider__controls'),
				speed: 800,
				mouseDrag: false,
				swipeAngle: false,
				autoplayText: ['⏵', '⏸'],
				autoplayButton: slideshow.querySelector('.slider__autoplay'),
				navContainer: slideshow.querySelector('.slider__nav'),
				controlsContainer: slideshow.querySelector('.slider__controls')
			})	
		}, 0)
		
	})
}