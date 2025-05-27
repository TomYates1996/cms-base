<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewCRMItem :isEvent="true" :events="events" :item="'event'" v-if="showNewListing" @cancelNew="showNewListing = false"/>
        <div v-if="!showNewListing" class="event-list-wrap">
          <h1>Events</h1>
          <ul class="event-list">
            <li v-for="event in events" :key="event.id" class="event-list-item">
              <a :href="`/event/${event.slug}`">{{ event.title }}</a>
                <button v-if="$page.props.auth.user" class="option" @click="deleteListing(event.id)" title="Delete event" aria-label="Delete event: {{ event.title }}">
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
        events: Array,
    },
    data() {
        return {
            showNewListing: false,
            localEvents: [],
        }
    },
    created() {
        this.localEvents = this.events;
    },
    methods: {
        newListing() {
            this.showNewListing = true;
        },
        deleteListing(event_id) {
            if (confirm("Are you sure you want to delete this event?")) {
                router.delete(`/crm/event/delete/${event_id}`, {
                onSuccess: () => {
                    this.localEvents = this.localEvents.filter(item => item.id !== event_id);
                },
                onError: (errors) => {
                    console.log('Error deleting event:', errors);
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
    .event-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .event-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>