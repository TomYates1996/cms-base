import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        promoCode: '',
        discountPercent: 100,
    }),
    getters: {
        itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
        totalPrice: (state) =>
        state.items.reduce((sum, item) => sum + item.price * item.quantity, 0),
        shipping: (state) => state.totalPrice > 50 ? 0 : 3.50,
    },
    actions: {
        initCart() {
            const stored = localStorage.getItem('my_cart');
            if (stored) {
                this.items = JSON.parse(stored);
            }
        },
        addToCart(product) {
            const existing = this.items.find(i => i.item_id === product.item_id);
            if (existing) {
                existing.quantity += product.quantity || 1;
            } else {
                this.items.push({ ...product, quantity: product.quantity || 1 });
            }
            this.persist();
        },
        removeItem(item_id) {
            this.items = this.items.filter(item => item.item_id !== item_id);
            this.persist();
        },
        clearCart() {
            this.items = [];
            this.persist();
        },
        persist() {
            localStorage.setItem('my_cart', JSON.stringify(this.items));
        },
        setPromoCode(code, percent) {
            this.promoCode = code;
            this.discountPercent = percent;
        },
    },
});
