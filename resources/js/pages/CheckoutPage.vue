<template>
    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo" />

    <main class="checkout-page">
        <section class="order-summary" aria-labelledby="order-summary-header">
            <h3 id="order-summary-header">Order Summary</h3>
            <h4>Items:</h4>
            <ul class="cart-items">
              <li class="summary-item" v-for="item in cart.items" :key="item.id">
                <p class="item-name">{{ item.name }} x {{ item.quantity }}</p>
                <p class="item-total"> £{{ (item.price * item.quantity).toFixed(2) }}</p>
              </li>
            </ul>
            <!-- <p v-if="cart.discount > 0">Discount: {{ cart.discount }}%</p> -->
            <div class="summary-item shipping">
              <p class="label">Shipping: </p>
              <p class="cost">£3.50</p>
            </div>
            <div class="summary-item total">
              <p class="label">Total:</p>
              <p class="cost">£{{ cart.total.toFixed(2) }}</p>
            </div>
        </section>

        <CheckoutForm :cart="cart" />
    </main>

    <Footer v-if="footer" :footer="footer" :pages="pages" />
</template>

<script>
    import Footer from '@/components/nav/Footer.vue';
    import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
    import CheckoutForm from '@/components/product/checkout/CheckoutForm.vue';

    export default {
        props: {
            cart: Object,
            pages: Object,
            header: Object,
            footer: Object,
        },
        components: {
            CheckoutForm,
            HamburgerHeader,
            Footer,
        },
    };
</script>

<style>
    .checkout-page {
      max-width: var(--width-max);
      margin: 0px auto;
      padding: 20px;
      display: flex;
      flex-direction: column-reverse;
      width: 100%;
      gap: 40px;
      .order-summary {
          min-width: 300px;
          gap: 8px;
          display: flex;
          flex-direction: column;
          border: 1px solid var(--primary-colour);
          padding: 10px 16px;
          .cart-items {
            border: 1px solid var(--black);
            padding: 6px 4px;
            border-radius: var(--radius-mid);
          }
          .summary-item {
            display: flex;
            justify-content: space-between;
            &.total {
              font-weight: 600;
              font-size: 1.125rem;
              border-top: 1px solid var(--black);
              padding-top: 8px;
            }
          }
      }
    }
    @media screen and (min-width: 64em) {
      .checkout-page {
        flex-direction: row-reverse;
        gap: 20px;
      }
    }
</style>