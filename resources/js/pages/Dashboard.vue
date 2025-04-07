<template>
    <Link 
        v-if="$page.props.auth.user"
        href="/cms/pages"
        method="get"
        class="option"
    >
        Pages
    </Link>
    <Link 
        v-if="$page.props.auth.user"
        href="/cms/slides"
        method="get"
        class="option"
    >
        Slides
    </Link>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import Pages from './cms/pages/Pages.vue';
import Slides from './cms/Slides.vue';


export default {
    components: {
      Link,
      Pages,
      Slides
    },
}
</script>

<style>

</style>