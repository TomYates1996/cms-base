<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewLayout v-if="showNewLayout" @cancelNew="showNewLayout = false" />
        <div v-if="!showNewLayout" class="layout-list-wrap">
          <h1 class="crm-header">Layouts</h1>
          <ul class="layout-list">
            <li v-for="layout in localLayouts" :key="layout.id" class="layout-list-item">
              <p>{{ layout.title }}</p>
                <button v-if="$page.props.auth.user" class="option" @click="deleteLayout(layout.id)" title="Delete layout" aria-label="Delete layout: {{ layout.title }}">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                <Link v-if="$page.props.auth.user" :href="`/cms/layouts/edit-content/${layout.id}`" title="Edit content" method="get" class="option" role="button" aria-label="Edit content for {{ layout.title }}">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                    </Link>
                </li>
            </ul>
        </div>
        </div>
        <div class="page-right">
            <button class="new-layout" @click="showNewLayout = !showNewLayout" aria-expanded="showNewLayout" aria-controls="newLayoutModal">{{ showNewLayout ? 'Cancel' : 'New Layout' }}</button>
        </div>
    </div>
</template>
  

<script>
import NewLayout from '@/components/cms/layouts/NewLayout.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewLayout,
    },
    props: {
        layouts: Array,
    },
    data() {
        return {
            showNewLayout: false,
            localLayouts: [],
        }
    },
    created() {
        this.localLayouts = this.layouts;
    },
    methods: {
        newLayout() {
            this.showNewLayout = true;
        },
        deleteLayout(layout_id) {
            if (confirm("Are you sure you want to delete this layout?")) {
                router.delete(`/cms/layouts/delete/${layout_id}`, {
                onSuccess: () => {
                    this.localLayouts = this.localLayouts.filter(item => item.id !== layout_id);
                },
                onError: (errors) => {
                    console.log('Error deleting page:', errors);
                }
                });
            }
        },
    },
}
</script>

<style scoped>
    .page-wrap {
        display: flex;
        gap: 20px;
        .page-left {
            width: 80%;
        }
        .page-right {
            width: 20%;
            border-left: 2px solid var(--black);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    }
    .layout-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .layout-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>