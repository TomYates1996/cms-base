<template>
    <div class="wrap">
        <ul>
          <li v-for="(item, index) in items" :key="index">
            <button @click.prevent="select(item)">
                {{ item.label }}
            </button>
          </li>
        </ul>
        <button class="btn-default" @click.prevent="showNew = !showNew" >New {{ type }} item</button>
        <NewItem v-if="showNew" :type="type" @created="emitCreated"/>
    </div>
</template>

<script>
import NewItem from './NewItem.vue';

export default {
    components: {
        NewItem,
    },
    props: {
        items: Array,
        type: String,
    }, 
    data () {
        return {
            showNew: false
        }
    },
    methods: {
        emitCreated(newItem) {
            this.showNew = !this.showNew;
            this.$emit('created', newItem);
        },
        select(item) {
            this.$emit('selected', item);
        },
    },
}
</script>

<style scoped>
    .wrap {
        position: absolute;
        top: 0px;
        left: 0px;
        background-color: red;
        height: 100%;
        width: 100%;
    } 
</style>