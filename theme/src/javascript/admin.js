document.addEventListener('DOMContentLoaded', () => {
	document.addEventListener('wheel', function(event){
		if(document.activeElement.type === 'number'){
			document.activeElement.blur();
		}
	});


	const disableCheckboxSelectors = [
		'taxonomy-casualty',
		'taxonomy-infrastructure',
		'taxonomy-infrastructure_affiliation',
		'taxonomy-infrastructure_declared_target',
		'taxonomy-infrastructure_destruction',
		'taxonomy-profession',
		'taxonomy-protected_persons',
	]



	disableCheckboxSelectors.forEach(disableCheckboxSelector => {
		document.querySelectorAll(`#${disableCheckboxSelector} input[type=checkbox]`).forEach(checkbox => {
			console.log(checkbox)
			checkbox.disabled = true
		})
	})
});