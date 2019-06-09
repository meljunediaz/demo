<template>
    <b-container class="overflow-auto">
        <m-menu />
       <h2>Error 404. Page not found</h2>
    </b-container>
</template>
 
<script>
    import MMenu from '../components/MMenu'

    export default {
        name: "notFound",
        components: {
          MMenu
        },
    }
</script>
 