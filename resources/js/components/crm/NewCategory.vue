<template>
  <form class="edit-page-info form" @submit.prevent="createCategory()" aria-label="Create New Category">
    <fieldset class="form-inner">
      <legend class="form-title">Create New Category</legend>

      <div class="form-page-title form-field">
        <label for="title">Category Title</label>
        <input id="title" name="title" type="text" required v-model="form.name" autofocus aria-required="true" @input="updateSlug"/>
      </div>

      <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Category
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
            'name' : '',
            'slug' : '',
        });

        return { form } 
    },
    props: {
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
        createCategory () {
            this.form.post(route('api.category.store'), {
                onSuccess: () => {
                    this.$inertia.visit(`/cms/crm/categories`);  
                },
                onError: (errors) => {
                    console.log('Form submission error:', errors); 
                },
            });
        },
        cancelNew() {
            this.$emit('cancelNew')
        },
        updateSlug() {
            if (!this.manualSlugChange) {
            this.form.slug = this.slugify(this.form.name); 
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
            if (!this.form.name) {
            this.manualSlugChange = false;
            this.form.slug = ''; 
            }
        },
    },
    watch: {
        'form.name'() {
        this.resetSlugOnTitleChange();
        }
    },
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