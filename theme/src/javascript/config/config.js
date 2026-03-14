export default {
	api: function() {
		return window.location.protocol+'//'+window.location.host+'/wp-json/airwars/v1';
	},

	conflictSettings: {

		'all': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o', 
				en: 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06'
			},
			centers: {
				lg: [31.386614154065256, 33.76787382880254],
				md: [31.386614154065256, 33.76787382880254],
				sm: [31.386614154065256, 33.76787382880254],
				xsm: [31.386614154065256, 33.76787382880254],				
				none: [31.386614154065256, 33.76787382880254]
			},
			zooms: {
				lg: 5.1,
				md: 5.1,
				sm: 5.1,
				xsm: 5.1,
				none: 5.1
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'palestinian-militants-in-israel': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cksirovet01ob17pj6t34591n',
				ar: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c',
				he: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c'
			},
			centers: {
				lg: [34.3829207, 31.7138970],
				md: [34.3829207, 31.4138970],
				sm: [34.3829207, 31.4138970],
				xsm: [34.3829207, 31.4138970],
				none: [34.3829207, 31.4138970]
			},
			zooms: {
				lg: 7.2,
				md: 7.2,
				sm: 7.2,
				xsm: 7.2,
				none: 7.2
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},

	
		'israeli-military-in-the-gaza-strip': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c',
				ar: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c',
				he: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c'
			},
			
			centers: {
				lg: [34.3829207, 31.4138970],
				md: [34.3829207, 31.4138970],
				sm: [34.3829207, 31.4138970],
				xsm: [34.3829207, 31.4138970],
				none: [34.3829207, 31.4138970]
			},
			zooms: {
				lg: 9.6,
				md: 9.6,
				sm: 9.6,
				xsm: 9.6,
				none: 9.6
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'israeli-military-in-syria-the-gaza-strip-syria': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cksirovet01ob17pj6t34591n',
				ar: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c',
				he: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c'
			},
			
			centers: {
				lg: [38.737922, 34.868975],
				md: [38.737922, 34.868975],
				sm: [38.737922, 34.868975],
				xsm: [38.737922, 34.868975],
				none: [38.737922, 34.868975]
			},
			zooms: {
				lg: 5.84,
				md: 5.84,
				sm: 5.84,
				xsm: 5.84,
				none: 5.84
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'israeli-military-in-syria-the-gaza-strip-the-gaza-strip': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cksirovet01ob17pj6t34591n',
				ar: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c',
				he: 'mapbox://styles/anecdote101/cksirj5mmab4t17pjx7v86j2c'
			},
			centers: {
				lg: [34.3829207, 31.4138970],
				md: [34.3829207, 31.4138970],
				sm: [34.3829207, 31.4138970],
				xsm: [34.3829207, 31.4138970],
				none: [34.3829207, 31.4138970]
			},
			zooms: {
				lg: 9.51,
				md: 9.51,
				sm: 9.51,
				xsm: 9.51,
				none: 9.51
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'israeli-military-in-iraq-syria': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o', 
				en: 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06'
			},
			centers: {
				lg: [40.4659, 34.7363],
				md: [40.4659, 34.7363],
				sm: [40.4659, 34.7363],
				xsm: [40.4659, 34.7363],
				none: [40.4659, 34.7363]
			},
			zooms: {
				lg: 5.26,
				md: 5.26,
				sm: 5.26,
				xsm: 5.26,
				none: 5.26
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'us-forces-in-somalia': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cjzwh4m9m00ed1cper9nzxly5'
			},
			centers: {
				lg: [48.77444, 5.216082],
				md: [48.77444, 5.216082],
				sm: [48.77444, 5.216082],
				xsm: [48.77444, 5.216082],
				none: [46.6311630, 3.938776845]
			},
			zooms: {
				lg: 5.307512171,
				md: 5.307512171,
				sm: 5.307512171,
				xsm: 5.307512171,
				none: 4.5
			},
			mapHeights: {
				lg: 800,
				md: 800,
				sm: 800,
				xsm: 800,
				none: 450
			}
		},
		'coalition-in-iraq-and-syria': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o', 
				en: 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06'
			},
			centers: {
				lg: [40.351, 35.816],
				md: [40.351, 35.816],
				sm: [40.351, 35.816],
				xsm: [40.351, 35.816],
				none: [40.351, 35.816]
			},
			zooms: {
				lg: 6.1,
				md: 6.1,
				sm: 6.1,
				xsm: 6.1,
				none: 5.1
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 350,
				none: 350
			}
		},
		'russian-military-in-syria': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/cjpi5j3vq0alc2rmk5cp19m7i',
				en: 'mapbox://styles/anecdote101/cjpi4vqp40a0a2sldkgn8raz1'
			},
			centers: {
				lg: [38.70065233824755, 35.134192825069235],
				md: [38.70065233824755, 35.134192825069235],
				sm: [38.70065233824755, 35.134192825069235],
				xsm: [38.70065233824755, 35.134192825069235],
				none: [38.70065233824755, 35.134192825069235]
			},
			zooms: {
				lg: 6.1,
				md: 6.1,
				sm: 6.1,
				xsm: 6.1,
				none: 5.1
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 350,
				none: 350
			}
		},
		'russian-military-in-ukraine': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/clh6baqsr00rx01qucdlb9hcd', 
				en: 'mapbox://styles/anecdote101/clh6baqsr00rx01qucdlb9hcd'
			},
			centers: {
				lg: [36.658652281, 49.6171534529],
				md: [36.658652281, 49.6171534529],
				sm: [36.658652281, 49.6171534529],
				xsm: [36.658652281, 49.6171534529],				
				none: [36.658652281, 49.6171534529]
			},
			zooms: {
				lg: 6.371931834124751,
				md: 6.371931834124751,
				sm: 6.371931834124751,
				xsm: 6.371931834124751,
				none: 6.371931834124751
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 450,
				none: 450
			}
		},
		'shahed-map': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/clm01id5d00ou01pb5r4fegib', 
				en: 'mapbox://styles/anecdote101/clm01id5d00ou01pb5r4fegib'
			},
			centers: {
				lg: [ 31.4982062137,49.19446764240],
				md: [ 31.4982062137,49.19446764240],
				sm: [ 31.4982062137,49.19446764240],
				xsm: [ 31.4982062137,49.19446764240],				
				none: [ 31.4982062137,49.19446764240]
			},
			zooms: {
				lg: 5.3906168,
				md: 5.3906168,
				sm: 5.3906168,
				xsm: 5.3906168,
				none: 5.3906168
			},
			mapHeights: {
				lg: 600,
				md: 600,
				sm: 600,
				xsm: 600,
				none: 600
			}
		},
		'turkish-military-in-iraq-and-syria': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/cjpi4kn2r09qv2so631u9c25o', 
				en: 'mapbox://styles/anecdote101/cjogy9ys57c2g2rmyca5ijk06'
			},
			centers: {
				lg: [40.351, 35.816],
				md: [40.351, 35.816],
				sm: [40.351, 35.816],
				xsm: [40.351, 35.816],
				none: [40.351, 35.816]
			},
			zooms: {
				lg: 6.1,
				md: 6.1,
				sm: 6.1,
				xsm: 6.1,
				none: 5.1
			},
			mapHeights: {
				lg: 450,
				md: 450,
				sm: 450,
				xsm: 350,
				none: 350
			}
		},
		'all-belligerents-in-libya': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/ckiio6ifu0fr519ofi5s8f35r',
				en: 'mapbox://styles/anecdote101/ckiio6ifu0fr519ofi5s8f35r'
			},
			centers: {
				lg: [17.13009835, 30.6786298],
				md: [17.13009835, 30.6786298],
				sm: [17.13009835, 30.6786298],
				xsm: [17.13009835, 30.6786298],
				none: [17.13009835, 30.6786298]
			},
			zooms: {
				lg: 5.436,
				md: 5.436,
				sm: 5.436,
				xsm: 5.436,
				none: 4.3
			},
			mapHeights: {
				lg: 550,
				md: 550,
				sm: 550,
				xsm: 550,
				none: 450
			}
		},
		'all-belligerents-in-libya-2011': {
			mapStyle: {
				ar: 'mapbox://styles/anecdote101/ckiio6ifu0fr519ofi5s8f35r',
				en: 'mapbox://styles/anecdote101/ckiio6ifu0fr519ofi5s8f35r'
			},
			centers: {
				lg: [17.13009835, 30.6786298],
				md: [17.13009835, 30.6786298],
				sm: [17.13009835, 30.6786298],
				xsm: [17.13009835, 30.6786298],
				none: [17.13009835, 30.6786298]
			},
			zooms: {
				lg: 5.436,
				md: 5.436,
				sm: 5.436,
				xsm: 5.436,
				none: 4.8
			},
			mapHeights: {
				lg: 550,
				md: 550,
				sm: 550,
				xsm: 550,
				none: 450
			}
		},
		'us-forces-in-yemen': {
			mapStyle: {
				en: 'mapbox://styles/anecdote101/cjzwh0q890syy1cs8q6aj9ulm',
				ar: 'mapbox://styles/anecdote101/cjzwh0q890syy1cs8q6aj9ulm'
			},
			centers: {
				lg: [49.1752, 14.7907],
				md: [49.1752, 14.7907],
				sm: [49.1752, 14.7907],
				xsm: [49.1752, 14.7907],
				none:  [47.8, 15.737]
			},
			zooms: {
				lg: 5.914,
				md: 5.914,
				sm: 5.914,
				xsm: 5.914,
				none: 4.5
			},
			mapHeights: {
				lg: 550,
				md: 550,
				sm: 550,
				xsm: 350,
				none: 350
			}
		}
	},
	colors: {
		'russian-military': '#2fb0d8',
		'russian-military-in-syria': '#2fb0d8',
		'shahed-map': '#009688',
		'coalition': '#35495d',
		'coalition-in-iraq-and-syria': '#35495d',

		'turkish-military': '#2abb9b',
		'turkish-military-in-iraq-and-syria': '#2abb9b',
		'israeli-military-in-iraq-syria': '#a0568d',
		'israeli-military-in-syria-the-gaza-strip': '#a0568d',
		'israeli-military': '#a0568d',
		
		'palestinian-forces': '#fb8734',
		'palestinian-militants': '#fb8734',
		'palestinian-militants-in-israel': '#03c591',
		'all-belligerents-in-libya': '#f1c561',
		'all-belligerents-in-libya-2011': '#e69c40',
		
		'us-forces-in-somalia': '#de8462',
		'isis-somalia': '#2287d0',
		'al-shabaab': '#8232b5',
		'al-qaeda-in-east-africa': '#ff0000',
		'declared': '#2980a5',
		'alleged': '#cc5d88',
		'unknown': '#999',
		'militants': '#2980a5',
		'other': '#333',
		'al-qaeda': 'red',
		'al-qaeda-in-the-arabian-peninsula-aqap': '#4eb391',
		'isis-yemen': '#397aa9',
		'us-forces-in-yemen': '#798ebb',
		'russian-military-in-ukraine': '#005BBB',
		'libya_map': {
			'lna-uae-military-egyptian-military': '#737eb5',
			'libyan-national-army': '#544ea2',
			'egyptian-military': '#3a86e4',
			'gna-turkish-military': '#60bf7d',
			'government-of-national-accord': '#60bf7d',
			'us-forces': '#57d6ca',
			'general-national-congress': '#96c5b9',
			'uk-military': '#282a73',
			'french-military': '#e3b551',
			'israeli-military': '#a0568d',
			'7th-brigade': '#c4e055',
			'chadian-military': '#cb5034',
			'russian-military': '#7c37a0',
			'united-arab-emirates-military': 'red',
			'turkish-military': 'green',
			contested: '#999',
			unknown: '#3e3e3e',
			'nato-forces': '#012169',
			'gaddafi-forces': '#008542',
			'libyan-rebel-forces': '#c50000'
		},
		'libya': {
			'lna-uae-military-egyptian-military': '#737eb5',
			'libyan-national-army': '#544ea2',
			'egyptian-military': '#3a86e4',
			'gna-turkish-military': '#60bf7d',
			'government-of-national-accord': '#60bf7d',
			'us-forces': '#57d6ca',
			'general-national-congress': '#96c5b9',
			'uk-military': '#282a73',
			'french-military': '#e3b551',
			'israeli-military': '#a0568d',
			'7th-brigade': '#c4e055',
			'chadian-military': '#cb5034',
			'russian-military': '#7c37a0',
			
			'contested': '#999',
			'unknown': '#3e3e3e',
			'nato-forces': '#012169',
			'gaddafi-forces': '#008542',
			'libyan-rebel-forces': '#c50000'
		}
	},


	breakpoints: {
		xsm: 768,
		sm: 1024,
		md: 1260,
		lg: 1420
	},
	dateFormat: 'D MMM YYYY',
	colorSchemes: {
		'libya-2011-civcas-timeline': {
			'nato-forces': '#012169',
			'gaddafi-forces': '#008542',
			'libyan-rebel-forces': '#c50000'
		},
		'libya-2011-strikes-timeline': {
			'nato-forces': '#012169',
			'gaddafi-forces': '#008542',
			'libyan-rebel-forces': '#c50000'
		},
		'civcas-per-president': {
			confirmed: '#81de9b',		
			fair: '#ceef83',
			weak: '#fdc461',
			contested: '#f98253',
			discounted: '#737373'
		},
		'civcas-grading-timeline': {
			confirmed: '#81de9b',		
			fair: '#ceef83',
			weak: '#fdc461',
			contested: '#f98253',
			discounted: '#737373'
		},
		'strikes-per-president': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'declared-strikes-per-president-coalition-iraq-syria': {
			iraq: '#22ce9c',
			syria: '#007a5c'
		},
		'militant-deaths-timeline': {
			'declared_strike': '#2980a5',
			'alleged_strike': '#cc5d88'
		},
		'declared-alleged-timeline': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'coalition-strikes-timeline': {
			iraq: '#22ce9c',
			syria: '#007a5c'
		},
		'militant-deaths-per-year-in-yemen': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'militant-deaths-per-year-in-somalia': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'coalition-weapons-releases-timeline': {
			'iraq-syria': '#14a07e'
		},
		'coalition-cumulative-strikes': {
			us: '#495b98',
			partners: '#348fcc'	
		},
		'coalition-isr-missions': {
			afghanistan: '#40537b',
			'iraq-syria': '#14a07e'
		},
		'coalition-strikes-iraq': {
			us: '#495b98',
			allied: '#348fcc'
		},
		'coalition-russia-alleged-incidents': {
			coalition: '#35495d',
			'russian-military': '#3b99d8'
		},
		'libya-strikes-timeline': {
			gna: '#000',
			gnc: '#999',
			lna: '#888',
			egypt: '#777',
			france: '#666',
			israel: '#555',
			uae: '#444',
			us: '#333',
			unknown: '#222',
		},
		'libya-civcas-belligerents-timeline': {
			gna: '#000',
			gnc: '#999',
			lna: '#888',
			egypt: '#777',
			france: '#666',
			israel: '#555',
			uae: '#444',
			us: '#333',
			unknown: '#222',
		},


		'strikes-by-us-president-in-somalia': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'declared-and-alleged-us-actions-in-yemen': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'declared-and-alleged-us-actions-in-somalia': {
			'declared_strike': '#29A597',
			'alleged_strike': '#8ece70'
		},
		'declared-strikes-by-us-president-in-iraq-and-syria': {
			iraq: '#22ce9c',
			syria: '#007a5c'
		},
		'declared-us-led-coalition-air-and-artillery-strikes-in-iraq-and-syria': {
			iraq: '#22ce9c',
			syria: '#007a5c'
		},
		'coalition-air-released-munitions-in-iraq-and-syria-2014-2020': {
			'iraq-syria': '#14a07e'
		}
	},
	gradingColors: {
		confirmed: '#81de9b',		
		fair: '#ceef83',
		weak: '#fdc461',
		contested: '#f98253',
		discounted: '#737373'
	}
};