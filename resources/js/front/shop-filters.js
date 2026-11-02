// Mobile: collapse/expand the shop filter sidebar. On xl the panel is always
// visible (CSS), so the toggle button is hidden there.
export const registerShopFilters = () => {
	$('.shop-filters-toggle').on('click', function () {
		const button = $(this);
		const panel = $('#' + button.attr('aria-controls'));
		const open = !panel.hasClass('is-open');

		panel.toggleClass('is-open', open);
		button.attr('aria-expanded', open ? 'true' : 'false');
	});
};
