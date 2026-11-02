export const refreshCart = () => {
    return;
    
    // @TODO
    $.ajax(
        site_settings.ajax_url,
        {
            method: 'GET',
            data: {
                action: 'cart_items_count'  // Use the action name
            },
            success: function(response) {
                console.log('Total items in cart:', response.total);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        }
    )
}

export const registerAjaxCart = () => {
    // Dismiss the "added to cart" toast.
    $('#cart-message-close').on('click', function () {
        $('#cart-message').stop(true, true).fadeOut(200);
    });

    $('.add-to-cart-btn').on('click', function () {
        let button = $(this);

        if (button.attr('disabled') === 'disabled') {
            return;
        }

        let originalText = button.find('span').text();  // Get original button text

        button.prop('disabled', true).find('span').text('Dodavanje...');

        let data = {};

        // Collect all data attributes from the button
        $.each(button.data(), function (key, value) {
            data[key] = value;
        });

        $.each(button[0].dataset, function (key, value) {
            data[key] = value;
        });

        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
            data: data,
            success: function (response) {
                if (response && response.cart_hash) {
                    $('#cart-message')
                        .stop(true, true)
                        .removeClass('hidden')
                        .hide()
                        .fadeIn(200)
                        .delay(4000)
                        .fadeOut(300);

                    button.find('span').text('Dodano!');

                } else {
                    button.find('span').text('Greška!');
                    button.addClass('bg-red-500').removeClass('hover:bg-primary/30');
                }
            },
            error: function () {
                button.find('span').text('Pokušajte ponovo');
                button.addClass('bg-red-500').removeClass('hover:bg-primary/30');
            },
            complete: function () {
                setTimeout(() => {
                    button.removeClass('bg-green-500 bg-red-500')

                    button.prop('disabled', false)
                        .find('span').text(originalText)
                        .addClass('hover:bg-primary/30');
                }, 3000);
            }
        });
    });
}

export const registerNumberFieldControls = () => {
    // Increment button
    $('button[data-input-counter-increment]').click(function () {
        let inputSelector = $(this).data('input-counter-increment');
        let $input = $(this).siblings(`input[id="${inputSelector}"]`);
        let currentValue = parseInt($input.val()) || 0;
        let max = $input.attr('max') ? parseInt($input.attr('max')) : Infinity;
        let step = $input.attr('step') ? parseInt($input.attr('step')) : 1;

        if (currentValue < max) {
            $input.val(currentValue + step);
        }
    });

    // Decrement button
    $('button[data-input-counter-decrement]').click(function () {
        let inputSelector = $(this).attr('data-input-counter-decrement');
        let $input = $(this).siblings(`input[id="${inputSelector}"]`);
        let currentValue = parseInt($input.val()) || 0;
        let min = $input.attr('min') ? parseInt($input.attr('min')) : -Infinity;
        let step = $input.attr('step') ? parseInt($input.attr('step')) : 1;

        if (currentValue > min) {
            $input.val(currentValue - step);
        }
    });
}