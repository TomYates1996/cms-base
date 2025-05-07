<template>
  <button @click="$emit(onClickAction, item)" v-for="item in itemList" :key="item.id">{{ item.name }}</button>
</template>

<script>
export default {
    props: {
        itemList: Array,
        onClickAction: String,
    },
    emits: ['footers', 'headers'],
    created() {
        console.log(this.itemList);
    },
}
</script>

<style>

</style>