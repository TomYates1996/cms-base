<template>
    <button class="checkout-btn" :class="cart.itemCount === 0 ? 'disabled' : ''" @click="proceedToCheckout" :disabled="cart.itemCount === 0">
        Checkout
    </button>
</template>

<script>
import { useCartStore } from '@/utils/cartStore';
import axios from 'axios';

export default {
    data() {
      return {
        cart: useCartStore()
      }
    },
    methods: {
      async proceedToCheckout() {
        try {
          const response = await axios.post('/checkout/start', {
            items: this.cart.items,
            promo: this.cart.promoCode,
          });

          // Use Inertia from this.$inertia to redirect
          this.$inertia.visit(`/checkout/${response.data.checkout_id}`);
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
.checkout-btn.disabled {
    opacity: 0.4;
}
</style>