<template>
  <div>
    <h2>Checkout</h2>
    <p>Total: £{{ cart.total.toFixed(2) }}</p>
    <p>Discount: {{ cart.discount }}%</p>
    <ul>
      <li v-for="item in cart.items" :key="item.id">
        {{ item.name }} x{{ item.quantity }} - £{{ (item.price * item.quantity).toFixed(2) }}
      </li>
    </ul>
  </div>
  <CheckoutForm :cart="cart"/>
</template>

<script>
import CheckoutForm from '@/components/product/checkout/CheckoutForm.vue';

export default {
  props: {
    cart: Object,
  },
  components: {
    CheckoutForm,
  },
};
</script>