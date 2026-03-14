
export function getDateFromValue(range, value, format) {
	// let days = Math.round(range.days * (value/100));
	let days = value;
	let min = range.min.clone();
	min.add(days, 'days');

	if (format) {
		return min.format('DD MMM YYYY');	
	} else {
		return min;
	}
}


