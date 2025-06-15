<template>
    <div class="page-wrap crm-page">
        <main class="page-left">
            <NewCRMItem v-if="showNewListing" :isListing="true" :listings="listings" :item="'listing'" @cancelNew="showNewListing = false" id="newListingForm" />
            <EditCRMItem v-if="showEditItem" :existingItem="listingToEdit" :isListing="true" :listings="listings" @cancelNew="showEditItem = false"/>
            <section v-if="!showNewListing && !showEditItem" class="list-wrap listing-list-wrap">
                <h1 class="crm-header">Listings</h1>
                <ul class="list listing-list">
                <li v-for="listing in listings" :key="listing.id" class="list-item listing-list-item">
                    <a :href="`/listing/${listing.slug}`">{{ listing.title }}</a>
                    <button class="edit-item" @click="editListing(listing.id)">
                        Edit Listing
                    </button>
                    <button v-if="$page.props.auth.user" class="btn-option" @click="deleteListing(listing.id)" :aria-label="`Delete listing: ${listing.title}`" title="Delete listing">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                    </button>
                </li>
                </ul>
            </section>
        </main>
        <aside class="page-right">
        <button class="btn-toggle new-layout" @click="showNewListing = !showNewListing" :aria-expanded="showNewListing.toString()" aria-controls="newListingForm">
            {{ showNewListing ? 'Cancel' : 'New Listing' }}
        </button>
        </aside>
    </div>
</template>

  

<script>
import EditCRMItem from '@/components/crm/EditCRMItem.vue';
import NewCRMItem from '@/components/crm/NewCRMItem.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewCRMItem,
        EditCRMItem,
    },
    props: {
        listings: Array,
    },
    data() {
        return {
            showNewListing: false,
            localListings: [],
            listingToEdit: {},
            showEditItem: false,
        }
    },
    created() {
        this.localListings = this.listings;
    },
    methods: {
        editListing(id) {
            this.showEditItem = true;
            this.listingToEdit = this.listings.find(listing => listing.id === id);
        },
        newListing() {
            this.showNewListing = true;
        },
        deleteListing(listing_id) {
            if (confirm("Are you sure you want to delete this listing?")) {
                router.delete(`/cms/crm/listing/delete/${listing_id}`, {
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