const CART_KEY = 'my_cart';

export default {
  getCart() {
    const cart = localStorage.getItem(CART_KEY);
    return cart ? JSON.parse(cart) : [];
  },

  saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  },

  addToCart(item) {
    const cart = this.getCart();
    const existing = cart.find(
      i => i.item_id === item.item_id
    );

    if (existing) {
      existing.quantity += item.quantity;
    } else {
      cart.push(item);
    }

    this.saveCart(cart);
  },

  removeFromCart(item_id) {
    const cart = this.getCart().filter(i => i.item_id !== item_id);
    this.saveCart(cart);
  },

  clearCart() {
    localStorage.removeItem(CART_KEY);
  }
};
