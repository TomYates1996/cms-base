<template>
    <form class="new-crm-item form" @submit.prevent="createListing()" aria-label="Create New Listing">
        <fieldset class="form-inner">
            <legend class="form-title" v-if="isListing">Create New Listing</legend>
      <legend class="form-title" v-if="isEvent">Create New Event</legend>
      <div class="form-section general-section">
          <h3 class="section-header">General Info</h3>
          <div class="form-field">
              <label for="title">Title</label>
              <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" @input="updateSlug" />
            </div>
            <div
                class="form-categories form-field"
            >
                <label for="form-categories">Category</label>
                <select
                id="form-categories"
                v-model="form.category_id"
                aria-required="true"
                >
                <option v-for="category in categories.categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
                </select>
            </div>
            
            <div v-if="form.category_id !== null" class="form-field">
                <label for="form-subcategories">Sub Category</label>
                <select v-model="form.subcategory_id">
                    <option
                    v-for="subcategory in filteredSubcategories"
                    :key="subcategory.id"
                    :value="subcategory.id"
                    >
                    {{ subcategory.name }}
                    </option>
                </select>
            </div>
            
            <div class="form-field">
                <label for="description">Description</label>
                <textarea id="description" name="description" v-model="form.description"></textarea>
            </div>
            
            <div class="form-field">
                <label for="description">Short Description</label>
                <textarea id="description" name="description" v-model="form.short_description"></textarea>
            </div>
            
            <div class="form-field">
                <label for="tags">Tags (comma separated)</label>
                <input id="tags" name="tags" type="text" v-model="form.tags" />
            </div>
        </div>
            <div v-if="isEvent" class="form-section organiser-section">
                <div class="form-group">
                    <label>
                        <input type="checkbox" v-model="useListingOrganiser" />
                        Use organiser details from a listing
                    </label>
                    </div>
            
                    <div class="form-group" v-if="useListingOrganiser">
                    <label for="listingSelector">Select Listing</label>
                    <select
                        id="listingSelector"
                        class="form-control"
                        v-model="selectedListingId"
                        @change="fillFromListing"
                    >
                        <option disabled value="">Select a listing</option>
                        <option
                        v-for="listing in regListings"
                        :key="listing.id"
                        :value="listing.id"
                        >
                        {{ listing.title }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="organiser_name">Organiser Name</label>
                    <input 
                        type="text" 
                        id="organiser_name" 
                        name="organiser_name" 
                        class="form-control" 
                        v-model="form.organiser_name"
                        :disabled="useListingOrganiser"
                    />
                </div>
            
                <div class="form-group">
                    <label for="organiser_email">Organiser Email</label>
                    <input 
                        type="email" 
                        id="organiser_email" 
                        name="organiser_email" 
                        class="form-control" 
                        v-model="form.organiser_email"
                        :disabled="useListingOrganiser"
                    />
                </div>
            
                <div class="form-group">
                    <label for="organiser_phone">Organiser Phone</label>
                    <input 
                        type="tel" 
                        id="organiser_phone" 
                        name="organiser_phone" 
                        class="form-control" 
                        v-model="form.organiser_phone"
                        :disabled="useListingOrganiser"
                    />
                </div>
            
            </div>
        <div class="form-section address-section">
            <h3 class="section-header">Address</h3>
            <button v-if="useListingOrganiser" class="autofill-address-btn" @click.prevent="autofillAddress()">Autofill with organiser details</button>
            <div class="form-field">
                <label for="address">Address</label>
                <input id="address" name="address" type="text" v-model="form.address" />
            </div>

            <div class="form-field">
                <label for="city">City</label>
                <input id="city" name="city" type="text" v-model="form.city" />
            </div>

            <div class="form-field">
                <label for="region">Region</label>
                <input id="region" name="region" type="text" v-model="form.region" />
            </div>

            <div class="form-field">
                <label for="country">Country</label>
                <input id="country" name="country" type="text" v-model="form.country" />
            </div>

            <div class="form-field">
                <label for="postcode">Postcode</label>
                <input id="postcode" name="postcode" type="text" v-model="form.postcode" />
            </div>

            <div class="form-field">
                <label for="latitude">Latitude</label>
                <input id="latitude" name="latitude" type="number" step="0.0000001" v-model.number="form.latitude" />
            </div>

            <div class="form-field">
                <label for="longitude">Longitude</label>
                <input id="longitude" name="longitude" type="number" step="0.0000001" v-model.number="form.longitude" />
            </div>
        </div>
        <div v-if="isListing" class="form-section contact-section">
            <h3 class="section-header">Contact Details</h3>
            <div class="form-field">
                <label for="phone_number">Phone Number</label>
                <input id="phone_number" name="phone_number" type="tel" v-model="form.phone_number" />
            </div>
            
            <div class="form-field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" v-model="form.email" />
            </div>

            <div class="form-field">
                <label for="website">Website</label>
                <input id="website" name="website" type="url" v-model="form.website" />
            </div>
        </div>

                <div class="form-section additional-section">
            <h3 class="section-header">Additional Items</h3>
            <div class="form-field">
            <label for="booking_url">Booking URL</label>
            <input id="booking_url" name="booking_url" type="url" v-model="form.booking_url" />
            </div>

            <div class="form-field">
            <label for="reservation_email">Reservation Email</label>
            <input id="reservation_email" name="reservation_email" type="email" v-model="form.reservation_email" />
            </div>

            <div class="form-field">
            <label for="featured">Featured</label>
            <input id="featured" name="featured" type="checkbox" v-model="form.featured" />
            </div>

            <!-- <div class="form-field">
            <label for="published_at">Published At</label>
            <input id="published_at" name="published_at" type="datetime-local" v-model="form.published_at" />
            </div> -->
        </div>

        <div class="form-section media-section">
            <h3 class="section-header">Media</h3>
            <div class="form-field">
                <label for="media_gallery">Media Gallery</label>
                <input
                    id="media_gallery"
                    name="media_gallery"
                    type="file"
                    accept="image/*"
                    multiple
                    @change="onMediaGalleryChange"
                />
                <ul>
                    <li v-for="(file, index) in form.media_gallery" :key="index">
                    {{ file.name }}
                    <button type="button" @click="removeMedia(index)" aria-label="Remove {{ file.name }}">Remove</button>
                    </li>
                </ul>
            </div>

            <div class="form-field">
                <label for="thumbnail_image">Thumbnail Image</label>
                <input
                    id="thumbnail_image"
                    name="thumbnail_image"
                    type="file"
                    accept="image/*"
                    @change="onThumbnailChange"
                />
            </div>
        </div>

        <div v-if="isListing" class="form-section openings-section">
            <h3 class="section-header">Openings</h3>
            <div class="form-field">
                <h3>Opening Hours</h3>
                <ul>
                <li v-for="(opening, index) in form.opening_hours" :key="index">
                    <span>{{ opening.name }}:</span>
                    
                    <label :for="`opening-from-${opening.name}`">From</label>
                    <input
                        v-model="form.opening_hours[index].from"
                        type="time"
                        :id="`opening-from-${opening.name}`"
                        :aria-label="`Opening time from on ${opening.name}`"
                    />

                    <label :for="`opening-to-${opening.name}`">To</label>
                    <input
                        v-model="form.opening_hours[index].to"
                        type="time"
                        :id="`opening-to-${opening.name}`"
                        :aria-label="`Opening time to on ${opening.name}`"
                    />

                    <label :for="`opening-closed-${opening.name}`">Closed</label>
                    <input
                        v-model="form.opening_hours[index].closed"
                        type="checkbox"
                        :id="`opening-closed-${opening.name}`"
                        :aria-label="`Is closed on ${opening.name}`"
                        />
                    </li>
                </ul>
            </div>
        </div>
        
        <div v-if="isEvent" class="form-section openings-section">
            <div class="one-day">
                <label class="date-label">On specific date only</label>
                <input type="checkbox" v-model="oneDay" class="event-date date-one-day" />
            </div>
            <div class="start-date">
                <label class="date-label">Start date</label>
                <input type="date" v-model="form.start_datetime" required @change="calculateDays()" class="event-start-date date-range-input" />
            </div>
            <div v-if="!oneDay || !oneDay && form.recurrence === 'none'" class="end-date">
                <label class="date-label">End date (inclusive)</label>
                <input type="date" v-model="form.end_datetime" class="event-end-date date-range-input" required  />
            </div>
                        
            <div class="recurrence">
                <label for="recurrence">Recurrence</label>
                <select v-model="form.recurrence" id="recurrence">
                    <option value="none">No recurrence</option>
                    <option v-if="!oneDay" value="weekly">Every Week</option>
                    <option v-if="!oneDay" value="2weeks">Every Other Week</option>
                    <!-- <option v-if="!oneDay" value="monthly">Monthly</option> -->
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <ul v-if="form.start_datetime && oneDay" class="day-list">
                <div v-for="(day, index) in calculatedDaysList" class="day-input-wrap" :key="index">
                    <label class="event-label">
                        <input type="checkbox" v-model="day.all_day" class="event-opening-checkbox"/>
                        All Day
                    </label>
                    <div v-if="!day.all_day" class="times-section">
                        <div v-for="(time, timeIndex) in day.times" :key="timeIndex" class="event-time-item">
                                <label class="">From</label>
                                <input type="time" v-model="time.from" class="" />
                                <label class="">To</label>
                                <input type="time" v-model="time.to" class="" />
                                <button type="button" @click="day.times.splice(timeIndex, 1)" v-if="day.times.length > 1" class="remove-time"><font-awesome-icon :icon="['fas', 'xmark']" /></button>
                        </div>
                        <button type="button" @click="day.times.push({ from: '', to: '' })" v-if="!day.all_day" class="add-time">Add Time</button>
                    </div>
                </div>
            </ul>    
            <ul v-if="form.start_datetime && !oneDay" class="day-list">
                <div v-for="(day, index) in daysList" class="day-input-wrap" :key="index">
                    <div class="day-active">
                        <label :for="`day-${day.name}`">{{day.label}}</label>
                        <input v-model="day.selected" type="checkbox" :name="`day-${day.name}`" :id="`day-${day.name}`">
                    </div>
                    <label class="event-label">
                        <input type="checkbox" v-model="day.all_day" class="event-opening-checkbox"/>
                        All Day
                    </label>
                    <div v-if="!day.all_day" class="times-section">
                        <div v-for="(time, timeIndex) in day.times" :key="timeIndex" class="event-time-item">
                                <label class="">From</label>
                                <input type="time" v-model="time.from" class="" />
                                <label class="">To</label>
                                <input type="time" v-model="time.to" class="" />
                                <button type="button" @click="day.times.splice(timeIndex, 1)" v-if="day.times.length > 1" class="remove-time"><font-awesome-icon :icon="['fas', 'xmark']" /></button>
                        </div>
                        <button type="button" @click="day.times.push({ from: '', to: '' })" v-if="!day.all_day" class="add-time">Add Time</button>
                    </div>
                </div>
            </ul>    
        </div>

        <div class="form-section prices-section">
            <h3 class="section-header">Prices</h3>
            <div class="form-field">
                <div class="prices-list" v-for="(price, index) in form.prices" :key="index">
                    <label :for="`prices-label-${index}`">Label</label>
                    <input
                    type="text"
                    :id="`prices-label-${index}`"
                    v-model="form.prices[index].label"
                    placeholder="e.g. Suite, Crab"
                    />

                    <label :for="`prices-price-${index}`">Price</label>
                    <input
                    type="number"
                    :id="`prices-price-${index}`"
                    v-model.number="form.prices[index].price"
                    step="0.01"
                    placeholder="e.g. 20"
                    />

                    <button class="btn-default" type="button" @click="removePrice(index)">Remove</button>
                </div>
                <button class="btn-default" type="button" @click="addPrice">Add Price</button>
            </div>
        </div>        
        <div class="form-section section-social">
            <h3 class="section-header">Social Media</h3>
            <div class="form-field" v-for="(link, index) in form.social_links" :key="index">
                <label :for="`social-link-${link.name}`">{{ link.name }}</label>
                <input
                    type="url"
                    :id="`social-link-${link.name}`"
                    :name="`social_links[${index}].url`"
                    v-model="form.social_links[index].url"
                    placeholder="Enter URL"
                />
            </div>
        </div>

        <div v-if="isListing" class="form-section amenities-section">
            <h3 class="section-header">Amenities</h3>
            <div class="form-field">
                <h3>Amenities</h3>
                <ul>
                    <li v-for="(amenity, index) in form.amenities" :key="index">
                    <input
                        type="checkbox"
                        :id="`amenity-${index}`"
                        v-model="form.amenities[index].checked"
                    />
                    <label :for="`amenity-${index}`">{{ amenity.name }}</label>
                    </li>
                </ul>
            </div>

            <!-- <div class="form-field">
            <label for="accessibility_info">Accessibility Info (JSON Object)</label>
            <textarea id="accessibility_info" name="accessibility_info" v-model="form.accessibility_info"></textarea>
            </div> -->
        </div>

        <div v-if="isListing" class="form-section listings-section">
            <h3 class="section-header">Related Listings</h3>
            <div class="all-listings">
                <h3>Available Listings</h3>
                <ul>
                    <li
                    v-for="listing in regListings"
                    :key="listing.id"
                    @click="toggleListing(listing)"
                    :class="{ selected: isSelected(listing) }"
                    >
                    {{ listing.title }}
                    </li>
                </ul>
                </div>

                <div class="selected-listings">
                <h3>Selected Listings for Form</h3>
                <ul>
                    <li
                    v-for="listing in selectedListings"
                    :key="listing.id"
                    >
                    {{ listing.title }}
                    <button @click="removeListing(listing)">Remove</button>
                    </li>
                </ul>
                </div>
        </div>


      <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Listing
      </button>
      <button type="button" class="btn-default" tabindex="6" :disabled="form.processing" @click.prevent="cancelNew()" >
        Cancel
      </button>
    </fieldset>
  </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle, LogIn } from 'lucide-vue-next';
import axios from 'axios';

export default {
  setup(){
    const form = useForm({
        title: '',
        slug: '',
        category_id: null,
        sub_category_id: null,
        short_description: '',
        description: '',
        tags: '',              
        address: '',
        city: '',
        region: '',
        country: '',
        postcode: '',
        latitude: null,        
        longitude: null,
        phone_number: '',
        email: '',
        website: '',
        media_gallery: [],     
        thumbnail_image: '',
        opening_hours: [
            {
                name: 'Monday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Tuesday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Wednesday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Thursday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Friday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Saturday',
                from: null,
                to: null,
                closed: false,
            },
            {
                name: 'Sunday',
                from: null,
                to: null,
                closed: false,
            },
        ],     
        prices: [],            
        booking_url: '',
        reservation_email: '',
        featured: false,      
        owner_id: null,        
        published_at: null,   
        social_links: [
            { name: 'Facebook', url: '' },
            { name: 'Twitter', url: '' },
            { name: 'Instagram', url: '' },
            { name: 'LinkedIn', url: '' },
            { name: 'Pinterest', url: '' },
            { name: 'TikTok', url: '' },
            { name: 'YouTube', url: '' },
            { name: 'Reddit', url: '' },
            { name: 'WhatsApp', url: '' }
        ],     
        amenities: [
            { name: 'Free Wi-Fi', checked: false },
            { name: 'Parking', checked: false },
            { name: 'Swimming Pool', checked: false },
            { name: 'Pet Friendly', checked: false },
            { name: 'Gym', checked: false },
            { name: 'Air Conditioning', checked: false },
            { name: 'Restaurant', checked: false },
            { name: 'Bar', checked: false },
            { name: 'Wheelchair Accessible', checked: false },
            { name: 'Spa', checked: false }
        ],       
        accessibility_info: {},
        listing_ids: [],
        event_ids: [],
        // Event Specific Data
        organiser_name: '',
        organiser_email: '',
        organiser_phone: '',
        organiser_id: '',
        openings: [],
        start_datetime: '',
        end_datetime: '',
        recurrence: 'None',
    });

      return { form } 
  },
    props: {
        events: {
            default: null,
            type: Array,
        },
        listings: {
            default: null,
            type: Array,
        },
        isListing: {
            default: false,
            type: Boolean,
        },
        isEvent: {
            default: false,
            type: Boolean,
        },
    },
    data () {
        return {
            manualSlugChange: false, 
            imagePreview: null,
            showImageGrid: false,
            regEvents: null,
            regListings: null,
            selectedListings: [],
            oneDay: false,
            calculatedDaysList: [],
            useListingOrganiser: false,
            selectedListingId: null,
            categories: [],
            daysList: [
                { name : 'monday', label : 'Mon', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'tuesday', label : 'Tue', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'wednesday', label : 'Wed', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'thursday', label : 'Thu', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'friday', label : 'Fri', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'saturday', label : 'Sat', selected : false, all_day: false, times: [{from: '',to: ''}] },
                { name : 'sunday', label : 'Sun', selected : false, all_day: false, times: [{from: '',to: ''}] },
            ]
        }
    },
    created() {
        console.log(this.listings);
        if ((this.events === null || this.events.length === 0) && !this.isEvent) {
            this.regListings = this.listings;
            this.indexEvents();
        }
        if ((this.listings === null || this.listings.length === 0) && !this.isListing) {
            
            this.regEvents = this.events;
            this.indexListings();
            
        }
    },
    computed: {
        filteredSubcategories() {
        const selected = this.categories.categories.find(
            (cat) => cat.id === this.form.category_id
        );
        return selected?.subcategories || [];
        },
    },
  components: {
      LoaderCircle,
  },
methods: {
    getCategories() {
        axios.get('/api/categories/index') 
        .then(res => {
            this.categories = res.data;
        })
        .catch(err => {
            console.error("Failed to load categories", err);
        });
    },
    onMediaGalleryChange(event) {
        const files = Array.from(event.target.files);
        this.form.media_gallery.push(...files);
        event.target.value = null;
    },
    removeMedia(index) {
        this.form.media_gallery.splice(index, 1);
    },
    addPrice() {
        this.form.prices.push({ label: '', price: null });
    },
    removePrice(index) {
        this.form.prices.splice(index, 1);
    },
    onThumbnailChange(event) {
        const file = event.target.files[0];
        this.form.thumbnail_image = file || null;
        event.target.value = null;
    },
generateOpenings() {
    const openings = [];
    const startDate = new Date(this.form.start_datetime);
    let endDate = new Date(this.form.end_datetime);
    if (!this.form.end_datetime || isNaN(endDate.getTime())) {
        endDate = new Date('2099-12-31');
    } 
    const recurrence = this.form.recurrence || 'none';
    const oneDay = this.oneDay;

    const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    if (oneDay) {
        const dayName = dayNames[startDate.getDay()];
        const month = startDate.getMonth() + 1;
        const date = startDate.getDate();

        if (recurrence === 'yearly') {
            const startYear = startDate.getFullYear();
            const endYear = endDate.getFullYear();

            for (let year = startYear; year <= endYear; year++) {
                const dateObj = new Date(year, month, date);
                if (dateObj > endDate) break;

                const freshDay = dayNames[dateObj.getDay()];

                openings.push({
                    date: dateObj.toISOString().slice(0, 10),
                    day: freshDay,
                    month: monthNames[month],
                    all_day: this.calculatedDaysList[0].all_day,
                    times: this.calculatedDaysList[0].all_day
                        ? []
                        : this.calculatedDaysList[0].times.map(t => ({ ...t }))
                });
            }
        } else {
            openings.push({
                date: startDate.toISOString().slice(0, 10),
                day: dayName,
                month: monthNames[month],
                all_day: this.calculatedDaysList[0].all_day,
                times: this.calculatedDaysList[0].all_day
                    ? []
                    : this.calculatedDaysList[0].times.map(t => ({ ...t }))
            });
        }

        this.form.openings = openings;
        return;
    }

        const selectedDays = this.daysList.filter(day => day.selected);

        if (!selectedDays.length) {
            this.form.openings = [];
            return;
        }

        const getMatchedDay = (date) => {
            const name = dayNames[date.getDay()];
            return selectedDays.find(d => d.name === name);
        };

        const addOpening = (date, matchedDay) => {
            openings.push({
                date: date.toISOString().slice(0, 10),
                day: dayNames[date.getDay()],
                month: monthNames[date.getMonth()],
                all_day: matchedDay.all_day,
                times: matchedDay.all_day ? [] : matchedDay.times.map(t => ({ ...t })),
            });
        };

        let currentDate = new Date(startDate);

        if (recurrence === 'none') {
            // Go day-by-day and add if it matches a selected day
            while (currentDate <= endDate) {
                const matchedDay = getMatchedDay(currentDate);
                if (matchedDay) {
                    addOpening(new Date(currentDate), matchedDay);
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

        } else if (recurrence === 'weekly') {
            // For each week, loop through 7 days and check matches
            while (currentDate <= endDate) {
                for (let i = 0; i < 7; i++) {
                    if (currentDate > endDate) break;
                    const matchedDay = getMatchedDay(currentDate);
                    if (matchedDay) {
                        addOpening(new Date(currentDate), matchedDay);
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
            }

        } else if (recurrence === '2weeks') {
            // Similar to weekly, but skip every other week
            while (currentDate <= endDate) {
                for (let i = 0; i < 7; i++) {
                    if (currentDate > endDate) break;
                    const matchedDay = getMatchedDay(currentDate);
                    if (matchedDay) {
                        addOpening(new Date(currentDate), matchedDay);
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                currentDate.setDate(currentDate.getDate() + 7);
            }
        }

        this.form.openings = openings;
        
    },
    createListing () {
        this.form.tags = this.form.tags.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
        this.form.amenities = this.form.amenities.filter(a => a.checked).map(a => a.name)
        this.selectedListings.forEach(el => {
            this.form.listing_ids.push(el.id);
        })

        if (this.isEvent) {
            // this.generateOpenings();
            if (this.oneDay) {
                this.form.openings = this.calculatedDaysList;
                this.form.end_datetime = this.form.start_datetime;
            } else {
                this.form.openings = this.daysList.filter(day => day.selected);
            }

            
            this.form.post(route('api.event.store'), {
                onSuccess: () => {
                    this.$inertia.visit(`/cms/crm/events`);  
                },
                onError: (errors) => {
                    console.log('Form submission error:', errors); 
                    },
                });
            }



        
        
        
        if (this.isListing) {
            this.form.post(route('api.listing.store'), {
                onSuccess: () => {
                    this.$inertia.visit(`/cms/crm/listings`);  
                },
                onError: (errors) => {
                    console.log('Form submission error:', errors); 
                        this.form.amenities = [
                            { name: 'Free Wi-Fi', checked: false },
                            { name: 'Parking', checked: false },
                            { name: 'Swimming Pool', checked: false },
                            { name: 'Pet Friendly', checked: false },
                            { name: 'Gym', checked: false },
                            { name: 'Air Conditioning', checked: false },
                            { name: 'Restaurant', checked: false },
                            { name: 'Bar', checked: false },
                            { name: 'Wheelchair Accessible', checked: false },
                            { name: 'Spa', checked: false }
                        ];  
                    },
                });
            }
        },
        cancelNew() {
            this.form.reset();
            this.$emit('cancelNew');
        },
      updateSlug() {
        if (!this.manualSlugChange) {
          this.form.slug = this.slugify(this.form.title); 
        }
      },
      slugify(text) {
        return text
          .toLowerCase()
          .replace(/\s+/g, '-')  
          .replace(/[^\w\-]+/g, '')  
          .replace(/\-\-+/g, '-')  
          .replace(/^-+/, '') 
          .replace(/-+$/, '');  
      },
      manualSlugEdit() {
        this.manualSlugChange = true;
      },
      resetSlugOnTitleChange() {
        if (!this.form.title) {
          this.manualSlugChange = false;
          this.form.slug = ''; 
        }
      },
        uploadImage(event) {
            this.form.image = event.target.files[0];
            this.updatePreview(this.form.image);
            
        },
        updatePreview(imageFile) {
            const file = imageFile; 
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    this.imagePreview = reader.result;
                };
                reader.readAsDataURL(file);
            }
        },
    indexEvents() {
        axios.get('/cms/crm/events/index')
        .then(response => {
            this.regEvents = response.data;
        })
        .catch(error => {
            console.error('Error fetching listings:', error);
        });
    },
    indexListings() {
        axios.get('/cms/crm/listings/index')
        .then(response => {
            this.regListings = response.data;
            console.log(this.regListings);
            
        })
        .catch(error => {
            console.error('Error fetching listings:', error);
        });
    },
    toggleListing(listing) {
      const exists = this.selectedListings.find(l => l.id === listing.id);
      if (!exists) {
        this.selectedListings.push(listing);
      } else {
        this.selectedListings = this.selectedListings.filter(l => l.id !== listing.id);
      }
    },
    isSelected(listing) {
      return this.selectedListings.some(l => l.id === listing.id);
    },
    removeListing(listing) {
      this.selectedListings = this.selectedListings.filter(l => l.id !== listing.id);
    },
    updateDay(opening) {
      if (opening.date) {
        const options = { weekday: 'long' }
        const dayName = new Date(opening.date).toLocaleDateString('en-GB', options)
        opening.day = dayName
      }
    },
    calculateDays() {
        const fullDayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const shortDayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        const date = new Date(this.form.start_datetime);
        const dayIndex = date.getDay();

        this.calculatedDaysList = [
            { name: fullDayNames[dayIndex], label: shortDayLabels[dayIndex], selected: false, all_day: false, times: [{ from: '', to: '' }] }
        ];
    },
    fillFromListing() {
        const selected = this.regListings.find(l => l.id === this.selectedListingId);
        if (selected) {
        this.form.organiser_name = selected.title;
        this.form.organiser_email = selected.email;
                this.form.organiser_phone = selected.phone_number;
                this.form.organiser_id = selected.id;
            }
        },
        autofillAddress() {
            const listing = this.regListings.find(el => el.id === this.selectedListingId);
            this.form.address = listing.address;
            this.form.city = listing.city;
            this.form.region = listing.region;
            this.form.postcode = listing.postcode;
            this.form.country = listing.country;
            this.form.longitude = listing.longitude;
            this.form.latitude = listing.latitude;
        },
    },
    watch: {
        'form.title'() {
            this.resetSlugOnTitleChange();
        }
    },
    mounted() {
       this.getCategories();
    },
}
</script>

<style scoped>
.new-crm-item {
    .form-inner {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        .form-section {
            border-top: 1px solid #00000050;
            border-right: 1px solid #00000050;
            &&:nth-child(even) {
                border-left: 1px solid #00000050;
            }
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 8px;
            .section-header {
                font-size: 18px;
                text-decoration: underline;
            }
            .autofill-address-btn {
                padding: 2px 6px;
                border-radius: 4px;
                background-color: var(--primary-colour);
                color: var(--white);
                @media (hover:hover) {
                    &&:hover {
                        text-decoration: underline;
                    }
                }
            }
            .form-field {
                display: flex;
                gap: 6px;
                input:not([type = "file"]),
                textarea {
                    border: 1px solid var(--black);
                    border-radius: 4px;
                }
            }
        }
        .media-section {
            grid-column: span 2;
        }
        .openings-section {
            grid-column: span 2;
            border-left: 1px solid #00000050;
            .day-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
                border-top: 1px solid var(--black);
                .day-input-wrap {
                    display: flex;
                    gap: 10px;
                    border-bottom: 1px solid var(--black);
                    padding: 8px;
                    .day-active {
                        display: flex;
                        gap: 8px;
                        justify-content: flex-start;
                        align-items: center;
                    }
                    .event-label {
                        display: flex;
                        gap: 4px;
                        justify-content: flex-start;
                        align-items: center;
                    }
                }
            }
        }
        .prices-section {
            grid-column: span 2;
            .form-field {
                display: flex;
                flex-direction: column;
                .prices-list {
                    display: flex;
                    gap: 8px;
                }
                button {
                    max-width: 100px;
                }
            }
        }
        .section-social {
            border-left: 1px solid #00000050;
            border-right: none;
        }
    }
}
</style>