<template>
<div class="details">
    <p>{{ details.firstName }} {{ details.surname }}</p>
    <p>{{ details.address1 }}</p>
    <p>{{ details.address2 }}</p>
    <p>{{ details.city }}</p>
    <p>{{ details.postcode }}</p>
</div>
</template>

<script>
export default {
    props: {
        details: Object,
    },
}
</script>

<style>
.details {
    border: 1px solid var(--grey);
    padding: 0px 8px;
    margin-left: auto;
    margin-right: 20px;
    p {
        font-size: 12px;
    }
}
</style>