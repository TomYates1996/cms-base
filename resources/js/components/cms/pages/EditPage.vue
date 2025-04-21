<template>
    <section class="page-list" aria-label="List of Pages">
      <table class="table">
        <thead>
          <tr class="table-head">
            <th scope="col" aria-hidden="true"></th>
            <th scope="col" aria-hidden="true"></th>
            <th scope="col" aria-hidden="true"></th>
            <th scope="col" aria-hidden="true"></th>
            <th scope="col" aria-hidden="true">&nbsp;</th>
          </tr>
        </thead>
      </table>
    </section>
  
    <form class="edit-page-info form" @submit.prevent="updatePage(form)" aria-label="Edit Page Information">
      <fieldset class="form-inner">
        <legend class="form-title">Edit Page Information</legend>
  
        <div class="form-page-title form-field">
          <label for="title">Page Title</label>
          <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" />
        </div>
  
        <div class="form-slug form-field">
          <label for="slug">Slug</label>
          <input id="slug" name="slug" type="text" required v-model="form.slug" aria-required="true" />
        </div>
  
        <div class="form-show-nav form-field">
          <label id="show-in-nav-label">Show in Navigation:</label>
          <button class="show-in-nav btn-default" @click.prevent="toggleShow" type="button" :aria-pressed="form.show_in_nav" aria-labelledby="show-in-nav-label">
            {{ form.show_in_nav ? 'No' : 'Yes' }}
          </button>
        </div>
  
        <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Changes
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
import { router } from '@inertiajs/vue3'


export default {
    setup(){
        const form = useForm({
            'title' : '',
            'slug' : '',
            'show_in_nav' : false,
            'created_by' : '',
        });

        return { form } 
    },
    components: {
        LoaderCircle,
    },
    props: {
        page: Object,
    },
    created() {
        this.form.title = this.page.title,
        this.form.slug = this.page.slug,
        this.form.created_by = this.page.created_by,
        this.form.show_in_nav = this.page.show_in_nav ? this.page.show_in_nav : true
    },
    methods: {
        updatePage (form) {
            form.put(route('api.pages.update', this.page.slug), {
            onSuccess: () => {
                router.get(`/cms/pages/${this.page.section}`);
            },
            onError: (errors) => {
            console.log('Form submission error:', errors);
            },
        });
        },
        toggleShow() {
            this.form.show_in_nav = !this.form.show_in_nav;
        },
        cancelNew() {
            this.$emit('cancelNew')
        },
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