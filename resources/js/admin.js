import { registerAdminNavigation } from './admin/nav';

window.$ = jQuery;

import { registerAjaxForms } from './admin/forms';
import { registerUnloadScript } from './admin/helper';
import registerMediaPickers from './admin/media-form-element';
import { registerAccordions } from './admin/accordion';
import { registerConditionalFields } from './admin/conditional';

$(document).ready(function () {
	registerAdminNavigation();
	registerAjaxForms();
	registerUnloadScript();
	registerMediaPickers();
	registerAccordions();
	registerConditionalFields();
});
