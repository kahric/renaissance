/**
 * GA4 e-commerce events for the WooCommerce Blocks cart and checkout.
 *
 * GTM4WP pushes view_cart / begin_checkout / purchase from PHP, but its
 * front-end script only knows the classic cart/checkout markup. With the
 * Blocks cart and checkout (React), remove_from_cart, add_shipping_info and
 * add_payment_info never reach the dataLayer. This bridge reads WooCommerce's
 * own data stores and pushes those events in the same shape GTM4WP uses.
 */

const STORE_CART = 'wc/store/cart';
const STORE_PAYMENT = 'wc/store/payment';
const STORE_CHECKOUT = 'wc/store/checkout';

function push(event, ecommerce) {
	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push({ ecommerce: null });
	window.dataLayer.push({ event, ecommerce });
}

// Items GTM4WP already pushed server-side (view_cart / begin_checkout), keyed
// by item_id — reused so our events carry the same names, brands, categories.
function gtm4wpItemsById() {
	const map = {};
	(window.dataLayer || []).forEach((entry) => {
		const items = entry && entry.ecommerce && entry.ecommerce.items;
		if (Array.isArray(items)) {
			items.forEach((item) => {
				if (item && item.item_id !== undefined) {
					map[String(item.item_id)] = item;
				}
			});
		}
	});
	return map;
}

function money(amount, minorUnit) {
	return Number(amount || 0) / Math.pow(10, Number(minorUnit || 0));
}

function toItem(storeItem, quantity, known) {
	const base = known[String(storeItem.id)];
	const price = money(storeItem.prices && storeItem.prices.price, storeItem.prices && storeItem.prices.currency_minor_unit);
	const item = base ? { ...base } : { item_id: storeItem.id, item_name: storeItem.name, price };
	item.quantity = quantity;
	return item;
}

function cartItems(cart, known) {
	return (cart.items || []).map((i) => toItem(i, i.quantity, known));
}

function currencyOf(cart) {
	return (cart.totals && cart.totals.currency_code) || window.gtm4wp_currency || 'EUR';
}

function valueOf(cart) {
	return money(cart.totals && cart.totals.total_items, cart.totals && cart.totals.currency_minor_unit);
}

function selectedShippingName(cart) {
	for (const pkg of cart.shippingRates || []) {
		for (const rate of pkg.shipping_rates || []) {
			if (rate.selected) {
				return rate.name;
			}
		}
	}
	return '';
}

function start(wp) {
	const select = wp.data.select;
	const isCheckout = !!document.querySelector('.wp-block-woocommerce-checkout');

	let lastItems = null; // key -> { storeItem, qty }
	let lastShipping = null;
	let lastPayment = null;
	let shippingSent = false;
	let paymentSent = false;
	let wasProcessing = false;

	const snapshot = (cart) => {
		const map = {};
		(cart.items || []).forEach((i) => { map[i.key] = { storeItem: i, qty: i.quantity }; });
		return map;
	};

	const sendShipping = (cart) => {
		const tier = selectedShippingName(cart);
		push('add_shipping_info', { currency: currencyOf(cart), value: valueOf(cart), shipping_tier: tier, items: cartItems(cart, gtm4wpItemsById()) });
		shippingSent = true;
	};

	const sendPayment = (cart, method) => {
		push('add_payment_info', { currency: currencyOf(cart), value: valueOf(cart), payment_type: method, items: cartItems(cart, gtm4wpItemsById()) });
		paymentSent = true;
	};

	wp.data.subscribe(() => {
		const cartStore = select(STORE_CART);
		if (!cartStore || cartStore.isResolving && cartStore.isResolving('getCartData')) {
			return;
		}
		const cart = cartStore.getCartData();
		if (!cart || !Array.isArray(cart.items)) {
			return;
		}

		// remove_from_cart: an item disappeared or its quantity went down.
		// add_to_cart: the quantity of an item already in the cart went up.
		// (Items newly added from cross-sells are left alone: GTM4WP catches
		// those clicks itself, so reporting them here would count them twice.)
		const now = snapshot(cart);
		if (lastItems !== null) {
			const known = gtm4wpItemsById();
			Object.keys(lastItems).forEach((key) => {
				const before = lastItems[key];
				const afterQty = now[key] ? now[key].qty : 0;
				if (afterQty === before.qty) {
					return;
				}
				const delta = Math.abs(afterQty - before.qty);
				const item = toItem(before.storeItem, delta, known);
				const event = afterQty < before.qty ? 'remove_from_cart' : 'add_to_cart';
				push(event, { currency: currencyOf(cart), value: (item.price || 0) * delta, items: [item] });
			});
		}
		lastItems = now;

		if (!isCheckout) {
			return;
		}

		// add_shipping_info: the customer picks a different shipping option.
		const shipping = selectedShippingName(cart);
		if (lastShipping !== null && shipping && shipping !== lastShipping) {
			sendShipping(cart);
		}
		lastShipping = shipping;

		// add_payment_info: the customer picks a different payment method.
		const paymentStore = select(STORE_PAYMENT);
		const method = paymentStore && paymentStore.getActivePaymentMethod ? paymentStore.getActivePaymentMethod() : '';
		if (lastPayment !== null && method && method !== lastPayment) {
			sendPayment(cart, method);
		}
		lastPayment = method;

		// On "place order": make sure both steps were reported once.
		const checkoutStore = select(STORE_CHECKOUT);
		const processing = !!(checkoutStore && (checkoutStore.isBeforeProcessing() || checkoutStore.isProcessing()));
		if (processing && !wasProcessing) {
			if (!shippingSent && cart.needsShipping !== false) {
				sendShipping(cart);
			}
			if (!paymentSent) {
				sendPayment(cart, method);
			}
		}
		wasProcessing = processing;
	});
}

export function registerBlocksEcommerce() {
	if (!document.querySelector('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout')) {
		return;
	}
	// The Blocks scripts register their stores after ours runs; wait for them.
	let tries = 0;
	const timer = setInterval(() => {
		const wp = window.wp;
		if (wp && wp.data && wp.data.select && wp.data.select(STORE_CART)) {
			clearInterval(timer);
			start(wp);
		} else if (++tries > 100) {
			clearInterval(timer);
		}
	}, 100);
}
