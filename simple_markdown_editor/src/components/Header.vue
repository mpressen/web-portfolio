<template>
  <div>
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <div class="navbar-brand">
            <img src="../../static/img/markdown_favicon.png"  alt="Markdown">
            <span>arkdown</span>
          </div>
        </div>
        <p class="collapse navbar-collapse"> A simple markdown editor ...</p>
      </div>
    </nav>
  </div>
</template>


<script>
  export default {
    data () {
      return {
      }
    },

    components: {
    }
  }
</script>

<style scoped>


  img {
    width:40px;
    height:40px;
    display: inline;
    margin-top: -10px;
  }

  span {
    margin-left: -3px;
    color: black;
    font-weight: bold;
  }

  p {
    margin-top: 19px;
    margin-bottom: 0px;
    font-style: italic;
    float: none;
    text-align: center;
  }
</style>
