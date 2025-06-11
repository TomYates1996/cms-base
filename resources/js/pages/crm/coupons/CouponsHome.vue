<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewCoupon v-if="showNewCoupon"/>
        <div v-if="!showNewCoupon" class="coupon-list-wrap">
          <h1>Coupons</h1>
          <ul class="coupon-list">
            <li v-for="coupon in useCoupons" :key="coupon.id" class="coupon-list-item">
                <p>{{ coupon.code }} {{ coupon.discount_percentage }}</p>
                <p>{{ coupon.active ? 'Active' : 'Disabled' }}</p>
                <button v-if="$page.props.auth.user" class="option" @click="deleteCoupon(coupon.id)" title="Delete coupon" aria-label="Delete coupon: {{ coupon.name }}">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                </li>
            </ul>
        </div>
        </div>
        <div class="page-right">
            <button class="new-layout" @click="showNewCoupon = !showNewCoupon" aria-expanded="showNewCoupon" aria-controls="newwListingModal">{{ showNewCoupon ? 'Cancel' : 'New Coupon' }}</button>
        </div>
    </div>
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
                    this.localCoupons = this.localProducts.filter(item => item.id !== coupon_id);
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
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>