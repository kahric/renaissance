export const registerAccordions = () => {
	$('.ren-accordion [data-accordion-toggle]').on('click', function () {
		$(this).closest('.ren-accordion').toggleClass('is-open');
	});
};
