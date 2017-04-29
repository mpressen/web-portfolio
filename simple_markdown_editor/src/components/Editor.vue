<template>
<div class="container-fluid">
    <textarea id="left" :value="input" @input="update"></textarea>
    <div id="right" v-html="markdown"></div>
  </div>
</template>

<script>
  import {markdown} from 'markdown'
  import notif from '@/stores/Notifications.js'

  export default {
    data () {
      return {
        input: '# Hello there ! [You know Markdown ?](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet)'
      }
    },

    mounted () {
      notif.showNotification('Welcome Arthur !', 'alert-success', true)
    },

    computed: {
      markdown () {
        return markdown.toHTML(this.input)
      }
    },

    methods: {
      update (e) {
        this.input = e.target.value
      }
    }
  }
</script>

<style scoped>

  .container-fluid {
    height: 92%;
  }

  #right, #left {
    display: inline-block;
    width: 49%;
    height: 100%;
    box-sizing: border-box;
    padding: 0 20px;
    overflow-y: auto;
  }

  #left {
    border: none;
    resize: none;
    border-right: 1px solid #ccc;
    background-color: #f6f6f6;
    font-family: courier, monospace;
    padding-top : 20px;
  }

</style>
