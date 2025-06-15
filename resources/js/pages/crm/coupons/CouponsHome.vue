<template>
    <section class="crm-page page-wrap">
        <main class="page-left" role="main" aria-label="Coupons management">
        <NewCoupon v-if="showNewCoupon" id="newCouponForm" />

        <section v-else class="list-wrap coupon-list-wrap" aria-labelledby="couponsHeading">
            <h1 id="couponsHeading" class="crm-header section-title">Coupons</h1>
            <ul class="list coupon-list" role="list">
            <li v-for="coupon in useCoupons" :key="coupon.id" class="list-item coupon-list-item" role="listitem">
                <p class="coupon-code">{{ coupon.code }}</p>
                <p class="coupon-discount">{{ coupon.discount_percentage }}%</p>
                <p class="coupon-status">{{ coupon.active ? 'Active' : 'Disabled' }}</p>
                <button v-if="$page.props.auth.user" class="btn-option option" @click="deleteCoupon(coupon.id)" :aria-label="`Delete coupon: ${coupon.code}`" title="Delete coupon">
                <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
            </li>
            </ul>
        </section>
        </main>

        <aside class="page-right sidebar" role="complementary" aria-label="Coupon actions">
        <button class="btn-primary new-layout" @click="showNewCoupon = !showNewCoupon" :aria-expanded="showNewCoupon.toString()" aria-controls="newCouponForm">
            {{ showNewCoupon ? 'Cancel' : 'New Coupon' }}
        </button>
        </aside>
    </section>
</template>
  

<script>
import NewCoupon from '@/components/crm/NewCoupon.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewCoupon,
    },
    props: {
        coupons: Array,
    },
    data() {
        return {
            showNewCoupon : false,
            localCoupons: [],
            useCoupons: [],
        }
    },
    created() {
        this.localCoupons = this.coupons;
        this.useCoupons = this.localCoupons;
    },
    methods: {
        newListing() {
            this.showNewCoupon = true;
        },
        closeSubcatList() {
            this.useCoupons = this.localCoupons;
            this.showSubCategories = false;
            this.currentCoupon = null;
        },
        addNewSubcoupon(newSub) {
            this.localSubcoupons.push(newSub);
            this.showNewSubCoupon = false;
        },
        deleteCoupon(coupon_id) {
            if (confirm("Are you sure you want to delete this coupon?")) {
                router.delete(`/cms/crm/coupon/delete/${coupon_id}`, {
                onSuccess: () => {
                    this.localCoupons = this.localCoupons.filter(item => item.id !== coupon_id);
                    this.useCoupons = this.localCoupons;
                },
                onError: (errors) => {
                    console.log('Error deleting coupon:', errors);
                }
                });
            }
        },
        deleteSubCoupon(coupon_id) {
            if (confirm("Are you sure you want to delete this coupon?")) {
                router.delete(`/cms/crm/subcoupon/delete/${coupon_id}`, {
                onSuccess: () => {
                    this.localSubcoupons = this.localSubcoupons.filter(item => item.id !== coupon_id);
                },
                onError: (errors) => {
                    console.log('Error deleting subcoupon:', errors);
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
    .coupon-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .coupon-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>