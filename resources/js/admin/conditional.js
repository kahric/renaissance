// Show/hide fields that declare `data-show-if-field` / `data-show-if-value`.
// value "*" means "show when the controlling field is non-empty".
export const registerConditionalFields = () => {
	document.querySelectorAll('[data-ajax-form]').forEach((form) => {
		const conditionals = form.querySelectorAll('[data-show-if-field]');

		if (!conditionals.length) {
			return;
		}

		const evaluate = () => {
			conditionals.forEach((el) => {
				const fieldName = el.getAttribute('data-show-if-field');
				const want = el.getAttribute('data-show-if-value');
				const ctrl = form.querySelector(`[name="${fieldName}"]`);

				if (!ctrl) {
					return;
				}

				const value =
					ctrl.type === 'checkbox'
						? ctrl.checked
							? 'on'
							: 'off'
						: (ctrl.value || '').trim();

				const show = want === '*' ? value !== '' : value === want;

				el.classList.toggle('ren-hidden', !show);
			});
		};

		form.addEventListener('change', evaluate);
		form.addEventListener('input', evaluate);
		evaluate();
	});
};
