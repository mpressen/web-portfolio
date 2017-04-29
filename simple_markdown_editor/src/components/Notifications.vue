<template>
    <div>
        <notification add key with v-for="notification in notifications" :notification="notification" transition="fade">
        </notification>
    </div>
</template>


<script>
    import notification from './partials/Notification.vue'
    import notif from '@/stores/Notifications.js'

    export default {
      data () {
        return {
          notifications: notif.state
        }
      },

      components: {
        notification
      }
    }
</script>

<style scoped>
    div {
        position: fixed;
        right: 10px;
        top: 60px;
        width: 300px;
        z-index: 1;
    }
</style>
