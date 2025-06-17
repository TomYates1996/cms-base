<template>
  <div>
    <div class="payment-method">
      <label>
        <input
          type="radio"
          value="card"
          v-model="selectedPayment"
        />
        Pay with Card
      </label>

      <label>
        <input
          type="radio"
          value="paypal"
          v-model="selectedPayment"
        />
        Pay with PayPal
      </label>
    </div>

    <!-- CARD PAYMENT -->
    <div v-if="selectedPayment === 'card'">
        <form @submit.prevent="submitCardPayment" class="accordion-content payment-form">
            <div class="form-input">
                <input id="cardNumber" v-model="payment.cardNumber" required />
                <label v-if="payment.cardNumber === ''" for="cardNumber">Card Number</label>
            </div>
            <div class="form-input">
                <input id="expiry" v-model="payment.expiry" required />
                <label v-if="payment.expiry === ''" for="expiry">Expiry</label>
            </div>
            <div class="form-input">
                <input id="cvc" v-model="payment.cvc" required />
                <label v-if="payment.cvc === ''" for="cvc">CVC</label>
            </div>
            <button type="submit" class="pay-btn">Pay</button>
        </form>
    </div>


    <!-- PAYPAL PAYMENT -->
    <div v-else-if="selectedPayment === 'paypal'">
      <div id="paypal-button-container"></div>
    </div>

    <!-- <button @click="goToStep(1)">Back</button> -->
  </div>
</template>

<script>
export default {
  props: {
    cart: Object,
    customerDetails: Object,
  },
  data() {
    return {
      selectedPayment: 'card',
      payment: {
        cardNumber: '',
        expiry: '',
        cvc: '',
      },
    };
  },
  methods: {
    goToStep(step) {
      this.$emit('step-change', step);
    },
    submitCardPayment() {
      console.log('Paying with card:', this.payment);
    },
    loadPayPalScript() {
      return new Promise((resolve, reject) => {
        if (window.paypal) {
          resolve();
          return;
        }
        const script = document.createElement('script');
        script.src = "https://www.paypal.com/sdk/js?client-id=ASAYlCX0oaD2qVTkqCKQOb6byT5qbKPTcRnBnL5h2dG7OavX1oXYiKLpyIWyEo78utBVHjhChVcSR_qo&currency=GBP";
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('PayPal SDK failed to load.'));
        document.head.appendChild(script);
      });
    },
    renderPayPalButton() {
      const container = document.getElementById('paypal-button-container');
      if (container) container.innerHTML = '';

      window.paypal.Buttons({
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: this.cart.total.toFixed(2),
              },
            }],
          });
        },
        onApprove: (data, actions) => {
          return actions.order.capture().then(details => {
                const payload = {
                    orderID: data.orderID,
                    items: this.cart.items.map(item => ({
                        id: item.item_id,
                        quantity: item.quantity,
                    })),
                    promoCode: this.cart.promoCode || null,
                    shipping: this.cart.total > 50 ? 0 : 3.5,

                    first_name: this.customerDetails.firstName,
                    surname: this.customerDetails.surname,
                    email: this.customerDetails.email,
                    phone: this.customerDetails.phone,
                    address1: this.customerDetails.address1,
                    address2: this.customerDetails.address2,
                    city: this.customerDetails.city,
                    postcode: this.customerDetails.postcode,
                    country: 'England',
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/paypal/capture-order', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
              },
              body: JSON.stringify(payload),
            })
            .then(async (res) => {
              const text = await res.text();
              let data;
              try {
                data = JSON.parse(text);
              } catch (e) {
                throw new Error('Server did not return JSON: ' + text);
              }

              if (res.ok && data.status === 'success') {
                this.$inertia.visit(`/order-confirmation/${data.order_number}`);  
              } else {
                alert('Payment failed: ' + (data.error || 'Unknown error'));
              }
            })
            .catch(err => {
              console.error(err);
              alert('An error occurred.');
            });
          });
        },
        onError: (err) => {
          console.error(err);
          alert('There was an error processing your PayPal payment.');
        },
      }).render('#paypal-button-container');
    },
    async setupPayPal() {
      try {
        await this.loadPayPalScript();
        this.renderPayPalButton();
      } catch (error) {
        console.error(error);
      }
    },
  },
  watch: {
    selectedPayment(newVal) {
      if (newVal === 'paypal') {
        this.$nextTick(() => {
          this.setupPayPal();
        });
      }
    },
  },
  mounted() {
    if (this.selectedPayment === 'paypal') {
      this.setupPayPal();
    }
  },
};
</script>

<style>
.payment-form {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    width: 100%;
}
.form-input {
    display: flex;
    flex-direction: column;
    width: 100%;
    border-bottom: 1px solid var(--black);
    position: relative;
    input {

    }
    label {
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s ease;
        position: absolute;
        pointer-events: none;
    }
    input:focus ~ label {
        font-size: .75rem;
    }
}
.payment-method {
    padding-bottom: 20px;
    display: flex;
    gap: 20px;
}
.pay-btn {
    background-color: var(--primary-colour);
    max-width: 250px;
    color: var(--text-primary);
    padding: 8px;
    border-radius: var(--radius-mid);
    grid-column: 1;
}
</style>