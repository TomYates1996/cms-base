<template>
  <form class="new-crm-item form" @submit.prevent="createListing()" aria-label="Create New Listing">
    <fieldset class="form-inner">
      <legend class="form-title">Create New Listing</legend>
        <div class="form-section general-section">
            <h3 class="section-header">General Info</h3>
            <div class="form-field">
                <label for="title">Title</label>
                <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" @input="updateSlug" />
            </div>

            <div class="form-field">
                <label for="category">Category</label>
                <input id="category" name="category" type="text" v-model="form.category" />
            </div>

            <div class="form-field">
            <label for="sub_category">Sub Category</label>
            <input id="sub_category" name="sub_category" type="text" v-model="form.sub_category" />
            </div>

            <div class="form-field">
            <label for="description">Description</label>
            <textarea id="description" name="description" v-model="form.short_description"></textarea>
            </div>

            <div class="form-field">
            <label for="description">Short Description</label>
            <textarea id="description" name="description" v-model="form.description"></textarea>
            </div>

            <div class="form-field">
            <label for="tags">Tags (comma separated)</label>
            <input id="tags" name="tags" type="text" v-model="form.tags" />
            </div>
        </div>
        <div class="form-section address-section">
            <h3 class="section-header">Address</h3>
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
        <div class="form-section contact-section">
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

            <div class="form-field">
            <label for="owner_id">Owner ID</label>
            <input id="owner_id" name="owner_id" type="number" v-model.number="form.owner_id" />
            </div>

            <div class="form-field">
            <label for="published_at">Published At</label>
            <input id="published_at" name="published_at" type="datetime-local" v-model="form.published_at" />
            </div>
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

        <div class="form-section openings-section">
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

        <div class="form-section amenities-section">
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
import { LoaderCircle } from 'lucide-vue-next';
import axios from 'axios';

export default {
  setup(){
    const form = useForm({
        title: '',
        slug: '',
        category: '',
        sub_category: '',
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
        accessibility_info: {} 
    });

      return { form } 
  },
  props: {
  },
  data () {
    return {
      manualSlugChange: false, 
      imagePreview: null,
      showImageGrid: false,
    }
  },
  created() {
  },
  components: {
      LoaderCircle,
  },
methods: {
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
    createListing () {
        this.form.tags = this.form.tags.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
        
        this.form.post(route('api.listing.store'), {
          onSuccess: () => {
              this.$inertia.visit(`/cms/crm/listings`);  
          },
          onError: (errors) => {
            console.log('Form submission error:', errors); 
          },
        });
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
  },
  watch: {
    'form.title'() {
      this.resetSlugOnTitleChange();
    }
  }

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