<template>
    <div class="checkout-accordion">

        <!-- Step 1 -->
        <section :class="['accordion-step', { open: step === 1 }]">
            <header @click="step = 1" class="accordion-header">
                <h3>Delivery Address</h3>
                <MiniAddress v-if="detailsComplete" :details="customerDetails" />
                <font-awesome-icon :icon="['fas', step === 1 ? 'caret-up' : 'caret-down']" />
            </header>
            <form v-if="step === 1" @submit.prevent="goToStep(2)" class="accordion-content delivery-address">
                <div class="form-input">
                    <input id="firstName" v-model="customerDetails.firstName" required />
                    <label v-if="customerDetails.firstName === ''" for="firstName">First Name</label>
                </div>
                <div class="form-input">
                    <input id="surname" v-model="customerDetails.surname" required />
                    <label v-if="customerDetails.surname === ''" for="surname">Surname</label>
                </div>
                <div class="form-input">
                    <input id="email" v-model="customerDetails.email" type="email"  required />
                    <label v-if="customerDetails.email === ''" for="email">Email</label>
                </div>
                <div class="form-input">
                    <input id="phone" v-model="customerDetails.phone" required />
                    <label v-if="customerDetails.phone === ''" for="phone">Phone</label>
                </div>
                <div class="form-input">
                    <input id="address1" v-model="customerDetails.address1" required />
                    <label v-if="customerDetails.address1 === ''" for="address1">Address Line 1</label>
                </div>
                <div class="form-input">
                    <input id="address2" v-model="customerDetails.address2" />
                    <label v-if="customerDetails.address2 === ''" for="address2">Address Line 2</label>
                </div>
                <div class="form-input">
                    <input id="city" v-model="customerDetails.city" required />
                    <label v-if="customerDetails.city === ''" for="city">City/Town</label>
                </div>
                <div class="form-input">
                    <input id="postcode" v-model="customerDetails.postcode" required />
                    <label v-if="customerDetails.postcode === ''" for="postcode">Postcode</label>
                </div>
                <button class="next-btn" type="submit" :disabled="!detailsComplete">Continue</button>
            </form>
        </section>

        <!-- Step 2 -->
        <section :class="['accordion-step', { open: step === 2 }]">
            <header @click="step = 2" class="accordion-header">
                <h3>Payment</h3>
                <font-awesome-icon :icon="['fas', step === 2 ? 'caret-up' : 'caret-down']" />
            </header>
            <div v-if="step === 2" class="accordion-content">
                <PaymentStep :cart="cart" :customerDetails="customerDetails" @step-change="step = 1"/>
            </div>
        </section>

    </div>
</template>

<script>
import MiniAddress from './MiniAddress.vue';
import PaymentStep from './PaymentStep.vue';
import axios from 'axios';


export default {
    components: {
        PaymentStep,
        MiniAddress,
    },
    props: {
        cart: Object,
    },
    data() {
        return {
            step: 1,
            customerDetails: {
                firstName: '',
                surname: '',
                address1: '',
                address2: '',
                city: '',
                postcode: '',
                email: '',
                phone: '',
            },
            addresses: [],
        };
    },
    computed: {
        detailsComplete() {
            if (this.customerDetails.firstName !== '' && this.customerDetails.surname !== '' && this.customerDetails.address1 !== '' && this.customerDetails.address2 !== '' && this.customerDetails.city !== '' &&  this.customerDetails.email !== '' && this.customerDetails.postcode !== '' &&  this.customerDetails.phone !== '') {
                return true;
            }
            return false;
        },
    },
    methods: {
        goToStep(nextStep) {
            this.step = nextStep;
        },
        submitOrder() {
            const payload = {
                address: this.address,
                payment: this.payment,
                cart: this.cart,
            };
            this.$axios.post('/checkout/submit', payload).then(response => {
                this.$router.push('/thank-you');
            });
        },
        // async fetchAddresses() {
        //     if (this.postcode.length < 5) return; 

        //     try {
        //         const response = await axios.get(`/api/addresses?postcode=${this.postcode}`);
        //         console.log(response);
                
        //         this.addresses = response.data;
        //     } catch (err) {
        //         console.error('Error fetching addresses', err);
        //     }
        // },
        // fillAddressFields() {
        //     if (this.selectedAddress) {
        //         this.customerDetails.address1 = this.selectedAddress.line1;
        //         this.customerDetails.city = this.selectedAddress.city;
        //     }
        // },
    },
};
</script>

<style scoped>
.checkout-accordion {
    border-radius: 4px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.accordion-step {
    border: 1px solid #ccc;
}
.accordion-header {
    cursor: pointer;
    background: #f7f7f7;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
}
.accordion-content {
    padding: 15px;
    background: #fff;
}
.accordion-step.open .accordion-content {
    display: block;
}
.accordion-content {
    display: none;
}

.accordion-step.open .delivery-address {
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
.next-btn {
    background-color: var(--primary-colour);
    max-width: 250px;
    color: var(--text-primary);
    padding: 8px;
    border-radius: var(--radius-mid);
}
</style>