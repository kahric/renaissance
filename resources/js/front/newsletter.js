export const registerNewsletterForm = () => {
    document.getElementById('newsletter-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value;

        try {
            const response = await fetch(window.postUrl + '?action=subscribe_newsletter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ email: email })
            });

            const result = await response.text();

            if (result.includes('already_subscribed')) {
                alert('Već ste prijavljeni na newsletter.');
            } else if (result.includes('subscribed')) {
                alert('Uspješno ste se prijavili! Provjerite svoj email za kod za popust.');
            } else {
                alert('Došlo je do pogreške. Pokušajte ponovno kasnije.');
            }
        } catch (error) {
            console.error('Greška:', error);
            alert('Došlo je do pogreške. Pokušajte ponovno kasnije.');
        }
    });
}