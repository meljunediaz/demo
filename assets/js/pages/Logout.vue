<template>
	
    <b-container class="overflow-auto">

        <m-menu :key="renderComponent"/>

        <!-- <button @click='aw'></button> -->

       <h2>You've been logout!</h2>
    </b-container>
</template>


<script>

    import MMenu from '../components/MMenu'

    export default {
        name: "Logout",
        data() {
            return {               
                renderComponent: 1,
            }
        },
        components: {
          MMenu
        },

        mounted() {
            localStorage.removeItem('user_id');
            localStorage.removeItem('secure');
            localStorage.removeItem('isAdmin');
            this.renderComponent += 1;


        }, 
    }
</script>