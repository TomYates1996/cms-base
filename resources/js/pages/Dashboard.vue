<template>
    <div class="page">
        <div class="left-section">
            <OptionsBar/>
        </div>
        <div class="right-section">
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
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import Pages from './cms/pages/Pages.vue';
import Slides from './cms/Slides.vue';
import OptionsBar from '@/components/cms/structure/OptionsBar.vue';


export default {
    components: {
      Link,
      Pages,
      Slides,
      OptionsBar,
    },
}
</script>

<style>
    .page {
        display: flex;
        width: 100%;
    }
    .left-section {
        width: 25%;
    }
    .right-section {
        width: 75%;
    }
</style>