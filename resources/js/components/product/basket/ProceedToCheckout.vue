<template>
  <button class="checkout-btn" @click="proceedToCheckout">Checkout</button>
</template>

<script>
import { useCartStore } from '@/utils/cartStore';
import axios from 'axios';
import { router } from '@inertiajs/vue3'; // if using Inertia

export default {
  methods: {
    async proceedToCheckout() {
      const cart = useCartStore();

      try {
        const response = await axios.post('/checkout/start', {
          items: cart.items,
          promo: cart.promoCode,
        });

        // Redirect to checkout with a token or cart ID
        router.visit(`/checkout/${response.data.checkout_id}`);
      } catch (err) {
        alert('Something went wrong. Please try again.');
      }
    },
  },
};
</script>

<style>
.checkout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-colour);
    color: var(--text-primary);
    padding: 10px;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    border-radius: 4px;
}
</style>