<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewCRMItem :item="'listing'" v-if="showNewListing" @cancelNew="showNewListing = false"/>
        <div v-if="!showNewListing" class="listing-list-wrap">
          <h1>Listings</h1>
          <ul class="listing-list">
            <li v-for="listing in listings" :key="listing.id" class="listing-list-item">
              <a :href="`/listing/${listing.slug}`">{{ listing.title }}</a>
                <button v-if="$page.props.auth.user" class="option" @click="deleteListing(listing.id)" title="Delete listing" aria-label="Delete listing: {{ listing.title }}">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                </li>
            </ul>
        </div>
        </div>
        <div class="page-right">
            <button class="new-layout" @click="showNewListing = !showNewListing" aria-expanded="showNewListing" aria-controls="newwListingModal">{{ showNewListing ? 'Cancel' : 'New Listing' }}</button>
        </div>
    </div>
</template>
  

<script>
import NewCRMItem from '@/components/crm/NewCRMItem.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewCRMItem,
    },
    props: {
        listings: Array,
    },
    data() {
        return {
            showNewListing: false,
            localListings: [],
        }
    },
    created() {
        this.localListings = this.listings;
    },
    methods: {
        newListing() {
            this.showNewListing = true;
        },
        deleteListing(listing_id) {
            if (confirm("Are you sure you want to delete this listing?")) {
                router.delete(`/crm/listing/delete/${listing_id}`, {
                onSuccess: () => {
                    this.localListings = this.localListings.filter(item => item.id !== listing_id);
                },
                onError: (errors) => {
                    console.log('Error deleting listing:', errors);
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
    .listing-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .listing-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>