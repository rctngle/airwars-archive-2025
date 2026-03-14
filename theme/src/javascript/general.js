import inView from 'in-view';
import hasTouch from 'has-touch';
import { attachTooltipEvents } from  './functions';
import easyScroll from 'easy-scroll';


var breakpoint = {};

breakpoint.refreshValue = function () {
	this.value = window.getComputedStyle(document.querySelector('body'), ':before').getPropertyValue('content').replace(/\"/g, '');
};

window.addEventListener('resize',e=>{
	breakpoint.refreshValue();
});


document.addEventListener('DOMContentLoaded', e=>{
	if(document.body.classList.contains('investigation')){
		window.addEventListener('scroll', e=>{
			if(window.scrollY > 50){
				document.querySelector('header').classList.add('header--pinned')
			} else {
				document.querySelector('header').classList.remove('header--pinned')
			}
		})
	}
})

function createFakeLanguageSwitcher(){
	document.querySelectorAll('.lang-switcher').forEach(languageSwitcher=>{
		languageSwitcher.querySelectorAll('div').forEach(button=>{

			button.addEventListener('click', e=>{
				const language = button.dataset.lang;
				const articleParent = button.closest('article');
				articleParent.dataset.lang = language;
			});
		});
	});
}

function createEvents(){

	// archives case study toggle
	if(document.querySelector('.case-studies-toggle')){
		document.querySelector('.case-studies-toggle').addEventListener('click', function(e){
			e.preventDefault();
			let list = this.parentNode.parentNode.parentNode.parentNode;
			if(list.classList.contains('case-studies-visible')){
				list.classList.remove('case-studies-visible');
				this.querySelector('i#arrow').className = 'fas fa-caret-down';
			} else {
				list.classList.add('case-studies-visible');
				this.querySelector('i#arrow').className = 'fas fa-caret-up';
			}
			
		});	
	}
	

	// military reports annotation switch
	document.querySelectorAll('.tab-switch div').forEach(function(el){
		el.addEventListener('click', function(e){
			getSiblings(el).forEach(function(element){
				element.classList.remove('active');
			});
			if(!el.classList.contains('active')){
				el.classList.add('active');
			}
			const articleParent = el.closest('article');
			if(el.classList.contains('original')){
				articleParent.classList.add('original');
			} else if(el.classList.contains('annotated')){
				articleParent.classList.remove('original');
			}
		});
	});

	// strike report language switch
	document.querySelectorAll('.statement-border-nav.language').forEach(function(el){

		el.addEventListener('mousedown', function(e){
			var report = this.parentNode.nextElementSibling; 
			
			// this.
			// if(!target.classList.contains('active')){
			// 	target.classList.add('active');
			// }
			if(e.target.classList.contains('or')){
				report.classList.add('original');
				this.classList.add('original');
			} else {
				report.classList.remove('original');
				this.classList.remove('original');
			}

		});
	});

	
	document.querySelectorAll('li.graphic').forEach(li => {
		li.addEventListener('click', e => {
			if (li.classList.contains('revealed')) {
				li.classList.remove('revealed')
			} else {
				li.classList.add('revealed')
			}
		})
	})

	// document.querySelectorAll('.graphic-warning').forEach(function(el){
	// 	el.addEventListener('mousedown', function(){
	// 		el.parentNode.classList.add('revealed');
	// 	});
	// });

	// header hover

	// if(document.querySelector('header .nav-conflict > div') !== null){
	// 	document.querySelector('header .nav-conflict > div').addEventListener('mouseenter', function(){
	// 		document.querySelector('header').classList.add('expanded');
	// 	});
	// 	document.querySelector('header .nav-conflict > div').addEventListener('mouseleave', function(){
	// 		document.querySelector('header').classList.remove('expanded');
	// 	});
	// }

	// document.querySelector('header').addEventListener('mouseenter', function(e){
	// 	if(e.target.classList.contains('fixed')){
	// 		document.querySelector('header').classList.add('expanded');	
	// 	}		
	// });


	// document.querySelector('header').addEventListener('mouseleave', function(e){
	// 	if(e.target.classList.contains('fixed')){
	// 		document.querySelector('header').classList.remove('expanded');	
	// 	}
	// });



	document.querySelectorAll('span.geo-link a').forEach(function(el){
		el.addEventListener('click', function(e){
			e.preventDefault();
			window.open(el.href);
		});
	});
	document.querySelectorAll('span.geo-link').forEach(function(el){
		el.addEventListener('click', function(e){			
			if(el.classList.contains('show-warning')){
				el.classList.remove('show-warning');
			} else {
				el.classList.add('show-warning');
			}
		});
	});

	document.querySelectorAll('.civilian-casualty-statements-nav li').forEach(function(el){
		el.addEventListener('mouseup', function(){

			var index = indexInParent(el) + 1;
			
			el.parentNode.querySelector('li.active').classList.remove('active');
			el.classList.add('active');

			el.parentNode.parentNode.nextElementSibling.querySelector('li.active').classList.remove('active');
			el.parentNode.parentNode.nextElementSibling.querySelector('li:nth-child('+index+')').classList.add('active');

			el.parentNode.nextElementSibling.querySelector('li.active').classList.remove('active');
			el.parentNode.nextElementSibling.querySelector('li:nth-child('+index+')').classList.add('active');
		});
	});	

	// expand collapse method
	// document.querySelectorAll('article:not(.type-lnews) h2 span').forEach(function(el){
	document.querySelectorAll('article.civ h2 span').forEach(function(el){
		el.addEventListener('mousedown', function(e){
			if(el.parentNode.parentNode.classList.contains('closed')){
				el.parentNode.parentNode.classList.remove('closed');
				el.innerHTML = '[<i class="far fa-arrow-up"></i> collapse]';
			} else {
				el.parentNode.parentNode.classList.add('closed');
				el.innerHTML = '[<i class="far fa-arrow-down"></i> expand]';
			}
		});
	});

	//show tooltips on hover
	attachTooltipEvents(document.querySelector('main'));
}



function checkCaptions() {

	document.querySelectorAll('article.civ').forEach(function(post) {
		var sources = post.querySelector('.info-right .sources-list');
		var sourcesPosition = getPosition(sources);
		//1393
		var sourcesBottom = sourcesPosition.top + sourcesPosition.height;
		post.querySelectorAll('.media-list .media-item').forEach(function(mediaItem, mediaItemIdx) {
	
			mediaItem.querySelectorAll('.caption').forEach(function(caption) {
				var captionPosition = getPosition(caption);
				var captionHeight = captionPosition.height;
				//var captionBottom = captionPosition.top + captionHeight;

				var mediaPosition = getPosition(mediaItem);
				var mediaHeight = mediaPosition.height;
				var mediaTop = mediaPosition.top;
				var mediaBottom = mediaPosition.top + mediaHeight;
				
				mediaItem.classList.remove('caption-bottom');
				mediaItem.classList.remove('caption-under');

				if (mediaTop > sourcesBottom) {
					//
				} else if (mediaBottom - (captionHeight + 150) > sourcesBottom) {
					mediaItem.classList.add('caption-bottom');
				} else {
					mediaItem.classList.add('caption-under');
				}
			});	
			
		});
	});
}

function getPosition(el) {
	if (el) {
		var rect = el.getBoundingClientRect();
		let scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
		let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var top = rect.top || rect.y;
		var left = rect.left || rect.x;

		return { 
			top: top + scrollTop, 
			left: left + scrollLeft,
			width: rect.width,
			height: rect.height,

		};
	} else {
		return { 
			top: 0,
			left: 0,
			width: 0,
			height: 0,

		};
	}
}

window.twttr = (function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0],
		t = window.twttr || {};
	if (d.getElementById(id)) return t;
	js = d.createElement(s);
	js.id = id;
	js.src = 'https://platform.twitter.com/widgets.js';
	fjs.parentNode.insertBefore(js, fjs);

	t._e = [];
	t.ready = function(f) {
		t._e.push(f);
	};

	return t;
}(document, 'script', 'twitter-wjs'));



window.fbAsyncInit = function() {
	FB.init({
		xfbml: true,
		version: 'v3.0'
	});
	window.handleFBLoad();
}; 

window.handleFBLoad = function() {
	createMedia();
};

function createMedia() {
	inView.offset(-1000);
	inView('.media-embed').on('enter', function(el) {
		checkCaptions();
		if (el.hasAttribute('data-embed')) {			
			var embed = el.getAttribute('data-embed');
			el.querySelector('.media').innerHTML = embed;
			el.removeAttribute('data-embed');

			el.querySelectorAll('script').forEach(function(s) {
				s.parentNode.removeChild(s);
			});
			
			
			if (window.FB) {
				window.FB.XFBML.parse(el);		
			}			
			if(window.twttr){
				window.twttr.widgets.load(el);				
			}

		}
		if(el.querySelector('.media').textContent.trim().length > 0 && el.querySelector('.media').childNodes[0].tagName === undefined){
			el.style.display = 'none';
		}
	}).on('exit', function(el) {
		
	});

	inView('.media-image').on('enter', function(el) {

		checkCaptions();
		if (el.hasAttribute('data-image')) {
			var src = el.getAttribute('data-image');
			el.querySelector('.media').innerHTML = '<img src="' + src + '" />';
			el.removeAttribute('data-embed');

			if (window.twttr.widgets) {
				window.twttr.widgets.load(el);	
			}

			var img=new Image();
			img.onload = function() {
				checkCaptions();
			};
			img.src = src;
			
		}
	}).on('exit', function(el) {
		
	});
}

function indexInParent(node) {
	var children = node.parentNode.childNodes;
	var num = 0;
	for (var i=0; i<children.length; i++) {
		if (children[i]==node) return num;
		if (children[i].nodeType==1) num++;
	}
	return -1;
}


function createSideScroller() {
	const arrowRight = document.querySelector('.conflict-events .fa-arrow-circle-right');
	const arrowLeft = document.querySelector('.conflict-events .fa-arrow-circle-left');

	if (arrowRight) {
		arrowRight.addEventListener('click', e => {

			const event = document.querySelector('.conflict-events .event');
			const rect = event.getBoundingClientRect();
			const width = rect.width + 20;

			const scrollEL = document.querySelector('.conflict-events .conflict-event-inner');
			easyScroll({
				'scrollableDomEle': scrollEL,
				'direction': 'right',
				'duration': 300,
				'easingPreset': 'linear',
				'scrollAmount': width
			});
			
		})
	}
	

	if (arrowLeft) {
		arrowLeft.addEventListener('click', e => {
			
			const event = document.querySelector('.conflict-events .event');
			const rect = event.getBoundingClientRect();
			const width = rect.width + 20;

			const scrollEL = document.querySelector('.conflict-events .conflict-event-inner');
			easyScroll({
				'scrollableDomEle': scrollEL,
				'direction': 'right',
				'duration': 300,
				'easingPreset': 'linear',
				'scrollAmount': -width
			});
			
		})
	}
}

var getSiblings = function (elem) {
	var siblings = [];
	var sibling = elem.parentNode.firstChild;
	for (; sibling; sibling = sibling.nextSibling) {
		if (sibling.nodeType !== 1 || sibling === elem) continue;
		siblings.push(sibling);
	}
	return siblings;
};


window.resizeIframe = function(obj) {
	obj.style.height = (obj.contentWindow.document.body.scrollHeight + 100) + 'px';
};

export default function() {
	createEvents();
	createMedia();
	createFakeLanguageSwitcher();
	createSideScroller();

}
