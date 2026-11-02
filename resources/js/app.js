import { registerMainMenu } from './front/nav';

window.$ = jQuery;

import {
	refreshCart,
	registerAjaxCart,
	registerNumberFieldControls,
} from './front/cart';
import { registerSwiper } from './front/swiper';
import { registerNewsletterForm } from './front/newsletter';
import { registerShopFilters } from './front/shop-filters';
import { registerBlocksEcommerce } from './front/blocks-ecommerce';

$(document).ready(function () {
	registerNewsletterForm();
	registerMainMenu();
	registerSwiper();
	registerAjaxCart();
	registerNumberFieldControls();
	refreshCart();
	registerShopFilters();
	registerBlocksEcommerce();
});

module.exports = [];
