<template>
    <div class="top-products" >
        <h2>Best Selling Products</h2>
        <p v-if="!$page.props.cms.products" class="contact-add">Email us to discuss adding products: <br>twyates@proton.me</p>
        <table v-if="$page.props.cms.products">
            <thead>
                <tr>
                <th>Rank</th>
                <th>Product Name</th>
                <th>Size</th>
                <th>Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in topProducts" :key="product.id">
                <td>{{ index + 1 }}</td>
                <td>{{ product.product_variant.name }}</td>
                <td>{{ product.size_label }}</td>
                <td>{{ product.quantity_sold }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            topProducts: [] 
        };
    },
    mounted() {
        this.fetchTopSellingProducts();
    },
    methods: {
        async fetchTopSellingProducts() {
            try {
                const response = await fetch('/api/top-selling-products');
                const data = await response.json();
                this.topProducts = data; 
                console.log(this.topProducts);
                
            } catch (error) {
                console.error('Error fetching top selling products:', error);
            }
        }
    }
};
</script>

<style>
    .top-products {
        border: 1px solid var(--black);
        width: fit-content;
        padding: 10px;
        .contact-add {
            display: flex;
            justify-content: center;
            width: 100%;
            align-items: center;
            height: 60%;
            font-weight: 600;
        }
        h2 {
            font-size: 20px;
            padding-left: 10px;
        }
        th {
            text-align: left;
        }
        th, td {
            padding: 4px 10px;
        }
    }
</style>
