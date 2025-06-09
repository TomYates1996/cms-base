<template>
    <div class="basket-icon">
        <div class="shopping-trolley" @mouseover="showMiniBasket = true">
            <font-awesome-icon :icon="['fas', 'cart-shopping']" />
            <p class="item-count">{{ itemCount }}</p>
        </div>
        <!-- <p class="price-total">£{{ cart.totalPrice?.toFixed(2) || '' }}</p>    -->
        <transition name="fade">
            <MiniBasket v-show="showMiniBasket" @mouseover="pauseMiniBasketTimeout()" @mouseleave="showMiniBasket = false"/>
        </transition>
    </div>
</template>

<script>
import { useCartStore } from '@/utils/cartStore';
import MiniBasket from './MiniBasket.vue';

export default {
    data() {
        return {
            showMiniBasket: false,
            basketTimeout: [],
        }
    },
    components: {
        MiniBasket,
    },
    computed: {
        cart() {
            return useCartStore(); 
        },
        itemCount() {
            return this.cart.itemCount; 
        }
    },
    methods: {
        toggleShowMiniBasket() {
            this.showMiniBasket = !this.showMiniBasket;
        },
        pauseMiniBasketTimeout() {
            clearTimeout(this.basketTimeout);
        },
    },
    watch: {
        // whenever question changes, this function will run
        itemCount(newItemCount, oldItemCount) {
            this.showMiniBasket = true;
            this.basketTimeout = setTimeout(this.toggleShowMiniBasket, 5000)
        }
    },
};
</script>


<style>
.basket-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    position: relative;
}
.shopping-trolley {
    position: relative;
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    width: fit-content;
    svg {
        font-size: 1rem;
        padding-right: 10px;
    }
    .item-count {
        position: absolute;
        top: 0px;
        right: 0px;
        transform: translate(50%, -50%);
        padding: 0px 4px;
        background-color: var(--bright-red);
        border-radius: 100px;
        color: var(--white);
        font-size: .875rem;
        font-weight: 600;
    }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>