<template>
  <form class="edit-page-info form" @submit.prevent="createBlogPost()" aria-label="Create New Post">
    <fieldset class="form-inner">
      <legend class="form-title">Create New Blog</legend>

      <div class="form-page-title form-field">
        <label for="title">Post Title</label>
        <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" @input="updateSlug"/>
      </div>

        <div class="form-slide-image form-field">
            <button type="button" class="btn-default add-img" @click="imageList()"
                aria-controls="image-grid" aria-expanded="showImageGrid.toString()">
                Select Image
            </button>
        </div>
        <div v-if="showImageGrid" id="image-grid" class="image-grid" role="region" aria-label="Image selection">
            <img v-for="image in images" :key="image.id" class="new-slide-img-option"
            @click="addImageToSlide(image)" :src="'/' + image.image_path"
            :alt="image.image_alt || 'Slide image option'" role="button" tabindex="0" />
            <NewImage @refreshImages="getImages()" />
        </div>
        <div v-if="imagePreview" class="image-preview form-field" role="region" aria-label="Selected image preview">
        <p class="sr-only">Preview Image</p>
        <img :src="imagePreview" alt="Preview of selected slide image" />
        </div>

        <label class="inline-flex items-center cursor-pointer">
        <input
            type="checkbox"
            v-model="form.is_featured"
            class="sr-only peer"
            id="is_featured"
        />
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-200"></div>
        <span class="ml-3 text-sm font-medium text-gray-900">Featured</span>
        </label>

        <label class="inline-flex items-center cursor-pointer">
        <span class="mr-3 text-sm font-medium text-gray-900">Publish</span>

        <input
            type="checkbox"
            :checked="form.status === 'published'"
            @change="form.status = $event.target.checked ? 'published' : 'draft'"
            class="sr-only peer"
            id="statusToggle"
        />

        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-colors duration-200"></div>
        </label>


        <div class="border rounded-lg">
            <button
                type="button"
                @click="showExtraSettings = !showExtraSettings"
                class="w-full flex justify-between items-center px-4 py-2 text-left bg-gray-100 hover:bg-gray-200 rounded-t-lg"
            >
                <span class="font-medium text-gray-700">Extra Settings</span>
                <svg
                :class="{ 'rotate-180': showExtraSettings }"
                class="w-5 h-5 transform transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div v-if="showExtraSettings" class="p-4 space-y-4 bg-white border-t">
                <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                <input
                    type="text"
                    id="meta_title"
                    v-model="form.meta_title"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                />
                </div>

                <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                <textarea
                    id="meta_description"
                    v-model="form.meta_description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                ></textarea>
                </div>
            </div>
            </div>

            <!-- 'category_id' => 'nullable|exists:categories,id', -->

      <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Blog
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
import NewImage from '../slides/images/NewImage.vue';

export default {
  setup(){
      const form = useForm({
          'title' : '',
          'slug' : '',
          'created_by' : '',
          'image_id': null,
          'image_path': null,
          'is_featured' : false,
          'meta_title' : '',
          'status' : 'draft',
          'meta_description' : '',
      });

      return { form } 
  },
  props: {
  },
  data () {
    return {
      manualSlugChange: false, 
      imagePreview: null,
      images: [],
      showImageGrid: false,
      showExtraSettings: false,
    }
  },
  created() {
  },
  components: {
      LoaderCircle,
      NewImage,
  },
  methods: {
        createBlogPost () {
          this.form.post(route('api.blogs.store'), {
          onSuccess: () => {
              this.$inertia.visit(`/cms/blog`);  
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
        getImages() {
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images; 
                console.log(this.images);
                
            })
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image) {
            this.form.image_id = image.id;
            this.form.image_path = image.image_path;
            this.imagePreview = '/' + image.image_path;
            this.showImageGrid = false;
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
.image-grid {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background-color: var(--white);
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    padding: 20px;
    overflow: scroll;
}

.new-slide-img-option {
    width: 100%;
    object-fit: cover;
    aspect-ratio: 1/1;
}

.image-preview {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.image-preview img {
    max-width: 200px;
    object-fit: contain;
}
form.edit-page-info {
    flex-direction: column;
}
.edit-page-info input {
    color: var(--black);
}

.page-list {
    .page-item {
        display: grid;
        grid-template-columns: repeat( 5, 1fr);
        padding: 8px 20px;
        .options-section {
            display: flex;
            gap: 20px;
        }
    }
    .table-head {
        border-bottom: 1px solid var(--black);
    }
}

.form-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    padding: 20px 0px;
    .form-title {
        font-size: 22px;
        font-weight: 600;
    }
    .form-field {
        display: flex;
        gap: 10px;
        align-items: center;
        label {
            font-size: 18px;
        }
    }
    input {
        border: 1px solid var(--black);
        padding: 4px;
        border-radius: 2px;
    }
}
</style>