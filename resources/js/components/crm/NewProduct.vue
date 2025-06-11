<template>
  <form @submit.prevent="submit">
    <!-- Product Info -->
    <h2>Product Info</h2>
    <div class="form-field">
      <label for="label">Label</label>
      <input id="label" name="label" type="text" required v-model="form.label" autofocus aria-required="true" @input="updateSlug" />
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
      <label>Meta Title</label>
      <input v-model="form.meta_title" type="text" />
    </div>
    <div class="form-field">
      <label>Meta Description</label>
      <input v-model="form.meta_description" type="text" />
    </div>
<!--
    <div>
      <label>Description</label>
      <textarea v-model="form.description"></textarea>
    </div>
    <div>
      <label>Short Description</label>
      <textarea v-model="form.short_description"></textarea>
    </div> -->

    <!-- Product Variants -->
    <h2>Variants</h2>
    <div v-for="(variant, vIndex) in form.variants" :key="vIndex" class="variant-block">
      <h3>Variant {{ vIndex + 1 }}</h3>

      <div class="form-field">
        <label>Variant Name</label>
        <input v-model="variant.label" type="text" required />
      </div>
        <div class="form-field">
          <label>Description</label>
          <textarea v-model="variant.description"></textarea>
        </div>
        <div class="form-field">
          <label>Short Description</label>
          <textarea v-model="variant.short_description"></textarea>
        </div>

      <div class="form-field">
        <label>Thumbnail</label>
        <input type="file" @change="e => setVariantThumbnail(e, vIndex)" />
        <div v-if="variant.thumbnail_image">
          Selected: {{ variant.thumbnail_image.name }}
          <button @click.prevent="removeVariantThumbnail(vIndex)">Remove</button>
        </div>
      </div>

      <div class="form-field">
        <label>Media Gallery</label>
        <input type="file" multiple @change="e => addVariantMedia(e, vIndex)" />
        <ul>
          <li v-for="(file, fIndex) in variant.media_gallery" :key="fIndex">
            {{ file.name }}
            <button @click.prevent="removeVariantMedia(vIndex, fIndex)">Remove</button>
          </li>
        </ul>
      </div>

      <!-- Variant Items -->
      <h4>Items</h4>
      <div
        v-for="(item, iIndex) in variant.items"
        :key="iIndex"
        class="item-block"
      >
        <div>
          <label>Label (e.g. S, M)</label>
          <input v-model="item.label" type="text" required />
        </div>

        <div>
          <label>SKU</label>
          <input v-model="item.sku" type="text" required />
        </div>

        <div>
          <label>Price</label>
          <input v-model.number="item.price" type="number" required />
        </div>

        <div>
          <label>Stock Quantity</label>
          <input v-model.number="item.stock_quantity" type="number" required />
        </div>

        <button @click.prevent="removeVariantItem(vIndex, iIndex)">Remove Item</button>
      </div>

      <button @click.prevent="addVariantItem(vIndex)">Add Item</button>
      <button @click.prevent="removeVariant(vIndex)">Remove Variant</button>
    </div>

    <button @click.prevent="addVariant">Add Variant</button>
    <br /><br />
    <button type="submit">Create Product</button>
  </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  data() {
    return {
      manualSlugChange: false,
      categories: [],
      form: useForm({
        label: '',
        slug: '',
        category_id: null,
        sub_category_id: null,
        meta_title: '',
        meta_description: '',
        variants: [this.createVariant()],
      }),
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
    createItem() {
      return {
        label: '',
        sku: '',
        price: 0,
        stock_quantity: 0,
      }
    },
    createVariant() {
      return {
        label: '',
        description: '',
        short_description: '',
        thumbnail_image: null,
        media_gallery: [],
        items: [this.createItem()],
      }
    },
    addVariant() {
      this.form.variants.push(this.createVariant())
    },
    removeVariant(index) {
      this.form.variants.splice(index, 1)
    },
    addVariantItem(vIndex) {
      this.form.variants[vIndex].items.push(this.createItem())
    },
    removeVariantItem(vIndex, iIndex) {
      this.form.variants[vIndex].items.splice(iIndex, 1)
    },
    setVariantThumbnail(event, vIndex) {
      const file = event.target.files[0]
      if (file) this.form.variants[vIndex].thumbnail_image = file
      event.target.value = null
    },
    removeVariantThumbnail(vIndex) {
      this.form.variants[vIndex].thumbnail_image = null
    },
    addVariantMedia(event, vIndex) {
      const files = Array.from(event.target.files)
      this.form.variants[vIndex].media_gallery.push(...files)
      event.target.value = null
    },
    removeVariantMedia(vIndex, fIndex) {
      this.form.variants[vIndex].media_gallery.splice(fIndex, 1)
    },
    slugify(text) {
      return text
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
    },
    updateSlug() {
      if (!this.manualSlugChange) {
        this.form.slug = this.slugify(this.form.label)
      }
    },
    manualSlugEdit() {
      this.manualSlugChange = true
    },
    submit() {
      this.form.post(route('products.store'), {
        onSuccess: () => {
          this.$inertia.visit('/cms/crm/products')
        },
        onError: (errors) => {
          console.error('Submission failed:', errors)
        },
      })
    },
  },
      mounted() {
       this.getCategories();
    },
}
</script>

<style scoped>
.variant-block {
  border: 1px solid #ddd;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 6px;
}

.item-block {
  padding: 0.5rem 0;
  border-top: 1px dashed #ccc;
}

.form-field {
  display: flex;
  gap: 6px;
}
</style>
