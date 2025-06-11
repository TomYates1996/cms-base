<template>
  <form class="edit-page-info form" @submit.prevent="createCoupon()" aria-label="Create New Coupon">
    <fieldset class="form-inner">
      <legend class="form-title">Create New Coupon</legend>

      <div class="form-page-title form-field">
        <label for="title">Coupon Title</label>
        <input id="title" name="title" type="text" required v-model="form.code" autofocus aria-required="true"/>
      </div>

      <div class="form-page-title form-field">
        <label for="title">Discount</label>
        <input id="title" name="title" type="text" required v-model="form.discount_percentage" autofocus aria-required="true" />%
      </div>

      <div class="form-page-title form-field">
        <label for="title">Expires At</label>
        <input id="title" name="title" type="date" v-model="form.expires_at" autofocus aria-required="false" />
      </div>

      <button type="submit" class="btn-default" tabindex="5" :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" /> Save Coupon
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
            'code' : '',
            'discount_percentage' : 0,
            'expires_at' : '',
        });

        return { form } 
    },
    props: {
    },
    data () {
        return {
        }
    },
    components: {
        LoaderCircle,
    },
    methods: {
        createCoupon () {
            this.form.post(route('api.coupons.store'), {
                onSuccess: () => {
                    this.$inertia.visit(`/cms/crm/coupons`);  
                },
                onError: (errors) => {
                    console.log('Form submission error:', errors); 
                },
            });
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