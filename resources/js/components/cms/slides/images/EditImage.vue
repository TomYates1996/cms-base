<template>
  <form class="form new-image edit-page-info" @submit.prevent="updateImage()" aria-labelledby="edit-image-title">
    <fieldset class="form-inner">
      <legend id="edit-image-title" class="form-title">Edit Image</legend>

      <div class="form-field slide-title">
        <label for="title">Image Title</label>
        <input
          id="title"
          name="title"
          type="text"
          v-model="form.title"
          required
          autofocus
          autocomplete="off"
          aria-required="true"
        />
      </div>

      <div class="form-field slide-credits">
        <label for="credits">Credits</label>
        <input
          id="credits"
          name="credits"
          type="text"
          v-model="form.credits"
          autocomplete="off"
        />
      </div>

      <div class="form-field">
        <label for="photo">Image Upload</label>
        <input
          id="photo"
          name="photo"
          type="file"
          ref="photo"
          accept="image/*"
          @change="uploadImage($event)"
          class="hidden-file-input"
        />
        <label for="photo" class="upload-label" tabindex="0">Choose Image</label>
        <span v-if="form.image && form.image.name" class="file-name">{{ form.image.name }}</span>

        <div v-if="imagePreview" class="image-preview-con" aria-live="polite">
          <img :src="imagePreview" alt="New image preview" class="preview-image" />
        </div>
        <div v-else-if="form.image_path" class="image-preview-con">
          <img :src="'/' + form.image_path" alt="Current image" class="preview-image" />
        </div>
      </div>

      <div class="form-field slide-image_alt">
        <label for="image_alt">Image Alt Text</label>
        <input
          id="image_alt"
          name="image_alt"
          type="text"
          v-model="form.image_alt"
          required
          autocomplete="off"
          aria-required="true"
        />
      </div>

      <div class="form-actions">
        <button
          type="submit"
          class="btn-default"
          :disabled="form.processing"
          aria-label="Update Image"
        >
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
          <span v-else>Update Image</span>
        </button>

        <button
          type="button"
          class="btn-default cancel-new-image"
          @click.prevent="closeEditImage"
          aria-label="Cancel Edit Image"
        >
          Cancel
        </button>
      </div>
    </fieldset>
  </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import imageCompression from 'browser-image-compression';

export default {
  props: {
    image: Object,
  },
  components: {
    LoaderCircle,
  },
  setup() {
    const form = useForm({
      id: '',
      title: '',
      credits: '',
      image: null,
      image_path: '',
      image_alt: '',
    });
    return { form };
  },
  data() {
    return {
      imagePreview: null,
    };
  },
  created() {
    this.form.id = this.image.id;
    this.form.title = this.image.title;
    this.form.credits = this.image.credits;
    this.form.image_path = this.image.image_path;
    this.form.image_alt = this.image.image_alt;
  },
  emits: ['refreshImages', 'close-edit'],
  methods: {
    closeEditImage() {
      this.$emit('close-edit');
    },
    async uploadImage(event) {
      const file = event.target.files[0];
      if (!file) return;

      const options = {
        maxSizeMB: 1,
        maxWidthOrHeight: 1920,
        useWebWorker: true,
      };

      try {
        const compressedFile = await imageCompression(file, options);
        this.form.image = compressedFile;
        this.updatePreview(compressedFile);
        this.form.image_path = ''; // Clear old image path
      } catch (error) {
        console.error('Image compression failed:', error);
      }
    },
    updatePreview(imageFile) {
      const reader = new FileReader();
      reader.onloadend = () => {
        this.imagePreview = reader.result;
      };
      reader.readAsDataURL(imageFile);
    },
    updateImage() {
      this.form.post(route('images.update'), {
        onSuccess: () => this.$inertia.visit('/cms/images'),
        onError: (errors) => console.error('Form submission error:', errors),
      });
    },
  },
};
</script>

<style scoped>
.new-image {
  width: 100%;
  background-color: var(--white);
  overflow: scroll;
  padding: 20px 0;
}

.form-inner {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 20px 0;
}

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

.image-preview-con img {
  max-width: 100%;
  border-radius: 4px;
  margin-top: 10px;
}

.hidden-file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
  border: 0;
}

.upload-label {
  display: inline-block;
  padding: 0.5rem 1rem;
  background-color: var(--sea-green);
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.upload-label:focus {
  outline: 2px solid var(--black);
}

.file-name {
  margin-left: 1rem;
  font-size: 14px;
}
</style>
