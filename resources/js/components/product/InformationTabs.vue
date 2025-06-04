<template>
    <div class="tabs">
        <!-- Tab list -->
        <div role="tablist" aria-label="Product Info Tabs">
            <button
            v-for="(tab, index) in tabs"
            :key="index"
            :id="`tab-${index}`"
            role="tab"
            :aria-selected="activeTab === index"
            :aria-controls="`panel-${index}`"
            :tabindex="activeTab === index ? 0 : -1"
            @click="selectTab(index)"
            @keydown.enter.space.prevent="selectTab(index)"
            class="tab-button"
            :class="{ active: activeTab === index }"
            >
            {{ tab.label }}
            </button>
        </div>

        <!-- Tab panels -->
        <div
            v-for="(tab, index) in tabs"
            :key="index"
            :id="`panel-${index}`"
            role="tabpanel"
            :aria-labelledby="`tab-${index}`"
            v-show="activeTab === index"
            class="tab-panel"
        >
            <p>{{ tab.content }}</p>
        </div>
    </div>
</template>

<script>


export default {
    props: {
        productDescription : '',
    },
    data() {
        return {
            activeTab: 0,
            tabs: [
                { label: 'Product Description', content: 'DescriptionTab' },
                { label: 'Delivery & Returns', content: "We hope that you are happy with your order, however, there may be instances where you wish to return an item. We ask that customers return items unused and with their original packaging. All returns should include a covering note stating the reason for the return and the order number clearly marked. Please retain the proof of postage until we have confirmed receipt of the items. We recommend sending by a recorded postage method. Refunds will be processed by the original payment method. If you paid on card, it can take up to 7 days to appear back in your account, with PayPal refunds taking up to 30 days refund if you've simply changed your mind, you have 30 days from the date of purchase to return your item(s) for a refund. Customers are responsible for return shipping costs where items are supplied correctly." },
                { label: 'Reviews', content: 'No reviews yet. Add yours.' }
            ]
        }
    },
    created() {
        this.tabs[0].content = this.productDescription;
    },
    methods: {
        selectTab(index) {
        this.activeTab = index
        }
    }
}
</script>

<style>
.tabs {
    .tab-button {
        padding: 0.5rem 1rem;
        border: none;
        background: #eee;
        cursor: pointer;
        &[aria-selected='true'] {
        background: #ccc;
        font-weight: bold;
        }
    }

    .tab-panel {
        padding: 1rem;
        border-top: 1px solid #ddd;
    }
}
</style>