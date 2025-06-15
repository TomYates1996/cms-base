<template>
    <div class="page-wrap crm-page">
        <main class="page-left">
            <NewCRMItem v-if="showNewListing" :isEvent="true" :events="events" :item="'event'" @cancelNew="showNewListing = false" id="newEventForm" />
            <EditCRMItem v-if="showEditItem" :existingItem="eventToEdit" :isEvent="true" :events="events" @cancelNew="showEditItem = false"/>
            <section v-if="!showNewListing && !showEditItem" class="list-wrap event-list-wrap">
                <h1 class="crm-header">Events</h1>
                <ul class="list event-list">
                <li v-for="event in events" :key="event.id" class="list-item event-list-item">
                    <a :href="`/event/${event.slug}`">{{ event.title }}</a>
                    <button class="edit-item" @click="editEvent(event.id)">
                        Edit Event
                    </button>
                    <button v-if="$page.props.auth.user" class="btn-option" @click="deleteListing(event.id)" :aria-label="`Delete event: ${event.title}`" title="Delete event">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                    </button>
                </li>
                </ul>
            </section>
        </main>
        <aside class="page-right">
            <button class="btn-toggle new-layout" @click="showNewListing = !showNewListing" :aria-expanded="showNewListing.toString()" aria-controls="newEventForm">
                {{ showNewListing ? 'Cancel' : 'New Event' }}
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
        events: Array,
    },
    data() {
        return {
            showNewListing: false,
            localEvents: [],
            eventToEdit: {},
            showEditItem: false,
        }
    },
    created() {
        this.localEvents = this.events;
    },
    methods: {
        editEvent(id) {
            this.showEditItem = true;
            this.eventToEdit = this.events.find(event => event.id === id);
        },
        newListing() {
            this.showNewListing = true;
        },
        deleteListing(event_id) {
            if (confirm("Are you sure you want to delete this event?")) {
                router.delete(`/cms/crm/event/delete/${event_id}`, {
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