<template>
    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>

    <div class="page-content">
        <div class="basket-box">
            <ul>
                <li class="basket-item" v-for="(item, index) in cart.items" :key="index">
                    <ResponsiveImage :slide="formatImageToFit(item.thumbnail)" :aspectRatios="aspectRatio" />
                    <div class="text-side">
                        <div class="title-sku">
                            <p class="sku">Product Code: {{ item.sku }}</p>
                            <p class="item-name">{{ item.name }} - {{ item.item_label }}</p>
                             <p v-if="item.quantity > item.available" class="quantity-available yellow">
                                Insufficient stock, {{ item.available }} available.  <br>
                                ETA for {{ item.quantity }} quantity is 4 days</p>
                        </div>
                        <div class="quantity-input-wrap">
                            <button class="quantity-btn quantity-down" type="button" @click="item.quantity--" :disabled="item.quantity <= 0"><font-awesome-icon :icon="['fas', 'minus']" /></button>
                            <label class="quantity" for="quantity-input">
                                <input class="quantity-input" type="number" v-model="item.quantity" min="0" aria-label="Quantity"/>
                            </label>
                            <button class="quantity-btn quantity-up" type="button" @click="item.quantity++"><font-awesome-icon :icon="['fas', 'plus']" /></button>
                        </div>
                        <p class="price">£{{ (item.price * item.quantity).toFixed(2) }}</p>
                    </div>
                    <button class="delete-item" @click="cart.removeItem(item.item_id)"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                </li>
            </ul>
        </div>

        <div class="payment-summary">
            <div class="inner-top">
                <div class="sub-total payment-item">
                    <p class="sub-total-label">Sub Total</p>
                    <p class="sub-total-value" :class="promoPercent > 0 ? 'promo-applied' : ''">£{{ cart.totalPrice.toFixed(2) }}</p>
                </div>
                <p class="discount payment-item" v-if="promoPercent > 0">£{{ (((cart.totalPrice.toFixed(2))/100)*(100-promoPercent)).toFixed(2) }} (-{{ promoPercent }}%)</p>
                <div class="shipping payment-item">
                    <p class="shipping-label">Shipping</p>
                    <p class="shipping-value">£{{ cart.shipping.toFixed(2) }}</p>
                </div>
                <div class="vat payment-item">
                    <p class="vat-label">VAT</p>
                    <p class="vat-value">£{{ ((cart.totalPrice / 100) * 20).toFixed(2) }}(20%)</p>
                </div>
                <div class="promo-box payment-item">
                    <input type="text" class="promo-input" id="promo-code" placeholder="Enter Promo Code" v-model="promoCode">
                    <button class="apply-promo promo-button" @click="applyPromo">Apply</button>
                    <p class="promoFailed" v-show="promoFailed">Enter a valid code</p>
                </div>
            </div>
            <div class="inner-bottom">
                <div class="total payment-item">
                    <p class="total-label">Total</p>
                    <p class="total-value">£{{calcedTot()}}</p>
                </div>
                <ProceedToCheckout/>
            </div>
        </div>

    </div>

    <Footer v-if="footer" :footer="footer" :pages="pages" />
</template>

<script>
import Footer from '@/components/nav/Footer.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import ProceedToCheckout from '@/components/product/basket/ProceedToCheckout.vue';
import ResponsiveImage from '@/components/widgets/components/ResponsiveImage.vue';
import { useCartStore } from '@/utils/cartStore';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';


export default {
    components: {
        ResponsiveImage,
        Footer,
        HamburgerHeader,
        Link,
        ProceedToCheckout,
    },
    props: {
        header: Object,
        footer: Object,
        pages: Object,
    },
    data() {
        return {
            aspectRatio: [
                { width: 70, height: 70, at: 1440 },
            ],
            promoCode: '',
            promoPercent: 0,
            promoFailed: false,
        }
    },
    computed: {
        cart() {
            return useCartStore(); 
        },
    },
    methods: {
        calcedTot() {
            let total = 0;
            total = (this.cart.totalPrice - ((this.cart.totalPrice)*this.promoPercent)/100) + this.cart.shipping;
            return total.toFixed(2);
        },
        formatImageToFit(image) {
            const slide = {
                image: {
                image_path: '/' + image,
                },
            };
            return slide;
        },
        async applyPromo() {
            try {
                const response = await axios.post('/promo/validate', {
                    code: this.promoCode,
                });

                if (response.data.valid) {
                    this.promoFailed = false;
                    this.promoPercent = response.data.discount_percentage;

                    this.cart.setPromoCode(this.promoCode, this.promoPercent);
                }
            } catch (error) {
                
                this.promoFailed = true;
                this.promoPercent = 0;
                this.cart.setPromoCode(null, 0);
                alert(
                    error.response?.data?.message ||
                    'Could not apply promo code. Please try again.'
                );
            }
        }
    },
}
</script>

<style>
.page-content {
    display: flex;
    gap: 20px;
    max-width: var(--width-max);
    margin: 0px auto;
    width: 100%;
    .basket-box {
        background-color: var(--white);
        padding: 10px;
        box-shadow: 0px 0px 2px #00000031;
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 100%;
        max-width: 66%;
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
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0px 0px 2px #00000031;
                .text-side {
                    display: flex;
                    flex-direction: row;
                    justify-content: flex-start;
                    align-items: flex-start;
                    gap: 20px;
                    .sku {
                        font-size: .75rem;
                        font-weight: 600;
                    }
                    .quantity-available {
                        font-size: 12px;
                        color: var(--yellow);
                        font-weight: 600;
                        display: flex;
                        flex-direction: column;
                        align-items: flex-start;
                        justify-content: flex-start;
                    }
                    .quantity-input-wrap {
                        display: flex;
                        align-items: stretch;
                        justify-content: center;
                        gap: 5px;
                        .quantity-input {
                            -moz-appearance: textfield; 
                            border: 1px solid var(--black);
                            text-align: center;
                            border-radius: 5px;
                            display: flex;
                            width: 50px;
    
                            &::-webkit-inner-spin-button,
                            &::-webkit-outer-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                            }
                        }
                        .quantity-btn {
                            border: 1px solid var(--secondary-colour);
                            border-radius: 5px;
                            padding: 5px;
                            font-size: 12px;
                            height: 100%;
                        }
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
                .delete-item {
                    margin-bottom: auto;
                    margin-left: auto;
                    align-items: center;
                    justify-content: center;
                    display: flex;
                    height: 30px;
                    width: 30px;
                    border-radius: 100%;
                    @media (hover:hover) {
                        &&:hover {
                            background-color: var(--grey);
                        }
                    }
                }
            }
        }
    }
    .payment-summary {
        max-width: 33%;
        width: 100%;
        background-color: var(--white);
        padding: 20px;
        box-shadow: 0px 0px 2px #00000031;
        display: flex;
        flex-direction: column;
        gap: 14px;
        .inner-top {
            border-bottom: 1px solid var(--grey);
            padding: 20px 0px 10px;
            .payment-item {
                display: flex;
                justify-content: space-between;
            }
            .discount {
                justify-content: flex-end;
            }
            .promo-box {
                margin-top: 10px;
                .promo-input {
                    
                }
            }
            .promo-applied {
                text-decoration: line-through;
            }
        }
        .inner-bottom {
            .payment-item {
                display: flex;
                justify-content: space-between;
            }
            .total-label {
                font-weight: 600;
                font-size: 1.125rem;
            }
            .total-value {
                color: var(--bright-red);
                font-weight: 600;
                font-size: 1.125rem;
            }
            display: flex;
            flex-direction: column;
            align-items: space-between;
            justify-content: flex-start;
            gap: 10px;
        }
    }
}
</style>