<template>
    <Head :title="data.meta_title"></Head>
    
    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>
    <div class="top-shop-section">
        <MediaGallery :images="useData.media_gallery"/>
        <div class="right-top-shop">
            <div class="listing-header">
                <h1 class="title">{{ useData.name }}</h1>
                <p>Product Code : {{ selectedSizeItem.sku }}</p>
                <p v-if="chosenAmount > selectedSizeItem.stock_quantity" class="quantity yellow">
                    Insufficient stock, {{ selectedSizeItem.stock_quantity }} available. 
                    ETA for {{ chosenAmount }} units is 4 days.
                </p>

                <p v-else :class="['quantity', selectedSizeItem.stock_quantity > 4 ? 'green' : 'red']">
                    {{ selectedSizeItem.stock_quantity <= 4 ? 'Last ' : '' }}
                    {{ selectedSizeItem.stock_quantity > 4 ? '4+' : selectedSizeItem.stock_quantity }} In Stock
                </p>
                <!-- <span class="category">
                    <p class="category-label" aria-label="Category">{{ data.category }}</p>
                    <p class="sub-cat-label" aria-label="Sub-category">{{ data.sub_category }}</p>
                </span> -->
            </div>
            
            
            <select id="size-selector" v-model="selectedSizeId" @change="onSizeChange">
                <option v-for="item in useData.items" :key="item.id" :value="item.id">
                    {{ item.size_label }}
                </option>
            </select>
       
            <ul class="variants" role="list">
            <li
                v-for="variant in data.variants"
                :key="variant.id"
                class="variant-thumbnail-item"
            >
                <button
                type="button"
                class="variant-thumbnail-button"
                @click="setUseData(variant)"
                :aria-label="`Select variant: ${variant.name}`"
                :title="variant.name"
                >
                <img
                    class="variant-thumbnail-img"
                    :src="'/' + variant.thumbnail_image"
                    :alt="variant.name"
                    loading="lazy"
                />
                </button>
            </li>
            </ul>
        
            <div class="bottom-product-top">
                <div class="price-section">
                    <p class="price">£{{ selectedSizeItem.price }}</p>
                    <p class="vat">Incl. VAT</p>
                </div>
                <div class="quantity-input-wrap">
                    <button class="quantity-btn quantity-down" type="button" @click="chosenAmount--" :disabled="chosenAmount <= 0"><font-awesome-icon :icon="['fas', 'minus']" /></button>
                    <input class="quantity-input" type="number" v-model="chosenAmount" min="0" aria-label="Quantity"/>
                    <button class="quantity-btn quantity-up" type="button" @click="chosenAmount++"><font-awesome-icon :icon="['fas', 'plus']" /></button>
                </div>
                <AddToCartButton :item="selectedSizeItem" :product="data" :quantity="chosenAmount" :variant="useData"/>
            </div>
        </div>
    </div>
    
    <InformationTabs :productDescription="useData.description"/>

    <Footer v-if="footer" :footer="footer" :pages="pages" />
</template>

<script>
import MediaGallery from '@/components/widgets/productPages/MediaGallery.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Footer from '@/components/nav/Footer.vue';
import { Head, Link } from '@inertiajs/vue3';
import RelatedListings from '../components/product/RelatedListings.vue';
import InformationTabs from '@/components/product/InformationTabs.vue';
import AddToCartButton from '@/components/product/basket/AddToCartButton.vue';


export default {
    props: {
        data: Object,
        header: Object,
        footer: Object,
        pages: Object,
        variantTypes: Array,
    },
    components: {
        MediaGallery,
        Footer,
        HamburgerHeader,
        Head,
        InformationTabs,
        AddToCartButton,
    },
    data() {
        return {
            selectedVariants: {}, 
            useData: {},
            selectedSizeId: 0,
            selectedSizeItem: '',
            chosenAmount: 1,
        }
    },
    created() {
        this.useData = this.data.variants[0];
        this.selectedSizeId = this.useData.items[0]?.id || null,
        this.selectedSizeItem = this.useData.items.find(
            item => item.id === this.selectedSizeId
        ) || null;
    },
    computed: {
        colorSizeMap() {
            const map = {};

            this.data.variants.forEach(variant => {
            const colorOption = variant.options.find(opt => opt.variant_type_id === 1); 
            const sizeOption = variant.options.find(opt => opt.variant_type_id === 2);

            if (!colorOption || !sizeOption) return;

            const color = colorOption.name;
            const size = sizeOption.name;

            if (!map[color]) {
                map[color] = [];
            }

            if (!map[color].includes(size)) {
                map[color].push(size);
            }
            });

            return map;
        }
    },
    methods: {
        onSizeChange() {
            this.selectedSizeItem = this.useData.items.find(
            item => item.id === this.selectedSizeId
            ) || null;
        },
        setUseData(variant) {
            this.useData = variant;
            this.selectedSizeItem = variant.items[0];
            this.selectedSizeId = this.selectedSizeItem.id
        },
    },
}
</script>

<style>
.top-shop-section {
    display: flex;
    .media-gallery {
        width: 50%;
    }
    .right-top-shop {
        width: 50%;
        display: flex;
        flex-direction: column;
        padding: 25px;
        gap: 16px;
        .listing-header {
            .title {
                font: var(--listing-title);
                margin-top: 20px;
            }
            .quantity {
                font-weight: 600;
                font-size: 14px;
                &&.red {
                    color: var(--deep-red);
                }
                &&.green {
                    color: var(--sea-green);
                }
                &&.yellow {
                    color: var(--yellow);
                }
            }
        }
        #size-selector {
            padding: 6px;
            max-width: 300px;
        }
    }
}
.bottom-product-top {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
    margin-top: 60px;
    .price-section {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        .price {
            font: var(--product-price-font);
            color: var(--product-price-colour);
        }
        .vat {
            font: var(--product-vat-font);
            color: var(--product-vat-colour);
        }
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
}
.variants {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    gap: 10px;
    .variant-thumbnail-item {   
        max-width: 80px;
        height: 100%;
        display: flex;
        border: 1px solid var(--black);
    }
}

</style>