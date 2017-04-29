<template>
  <transition name='fade' enter-active-class='fadeInDown' leave-active-class='fadeOutDown'>
    <div class='alert notification animated' :class='notification.type'>
      <a class='close' @click='deleteNotif' aria-label='close'>&times;</a>
      <p>{{notification.text}}</p>
    </div>
  </transition>
</template>

<script>
  import notif from '@/stores/Notifications.js'

  export default {
    props: ['notification'],
  
    methods: {
      deleteNotif () {
        notif.removeNotification(this.notification)
      }
    }
  }
</script>

<style scoped>
  p {
    margin-right: 20px;
  }
</style>

