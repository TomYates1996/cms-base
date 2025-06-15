<template>
    <section class="crm-page page-wrap">
        <main class="page-left" role="main" aria-label="Products management">
        <NewProduct :products="products" :variantTypes="variantTypes" v-if="showNewListing" @cancelNew="showNewListing = false" id="newProductForm" />
        
        <section v-else class="list-wrap product-list-wrap" aria-labelledby="productsHeading">
            <h1 id="productsHeading" class="crm-header section-title">Products</h1>
            <ul class="list product-list" role="list">
            <li v-for="product in products" :key="product.id" class="list-item product-list-item" role="listitem">
                <a :href="`/product/${product.slug}`" class="list-link product-link">{{ product.label }}</a>
                <button v-if="$page.props.auth.user" class="btn-option option" @click="deleteListing(product.id)" :aria-label="`Delete product: ${product.label}`" title="Delete product">
                <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
            </li>
            </ul>
        </section>
        </main>

        <aside class="page-right sidebar" role="complementary" aria-label="Product actions">
            <button class="btn-primary new-layout" @click="showNewListing = !showNewListing" :aria-expanded="showNewListing.toString()" aria-controls="newProductForm">
                {{ showNewListing ? 'Cancel' : 'Add Product' }}
            </button>
        </aside>
    </section>
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