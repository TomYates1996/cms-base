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
        <span class="total-views">Total Site Page Views: {{ totalViews }}</span>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

export default {
    props: {
        totalViews: String,
        topPages: Array,
    },
    components: {
      Link,
    },
}
</script>

<style>
    .top-pages {
        border: 1px solid var(--black);
        width: fit-content;
        display: flex;
        flex-direction: column;
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
    .total-views {
        margin-top: 20px;
        margin-left: auto;
        margin-right: 10px;
    }
</style>