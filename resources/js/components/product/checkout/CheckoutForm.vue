<template>
    <div>
        <h2>Checkout</h2>

        <div v-if="step === 1">
        <h3>1. Delivery Address</h3>
        <form @submit.prevent="goToStep(2)">
            <input v-model="address.first_name" placeholder="First Name" required />
            <input v-model="address.surname" placeholder="Surname" required />
            <input v-model="address.line1" placeholder="Address Line 1" required />
            <input v-model="address.city" placeholder="City/Town" required />
            <input v-model="address.postcode" placeholder="Postcode" required />
            <button type="submit">Continue to Payment</button>
        </form>
        </div>

        <div v-else-if="step === 2">
            <PaymentStep :cart="cart"/>
        </div>
    </div>
</template>

<script>
import PaymentStep from './PaymentStep.vue';

export default {
    components: {
        PaymentStep,
    },
    props: {
        cart: Object,
    },
    data() {
        return {
        step: 1,
        address: {
            name: '',
            line1: '',
            city: '',
            postcode: '',
        },
        payment: {
            cardNumber: '',
            expiry: '',
            cvc: '',
        },
        };
    },
    methods: {
        goToStep(n) {
        this.step = n;
        },
        submitOrder() {
        const payload = {
            address: this.address,
            payment: this.payment,
            cart: this.cart,
        };
        // POST to Laravel endpoint for order creation
        this.$axios.post('/checkout/submit', payload).then(response => {
            // Show success, redirect to confirmation, etc.
            this.$router.push('/thank-you');
        });
        },
    },
};
</script>
