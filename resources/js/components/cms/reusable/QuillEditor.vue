<template>
  <div>
    <div ref="editor" class="quill-editor" />
  </div>
</template>

<script>
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

export default {
  name: 'QuillEditor',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    options: {
      type: Object,
      default: () => ({
        theme: 'snow',
        modules: {
          toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'image'],
            ['clean']
          ]
        }
      })
    }
  },
  emits: ['update:modelValue'],
  mounted() {
    this.quill = new Quill(this.$refs.editor, this.options)

    this.quill.root.innerHTML = this.modelValue || ''

    this.quill.on('text-change', () => {
      this.$emit('update:modelValue', this.quill.root.innerHTML)
    })
  },
  watch: {
    modelValue(newValue) {
      if (this.quill && newValue !== this.quill.root.innerHTML) {
        this.quill.root.innerHTML = newValue
      }
    }
  }
}
</script>

<style scoped>
.quill-editor {
  min-height: 200px;
}
</style>