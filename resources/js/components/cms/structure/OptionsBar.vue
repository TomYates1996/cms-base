<template>
  <nav>
    <a href="/cms/dashboard" class="app-name" aria-label="Go to CMS dashboard">{{ this.$appName }}</a>
    <ul>
        <li>
            <button @click="toggle('pages')" :aria-expanded="this.expanded.pages" :aria-controls="'section-pages'" class="option accordion-toggle">
                Pages
                <span aria-hidden="hidden"><font-awesome-icon :icon="this.expanded.pages ? ['fas', 'sort-up'] : ['fas', 'sort-down']" /></span>
            </button>
            <Link 
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/primary"
                method="get"
                class="option nav-child"
            >
                Primary
            </Link>
            <Link 
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/secondary"
                method="get"
                class="option nav-child"
            >
                Secondary
            </Link>
            <Link 
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/footer"
                method="get"
                class="option nav-child"
            >
                Footer
            </Link>
        </li>
        <li>
            <Link 
                v-if="$page.props.auth.user"
                href="/cms/slides"
                method="get"
                class="option"
            >
                Slides
            </Link>
        </li>
        <li>
            <Link 
                v-if="$page.props.auth.user"
                href="/cms/images"
                method="get"
                class="option"
            >
                Images
            </Link>
        </li>
        <li>
            <Link 
                v-if="$page.props.auth.user"
                href="/cms/layouts"
                method="get"
                class="option"
            >
                Layouts
            </Link>
        </li>
        <li>
            <Link 
                v-if="$page.props.auth.user"
                href="/cms/blog"
                method="get"
                class="option"
            >
                Blog
            </Link>
        </li>
        <li>
            <button @click="toggle('crm')" :aria-expanded="this.expanded.crm" :aria-controls="'section-crm'" class="option accordion-toggle">
                CRM
                <span aria-hidden="hidden"><font-awesome-icon :icon="this.expanded.crm ? ['fas', 'sort-up'] : ['fas', 'sort-down']" /></span>
            </button>
            <Link 
                v-if="$page.props.auth.user && this.expanded.crm"
                href="/cms/crm/listings"
                method="get"
                class="option nav-child"
            >
                Listings
            </Link>
            <Link 
                v-if="$page.props.auth.user && this.expanded.crm"
                href="/cms/crm/events"
                method="get"
                class="option nav-child"
            >
                Events
            </Link>
        </li>
    </ul>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3';


export default {
    components: {
      Link,
    },
    data() {
        return {
            expanded: {
                pages: false,
            }
        }
    },
    methods: {
        toggle(navItem) {
            this.expanded[navItem] = !this.expanded[navItem]
        },
    },

}
</script>

<style scoped>
    nav {
        background-color: var(--pale-green);
        height: 100%;
        .app-name {
            padding: 10px 20px;
            display: flex;
            font-weight: 600;
        }
        ul {
            li {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                border-top: 2px solid var(--white);
                .option {
                    padding: 10px 20px;
                    width: 100%;
                    display: flex;
                    align-content: center;
                    justify-content: flex-start;
                }
                .option.nav-child {
                    border-top: 1px solid var(--white);
                    padding-left: 30px;
                    background-color: #8cb88e;
                }
                .accordion-toggle {
                    justify-content: space-between;
                }
            }
            li:first-of-type {
                border-top: 4px solid var(--black);
            }
        }
    }
</style>