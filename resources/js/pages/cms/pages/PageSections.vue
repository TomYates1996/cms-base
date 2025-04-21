<template>
    <Link 
        v-if="$page.props.auth.user"
        href="/cms/pages/primary"
        method="get"
        class="option"
    >
        Primary
    </Link>
    <Link 
        v-if="$page.props.auth.user"
        href="/cms/pages/secondary"
        method="get"
        class="option"
    >
        Secondary
    </Link>
    <Link 
        v-if="$page.props.auth.user"
        href="/cms/pages/footer"
        method="get"
        class="option"
    >
        Footer
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
      Link,
    },
}
</script>

<style>

</style>