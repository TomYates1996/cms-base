<template>
  Layouts
    <button class="new-layout" @click="newLayout">New Layout</button>
    <NewLayout v-if="showNewLayout" @cancelNew="showNewLayout = false"/>
    <ul>
        <li v-for="layout in localLayouts" :key="layout.id">
            <p>{{ layout.title }}</p>
            <button v-if="$page.props.auth.user" class="option" @click="deleteLayout(layout.id)" title="Delete layout" aria-label="Delete {{ layout.title }}"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
            <Link v-if="$page.props.auth.user" :href="`/cms/layouts/edit-content/${layout.id}`" title="Edit content" method="get" class="option" role="button" aria-label="Edit content for {{ page.title }}"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></Link>
        </li>
    </ul>

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

<style>

</style>