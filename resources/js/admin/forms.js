import { resetSettingsNonce } from './helper';

// Update the button label without wiping its icon (label lives in [data-btn-label]).
const setBtnLabel = ($btn, text) => {
	const label = $btn.find('[data-btn-label]');
	(label.length ? label : $btn).text(text);
};

const submitResponseHandlers = {
	theme_settings_update: (data, $btn) => {
		setBtnLabel($btn, 'Uspješno spremljeno!');
		setTimeout(
			() =>
				setBtnLabel($btn, 'Spremi izmjene') &&
				$btn.removeAttr('disabled'),
			1500
		);
		$btn.removeAttr('disabled');
	},
};

export const registerAjaxForms = () => {
	$('[data-ajax-form]').each(function () {
		const form = $(this);
		const $btn = form.find('button[type="submit"]');
		const action = form.data('ajax-form');

		form.change(function () {
			window.ask_before_unload = true;
		});

		this.addEventListener('submit', function (event) {
			event.preventDefault();

			$btn.attr('disabled', 'disabled');
			setBtnLabel($btn, 'Spremanje u toku...');

			let data = $(this)
				.serializeArray()
				.concat(
					form
						.find('input[type=checkbox]:not(:checked)')
						.map(function () {
							return { name: this.name, value: 'off' };
						})
						.get()
				);

			const handler = submitResponseHandlers[action] ?? null;

			$.ajax(ajaxurl, {
				method: 'POST',
				data: { action, data },
				success: (response) => {
					typeof handler === 'function' && handler(response, $btn);
					resetSettingsNonce();
					window.ask_before_unload = false;
				},
				error: () => {
					setBtnLabel($btn, 'Greška pri spremanju');
					$btn.removeAttr('disabled');
					resetSettingsNonce();
				},
			});
		});
	});
};
