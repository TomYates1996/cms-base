<template>
    <div class="mini-basket">
        <h2 class="mini-basket-title">Your Basket</h2>
        <ul>
            <li class="basket-item" v-for="(item, index) in cart.items" :key="index">
                <ResponsiveImage :slide="formatImageToFit(item.thumbnail)" :aspectRatios="aspectRatio" />
                <div class="text-side">
                    <p class="sku">Product Code: {{ item.sku }}</p>
                    <p class="item-name">{{ item.name }} - {{ item.item_label }}</p>
                    <div class="item-footer">
                        <p class="quantity">Quantity: {{ item.quantity }}</p>
                        <p class="price">£{{ (item.price * item.quantity).toFixed(2) }}</p>
                    </div>
                </div>
            </li>
        </ul>
        <p v-if="cart.itemCount === 0">Your basket is empty</p>
        <Link 
            href="/basket"
            method="get"
            class="checkout-btn"
        >
            View Basket
        </Link>
    </div>
</template>

<script>
import ResponsiveImage from '@/components/widgets/components/ResponsiveImage.vue';
import { useCartStore } from '@/utils/cartStore';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        ResponsiveImage,
        Link,
    },
    data() {
        return {
            aspectRatio: [
                { width: 70, height: 70, at: 1440 },
            ],
        }
    },
    computed: {
        cart() {
            return useCartStore(); 
        },
    },
    methods: {
        formatImageToFit(image) {
            const slide = {
                image: {
                image_path: '/' + image,
                },
            };
            return slide;
        },
    },
}
</script>

<style>
.mini-basket {
    background-color: var(--white);
    padding: 10px;
    box-shadow: 0px 1px 2px #00000031;
    position: absolute;
    top: 100%;
    right: 0px;
    width: 340px;
    z-index: 9;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 14px;
    ul {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 10px;
        .basket-item {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            width: 100%;
            gap: 10px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 1px 2px #00000031;
            .text-side {
                .sku {
                    font-size: .75rem;
                    font-weight: 600;
                }
                .item-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: baseline;
                    .quantity {
                        font-size: 12px;
                        font-weight: 600;
                    }
                    .price {
                        color: var(--bright-red);
                        font-weight: 600;
                        font-size: 1rem;
                    }
                }
            }
            .image-wrapper {
                border-radius: 6px;
                overflow: hidden;
                border: 1px solid var(--black);
            }
        }
    }
    .checkout-btn {
        background-color: var(--primary-colour);
        color: var(--text-primary);
        width: 100%;
        padding: 8px;
        display: flex;
        justify-content: center;
    }
}
</style>