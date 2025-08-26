// document.addEventListener('DOMContentLoaded', async () => {
//     const stripe = Stripe(stripePublicKey);
//     const elements = stripe.elements();
//     const card = elements.create('card');
//     card.mount('#card-element');

//     const form = document.getElementById('payment-form');
//     form.addEventListener('submit', async (e) => {
//         e.preventDefault();

//         const {token, error} = await stripe.createToken(card);

//         if (error) {
//             document.getElementById('card-errors').textContent = error.message;
//         } else {
//             document.getElementById('stripeToken').value = token.id;
//             form.submit();
//         }
//     });
// });


document.addEventListener('DOMContentLoaded', async () => {
    const stripe = Stripe(stripePublicKey);
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('payment-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Get the name from the input fields
        const firstName = document.querySelector('[name="first_name"]').value;
        const lastName = document.querySelector('[name="last_name"]').value;
        const fullName = `${firstName} ${lastName}`;

        // Create token with name
        const { token, error } = await stripe.createToken(card, {
            name: fullName,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
        } else {
            // Insert token into hidden input and submit the form
            document.getElementById('stripeToken').value = token.id;
            form.submit();
        }
    });
});


//================== Fields Error handling ==================//
document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');

    emailInput.addEventListener('keyup', function () {
        const email = emailInput.value.trim();
        const validEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        console.log('Email:', email, 'Valid?', validEmail);

        if (!validEmail && email !== '') {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    phoneInput.addEventListener('keyup', function () {
        const phone = phoneInput.value.trim();
        const validPhone = /^[0-9]{10,15}$/.test(phone);
        console.log('Phone:', phone, 'Valid?', validPhone);

        if (!validPhone && phone !== '') {
            phoneError.style.display = 'block';
        } else {
            phoneError.style.display = 'none';
        }
    });
});



//================== End Fields Error handling ==================//

