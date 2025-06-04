<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewProduct :products="products" :variantTypes="variantTypes" v-if="showNewListing" @cancelNew="showNewListing = false"/>
        <div v-if="!showNewListing" class="product-list-wrap">
          <h1>Products</h1>
          <ul class="product-list">
            <li v-for="product in products" :key="product.id" class="product-list-item">
              <a :href="`/product/${product.slug}`">{{ product.label }}</a>
                <button v-if="$page.props.auth.user" class="option" @click="deleteListing(product.id)" title="Delete product" aria-label="Delete product: {{ product.title }}">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                </li>
            </ul>
        </div>
        </div>
        <div class="page-right">
            <button class="new-layout" @click="showNewListing = !showNewListing" aria-expanded="showNewListing" aria-controls="newwListingModal">{{ showNewListing ? 'Cancel' : 'New Event' }}</button>
        </div>
    </div>
</template>
  

<script>
import NewProduct from '@/components/crm/NewProduct.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewProduct,
    },
    props: {
        products: Array,
        variantTypes: Array,
    },
    data() {
        return {
            showNewListing: false,
            localProducts: [],
        }
    },
    created() {
        this.localProducts = this.products;
    },
    methods: {
        newListing() {
            this.showNewListing = true;
        },
        deleteListing(product_id) {
            if (confirm("Are you sure you want to delete this product?")) {
                router.delete(`/cms/crm/product/delete/${product_id}`, {
                onSuccess: () => {
                    this.localProducts = this.localProducts.filter(item => item.id !== product_id);
                },
                onError: (errors) => {
                    console.log('Error deleting product:', errors);
                }
                });
            }
        },
    },
}
</script>

<style scoped>
    .page-wrap {
        display: flex;
        gap: 20px;
        .page-left {
            width: 80%;
        }
        .page-right {
            width: 20%;
            border-left: 2px solid var(--black);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    }
    .product-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .product-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>