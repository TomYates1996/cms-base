<template>
    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>

    <p>Order: {{ order.order_number }}</p>
    <!-- <p>Price Paid: {{ order.price }}</p> -->
    <p>Thank you {{ order.first_name }} {{ order.last_name }} for your order <br>Expect Us</p>
    <p>Status: {{ order.status }}</p>

    <Footer v-if="footer" :footer="footer" :pages="pages" />
</template>

<script>
import Footer from '@/components/nav/Footer.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import { useCartStore } from '@/utils/cartStore';


export default {
    props: {
        order: Object,
        header: Object,
        pages: Object,
        footer: Object,
        widgets: Array,
    },
    components: {
        HamburgerHeader,
        Footer
    },
    computed: {
        cart() {
            return useCartStore(); 
        },
    },
    created() {
        this.cart.clearCart();
    },
}
</script>

<style>

</style>