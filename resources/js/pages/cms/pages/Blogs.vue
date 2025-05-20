<template>
    <div class="page-wrap">
      <div class="page-left">
        <NewBlog v-if="showNewBlogPost" @cancelNew="showNewBlogPost = false" />
        <div v-if="!showNewBlogPost" class="blog-list-wrap">
          <h1>Blogs</h1>
          <ul class="blog-list">
            <li v-for="blog in blogPosts" :key="blog.id" class="blog-list-item">
              <p>{{ blog.title }}</p>
                <button v-if="$page.props.auth.user" class="option" @click="deleteBlog(blog.id)" title="Delete blog" aria-label="Delete blog: {{ blog.title }}">
                    <font-awesome-icon :icon="['fas', 'trash-can']" />
                </button>
                <Link v-if="$page.props.auth.user" :href="`/cms/blog/edit-content/${blog.id}`" title="Edit content" method="get" class="option" role="button" aria-label="Edit content for {{ layout.title }}">
                    <font-awesome-icon :icon="['fas', 'pen-to-square']" />
                    </Link>
                </li>
            </ul>
        </div>
        </div>
        <div class="page-right">
            <button class="new-layout" @click="showNewBlogPost = !showNewBlogPost" aria-expanded="showNewBlogPost" aria-controls="newBlogPostModal">{{ showNewBlogPost ? 'Cancel' : 'New Blog' }}</button>
        </div>
    </div>
</template>
  

<script>
import NewBlog from '@/components/cms/blog/NewBlog.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewBlog,
    },
    props: {
        blogPosts: Array,
    },
    data() {
        return {
            showNewBlogPost: false,
            localBlogPosts: [],
        }
    },
    created() {
        this.localBlogPosts = this.blogPosts;
    },
    methods: {
        newBlog() {
            this.showNewBlogPost = true;
        },
        deleteBlog(blog_id) {
            if (confirm("Are you sure you want to delete this blog post?")) {
                router.delete(`/cms/blog/delete/${blog_id}`, {
                onSuccess: () => {
                    this.localBlogPosts = this.localBlogPosts.filter(item => item.id !== blog_id);
                },
                onError: (errors) => {
                    console.log('Error deleting blog:', errors);
                }
                });
            }
        },
    },
}
</script>

<style scoped>
    .page-wrap {
        display: flex;
        gap: 20px;
        .page-left {
            width: 80%;
        }
        .page-right {
            width: 20%;
            border-left: 2px solid var(--black);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    }
    .blog-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .blog-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>