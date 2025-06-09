<template>
    <div class="top-pages">
        <h2>Most Visited Pages</h2>
        <table>
            <thead>
            <tr>
                <th>Rank</th>
                <th>Page</th>
                <th>Views</th>
                <th>Link</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(page, index) in topPages" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ page.title }}</td>
                <td>{{ page.views }}</td>
                <td>
                <Link :href="'/' + page.slug" :aria-label="'Go to ' + page.title + ' page'">
                    <font-awesome-icon :icon="['fas', 'person-walking']" />
                </Link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <span>Total Site Page Views: {{ totalViews }}</span>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import Pages from './cms/pages/Pages.vue';
import Slides from './cms/Slides.vue';
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';


export default {
    components: {
      Link,
      Pages,
      Slides,
      OptionsBar,
    },
    layout: CMSLayout,
    props: {
        totalViews: Number,
        topPages: Array,
    },
}
</script>

<style>
    .top-pages {
        border: 1px solid var(--black);
        width: fit-content;
        padding: 10px;
        h2 {
            font-size: 20px;
            padding-left: 10px;
        }
        th {
            text-align: left;
        }
        th, td {
            padding: 4px 10px;
        }
    }
</style>