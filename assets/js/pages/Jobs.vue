<template>
    <b-container class="overflow-auto">
        <m-menu />
    	<radius-search-map />
    </b-container>
</template>


<script>


  import RadiusSearchMap from "../components/RadiusSearchMap";
  import MMenu from '../components/MMenu'
    export default {
        name: "Jobs",

        data() {
            return {
               
            }
        },
        
        components: {
          RadiusSearchMap,
          MMenu
        },
        mounted() {
            

        }, 
    }
</script>