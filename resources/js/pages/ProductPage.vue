<template>
    <Head :title="data.title"></Head>
    
    <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>
    
    <div class="listing-header">
        <h1 class="title">{{ data.title }}</h1>
        <span class="category">
            <p class="category-label" aria-label="Category">{{ data.category }}</p>
            <p class="sub-cat-label" aria-label="Sub-category">{{ data.sub_category }}</p>
        </span>
        <div class="location" v-if="data.address || data.city || data.region || data.postcode">
            <font-awesome-icon :icon="['fas', 'location-dot']" />
            <div class="address-string">
                <p v-if="data.address">{{ data.address }},</p>
                <p v-if="data.city"> {{ data.city }},</p>
                <p v-if="data.region"> {{ data.region }},</p>
                <p v-if="data.postcode"> {{ data.postcode }}</p>
            </div>
        </div>
    </div>
    <div class="listing-content">
        <div class="left-col">
            <MediaGallery :images="data.media_gallery"/>
            <div class="detail-col-mobile">
                <section class="contact" aria-labelledby="contact-heading">
                    <h2 id="contact-heading" class="sr-only">Contact Information</h2>
                    <ul>
                        <li v-if="data.phone_number">
                        <strong>Phone:</strong> <a :href="`tel:${data.phone_number}`">{{ data.phone_number }}</a>
                        </li>
                        <li v-if="data.email">
                        <strong>Email:</strong> <a :href="`mailto:${data.email}`">{{ data.email }}</a>
                        </li>
                        <li v-if="data.website">
                        <strong>Website:</strong> <a :href="data.website" target="_blank" rel="noopener">{{ data.website }}</a>
                        </li>
                    </ul>
                </section>
            </div>
            <div class="description-tab tab" v-if="data.description">
                <h2 class="tab-heading">About</h2>
                <p class="description-body">{{ data.description }}</p>
            </div>
            <div v-if="data.opening_hours" class="openings-tab tab">
                <h2 class="tab-heading">Opening Times</h2>
                <ul class="opening-list ul-list">
                    <li class="opening-item list-item" v-for="opening in data.opening_hours" :key="opening.name">
                        <p>{{ opening.name }}</p>
                        <p v-if="(opening.closed === '0' || opening.closed === false) && opening.from">{{ opening.from }} - {{ opening.to }}</p>
                        <p v-else>Closed</p>
                    </li>
                </ul>
            </div>
            <div v-if="data.openings" class="openings-tab tab">
                <h2 class="tab-heading">Event Times</h2>
                <ul class="opening-list ul-list">
                    <p v-if="data.recurrence !== 'none'">{{ formatOpenings(data.openings) }}</p>
                    <li class="opening-item list-item" v-for="(opening, index) in data.openings" :key="index">
                        <p class="opening-day-name">{{ opening.name }}</p>
                        <ul v-if="opening.times && (opening.all_day === '0' || !opening.all_day)">
                            <li v-for="(time, index) in opening.times" :key="index">
                                <p>{{ time.from }} - {{ time.to }}</p>
                            </li>
                        </ul>
                        <p v-if="opening.all_day === '1' || opening.all_day === true">All Day</p>
                        <p v-if="!opening.all_day && !opening.times">Closed</p>
                    </li>
                </ul>
            </div>
            <div class="prices-tab tab" v-if="data.prices && data.prices.length > 0 || data.booking_url || data.reservation_email">
                <h2 class="tab-heading">Prices</h2>
                <ul class="prices-list ul-list">
                    <li class="prices-item list-item" v-for="price in data.prices" :key="price.label">
                        <p>{{ price.label }}</p>
                        <p>£{{ price.price }}</p>
                    </li>
                </ul>
                <a v-if="data.booking_url" :href="data.booking_url">Buy Online</a>
                <a v-if="data.reservation_email" :href="`mailto: ${data.reservation_email}`">Inquire/Reserve</a>
            </div>
            <div class="amenities-tab tab" v-if="data.amenities && data.amenities.length > 0">
                <h2 class="tab-heading">Amenities</h2>
                <ul class="amenities-list ul-list">
                    <li class="amenity-item list-item" v-for="(amenity, index) in data.amenities" :key="index">
                        {{ amenity }}
                    </li>
                </ul>
            </div>
            <div class="social-media-tab tab" v-if="data.social_links.some(link => link.url)">
                <h2 class="tab-heading">Social Media</h2>
                <ul>
                    <li v-for="(social, index) in data.social_links.filter(link => link.url)" :key="index">
                        <a :href="social.url">
                            <font-awesome-icon :icon="['fab', social.name.toLowerCase()]"/>
                            {{ social.url }}
                        </a>
                    </li>
                </ul>
            </div>
            <RelatedListings v-if="data.related_listings && data.related_listings.length !== 0 || data.listing" :listings="data.related_listings ? data.related_listings : [data.listing]"/>
            <LinkedEvents v-if="data.events && data.events.length !== 0" :events="data.events"/>
        </div>
        <div class="right-col">
                <section class="contact" aria-labelledby="contact-heading">
                <h2 id="contact-heading" class="sr-only">Contact Information</h2>
                <ul>
                    <li v-if="data.phone_number">
                        <strong>Phone:</strong> <a :href="`tel:${data.phone_number}`">{{ data.phone_number }}</a>
                    </li>
                    <li v-if="data.email">
                        <strong>Email:</strong> <a :href="`mailto:${data.email}`">{{ data.email }}</a>
                    </li>
                    <li v-if="data.website">
                        <strong>Website:</strong> <a :href="data.website" target="_blank" rel="noopener">{{ data.website }}</a>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    <Footer v-if="footer" :footer="footer" :pages="pages" />
</template>

<script>
import MediaGallery from '@/components/widgets/productPages/MediaGallery.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Footer from '@/components/nav/Footer.vue';
import { Head, Link } from '@inertiajs/vue3';
import RelatedListings from '../components/product/RelatedListings.vue';
import LinkedEvents from '@/components/product/LinkedEvents.vue';


export default {
    props: {
        data: Object,
        header: Object,
        footer: Object,
        pages: Object,
    },
    components: {
        MediaGallery,
        Footer,
        HamburgerHeader,
        Head,
        RelatedListings,
        LinkedEvents,
    },
    data() {
        return {
        }
    },
    methods: {
        formatOpenings(input) {
            const days = input.map(item => item.name.toLowerCase());

            const formattedDays = days.map(day =>
                day.charAt(0).toUpperCase() + day.slice(1)
            );

            let recurr = ''

            if (this.data.recurrence === '2weeks') {
                recurr = 'Every other week'
            } else {
                recurr = this.data.recurrence.charAt(0).toUpperCase() + this.data.recurrence.slice(1);
            }

            const endDate = new Date(this.data.end_datetime);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedEnd = endDate.toLocaleDateString(undefined, options);
            
            const daysString = formattedDays.splice(0, formattedDays.length - 1).join(', ');

            let outputString = `${recurr} on ${daysString} and ${formattedDays[formattedDays.length - 1]} until ${formattedEnd}`;

            return outputString;
        },
    },
}
</script>

<style>
.listing-header {
    display: flex;
    flex-direction: column;
    max-width: var(--width-max);
    margin: 0px auto var(--widget-bottom);
    width: 100%;
    padding: 0px 20px;
    gap: 4px;
    .title {
        font: var(--listing-title);
    }
    .category {
        display: flex;
        align-items: baseline;
        justify-content: flex-start;
        gap: 8px;
        .category-label {
            font: var(--listing-category);
            color: var(--primary-colour);
        }
        .sub-cat-label {
            font: var(--listing-sub-category);
            display: none;
        }
    }
    .location {
        display: flex;
        align-items: baseline;
        gap: 6px;
        .address-string {
            display: flex;
            align-items: baseline;
            gap: 3px;
        }
    }
}

.listing-content {
    display: flex;
    max-width: var(--width-max);
    flex-direction: column;
    width: 100%;
    margin: 0 auto;
    padding: 0 20px;
    gap: 20px;
    .tab {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: var(--widget-bottom);
        .tab-heading {
            font: var(--listing-tab-heading);
            padding: 6px 0px;
            width: 100%;
            border-bottom: 1px solid var(--black);
        }
        .ul-list {
            .list-item {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
        }
    }
    @media screen and (min-width: 40rem) {
        flex-direction: row;
    }
}

.left-col {
    flex-basis: 100%;
    flex-shrink: 0;
    flex-grow: 0;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    max-width: 100%;
    .detail-col-mobile {
        display: flex;
        flex-direction: column;
    }
    @media screen and (min-width: 40rem) {
        max-width: 75%;
        flex-basis: 75%;
        .detail-col-mobile {
            display: none;
        }
    }
}

.right-col {
    flex-basis: 25%;
    flex-shrink: 0;
    flex-grow: 0;
    box-sizing: border-box;
    max-width: 50%;
    display: none;
    flex-direction: column;
        @media screen and (min-width: 40rem) {
            display: flex;
        }
}

.opening-day-name {
    text-transform: capitalize;
}
</style>