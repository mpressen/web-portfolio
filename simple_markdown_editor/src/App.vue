<template>
  <div id='app'>
    <my-header />
    <notifications />
    <editor />
    <my-footer />
  </div>
</template>

<script>
  import myHeader from './components/Header.vue'
  import notifications from './components/Notifications.vue'
  import editor from './components/Editor.vue'
  import myFooter from './components/Footer.vue'

  export default {
    name: 'app',

    components: {
      myHeader,
      notifications,
      editor,
      myFooter
    }
  }
</script>

<style>
  html, body, #app  {
    height: 100%;
  }
</style>
