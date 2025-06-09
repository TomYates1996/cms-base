<template>
    <button class="add-to-cart-btn" @click="addToCart()"><font-awesome-icon :icon="['fas', 'basket-shopping']" />Add To Cart</button>
</template>

<script>
import { useCartStore } from '@/utils/cartStore';

export default {
  name: 'AddToCartButton',
  props: {
      product: Object,
      variant: Object,
      item: Object,
      quantity: Number,
  },
  data() {
    return {
    };
  },
  methods: {
        addToCart() {
            if (!this.item) {
                alert(`We're sorry, there was an error adding this product.`);
                return;
            }

            const cart = useCartStore();

            

            const cartItem = {
                product_id: this.product.id,
                variant_id: this.variant.id,
                item_id: this.item.id,
                name: this.variant.name,
                item_label: this.item.size_label,
                sku: this.item.sku,
                price: this.item.price,
                quantity: this.quantity,
                thumbnail: this.variant.thumbnail_image || '', 
                available: this.item.stock_quantity,
            };

            cart.addToCart(cartItem);
        },
  }
};
</script>

<style>
    .add-to-cart-btn {
        background-color: var(--yellow);
        border-radius: 5px;
        padding: 4px 10px;
        display: flex;
        gap: 5px;
        align-items: center;
    }
</style>
