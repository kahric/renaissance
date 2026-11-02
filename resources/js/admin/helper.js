export const resetSettingsNonce = () => {
    $.get(
        ajaxurl,
        {'action': 'theme_get_nonce'},
        (response) => $( "#settings-nonce" ).val(response.nonce),
    );
}

export const registerUnloadScript = () => {
    window.onbeforeunload = () => {
        if ('ask_before_unload' in window && window.ask_before_unload) {
            return "Izaći bez spremanja podataka?";
        }
    }
}