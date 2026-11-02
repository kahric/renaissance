export const registerMainMenu = () => {
	const isDesktop = () => window.innerWidth >= 1024;

	// Mobile hamburger toggle.
	$('#primary-menu-toggle').on('click', function (e) {
		e.preventDefault();
		$('#primary-menu').slideToggle(150);
	});

	// Mobile only: tap a parent to expand its submenu (accordion).
	// On desktop the submenu opens on hover via CSS and the link navigates normally.
	$('a[data-has-children="true"]').on('click', function (e) {
		if (isDesktop()) {
			return;
		}

		e.preventDefault();
		const link = $(this);
		link.toggleClass('active');
		link.siblings('.submenu').slideToggle(150);
	});
};
