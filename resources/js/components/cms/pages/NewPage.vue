<template>
  <form class="edit-page-info form" @submit.prevent="createPage()" aria-label="Create New Page">
    <fieldset class="form-inner">
      <legend class="form-title">Create New Page</legend>

      <div class="form-page-title form-field">
        <label for="title">Page Title</label>
        <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" @input="updateSlug"/>
      </div>

      <div class="form-slug form-field">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" required v-model="form.slug" :aria-required="true" @input="manualSlugEdit"/>
      </div>

        <div class="form-show-nav form-field">
          <label id="show-in-nav-label">Show in Navigation:</label>
          <button class="show-in-nav btn-default" @click.prevent="toggleShow" type="button" :aria-pressed="form.show_in_nav" aria-labelledby="show-in-nav-label">
            {{ form.show_in_nav ? 'No' : 'Yes' }}
          </button>
        </div>

      <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Page
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

export default {
  setup(){
      const form = useForm({
          'title' : '',
          'slug' : '',
          'show_in_nav' : true,
          'created_by' : '',
          'parent' : {},
          'section' : '',
      });

      return { form } 
  },
  props: {
    parent: Object,
    section: String,
  },
  data () {
    return {
      manualSlugChange: false, 
    }
  },
  components: {
      LoaderCircle,
  },
  methods: {
      createPage () {
        this.form.parent = this.parent;
        this.form.section = this.section;
        
        this.form.post(route('api.pages.store'), {
        onSuccess: () => {
          if (this.parent) {
            this.$inertia.visit(`/cms/pages/children/${this.parent.slug}`);  
          } else {
            this.$inertia.visit(`/cms/pages/${this.section}`);  
          }
        },
        onError: (errors) => {
          console.log('Form submission error:', errors); 
        },
      });
      },
      cancelNew() {
        this.$emit('cancelNew')
      },
      toggleShow() {
        this.form.show_in_nav = !this.form.show_in_nav;
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
  },
  watch: {
    'form.title'() {
      this.resetSlugOnTitleChange();
    }
  }

}
</script>

<style scoped>
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