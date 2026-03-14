import Config from './config/config';
import hasTouch from 'has-touch';

export default function() {
	createResponsiveHeader();
}

function createResponsiveHeader() {

	let breakpoint, previousBreakpiont;

	const onResize = () => {
		previousBreakpiont = breakpoint;
		breakpoint = undefined;
		for (let bp in Config.breakpoints) {
			const size = Config.breakpoints[bp];
			if (breakpoint === undefined && window.innerWidth < size) {
				breakpoint = bp;
			}
		}

		if (breakpoint === undefined) {
			breakpoint = 'lg';
		}

		if (previousBreakpiont !== breakpoint) {
			if (breakpoint === 'xsm') {
				destroyHeader();
				destroyTabletHeader();
				createMobileHeader();
			} else if (hasTouch) {
				destroyMobileHeader();
				destroyHeader();
				createTabletHeader();
			} else {
				destroyMobileHeader();
				destroyTabletHeader();
				createHeader();
			}
		}
	};

	window.addEventListener('resize', onResize);
	onResize();
}


function createTabletHeader(){
	document.querySelectorAll('header nav > ul > li, .page-title h1').forEach(function(el){
		el.addEventListener('click', function(e){
			onHeaderPageTitleMouseEnter(e);
			onHeaderMouseEnter();			
		});
	});

	document.addEventListener('scroll', function(){		
		if(document.querySelector('header').classList.contains('fixed')){
			document.querySelector('header').classList.remove('expanded');	
		}		
		onHeaderDocumentScroll();
	});
}

function destroyTabletHeader(){
	document.querySelectorAll('header nav > ul > li, .page-title h1').forEach(function(el){
		el.removeEventListener('click', onHeaderPageTitleMouseEnter);
	});
}

function createMobileHeader(){
	document.querySelector('.mobile-nav-toggle').addEventListener('click', onMobileNavToggleMouseDown);
	document.querySelectorAll('.with-subnav').forEach(function(el){
		el.addEventListener('click', onMobileSubnavClick);
	});	
}

function destroyMobileHeader() {
	const header = 	document.querySelector('header');
	if(header){
		header.classList.remove('open');	

		document.querySelector('.mobile-nav-toggle').removeEventListener('click', onMobileNavToggleMouseDown);
		document.querySelectorAll('.with-subnav').forEach(function(el){
			el.removeEventListener('click', onMobileSubnavClick);
		});		
	}
	
}

function onMobileNavToggleMouseDown(e) {
	const header = 	document.querySelector('header');
	if(header.classList.contains('open')){
		header.classList.remove('open');	
	} else {
		header.classList.add('open');
	}		
}

function onMobileSubnavClick(e) {
	const navItem = e.currentTarget;
	if(navItem.classList.contains('mobile-subnav-open')){
		navItem.classList.remove('mobile-subnav-open');	
	} else {
		document.querySelectorAll('.mobile-subnav-open').forEach(function(openElement){
			openElement.classList.remove('mobile-subnav-open');
		});	
		navItem.classList.add('mobile-subnav-open');
	}	
}

function createHeader() {
	if(document.querySelector('header')){
		document.querySelector('header').addEventListener('mouseenter', onHeaderMouseEnter);
		document.querySelectorAll('header nav > ul > li, .page-title h1').forEach(function(el){
			el.addEventListener('mouseenter', onHeaderPageTitleMouseEnter);
		});
		
		if(document.querySelector('.nav-conflict')){
			document.querySelector('.nav-conflict').addEventListener('mouseenter', closeSubNavs);	
		}

		document.querySelector('header').addEventListener('mouseleave', onHeaderMouseLeave);
		document.querySelector('nav').addEventListener('mouseleave', onNavMouseLeave);
		document.addEventListener('scroll', onHeaderDocumentScroll);	
	}
	
}

function destroyHeader() {
	document.querySelector('header').removeEventListener('mouseenter', onHeaderMouseEnter);
	document.querySelectorAll('header nav > ul > li, .page-title h1').forEach(function(el){
		el.removeEventListener('mouseenter', onHeaderPageTitleMouseEnter);
	});
	if(document.querySelector('.nav-conflict')){
		document.querySelector('.nav-conflict').removeEventListener('mouseenter', closeSubNavs);	
	}
	document.querySelector('header').removeEventListener('mouseleave', onHeaderMouseLeave);
	document.querySelector('nav').removeEventListener('mouseleave', onNavMouseLeave);
	document.removeEventListener('scroll', onHeaderDocumentScroll);
}

function onNavMouseLeave(e) {
	closeSubNavs();

}

function onHeaderMouseEnter(e) {
	let position = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
	if(position > 0 || document.querySelector('header').classList.contains('short') && !document.body.classList.contains('syria-earthquake-strikes')) {
		document.querySelector('header').classList.add('expanded', 'fixed');	
	}	
}

function onHeaderMouseLeave(e) {
	closeSubNavs();
	document.querySelector('header').classList.remove('expanded');
	let position = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
	if(position <= 340){
		document.querySelector('header').classList.remove('fixed');
	}
}

function onHeaderPageTitleMouseEnter(e) {

	const el = e.currentTarget;
	closeSubNavs();

	if(el.classList.contains('with-subnav')){
		if(document.querySelector('.nav-conflict')){
			document.querySelector('.nav-conflict').classList.add('hide');
		}
	}
	el.classList.add('subnav-open');
}

function onHeaderDocumentScroll(e) {
	closeSubNavs();
	let position = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
	if(position > 340){
		document.querySelector('header').classList.add('fixed');
	} else {
		document.querySelector('header').classList.remove('fixed');
	}
	if(!document.querySelector('header').classList.contains('short')){
		document.querySelector('header > .content').style.height = (420 - position)+ 'px';	
	}
}

function closeSubNavs() {
	if(document.querySelector('.nav-conflict')){
		document.querySelector('.nav-conflict').classList.remove('hide');	
	}

	document.querySelectorAll('.subnav-open').forEach((el) => {
		el.classList.remove('subnav-open');
	});
}
