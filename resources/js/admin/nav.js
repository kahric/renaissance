export const registerAdminNavigation = () => {
	changeSection(getSectionFromUrl() ?? 'general');

	$('.theme-admin-nav div[data-section]').click(function () {
		const section = $(this).data('section');

		changeSection(section);
	});
};

export const changeSection = (section) => {
	$('.theme-admin-sections div[data-section]').addClass('hidden');
	$(`.theme-admin-sections div[data-section="${section}"]`).removeClass(
		'hidden'
	);

	$('.theme-admin-nav [data-section]').removeClass('is-active');
	$(`.theme-admin-nav [data-section="${section}"]`).addClass('is-active');

	window.location.hash = section;
};

function getSectionFromUrl() {
	const hash = window.location.hash;

	if (hash.length === 0) {
		return null;
	}

	return hash.slice(1);
}
