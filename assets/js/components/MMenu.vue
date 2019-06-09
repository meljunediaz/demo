<template>
    
        <b-nav>

            <b-nav-item tag='div' to="/" v-if="is_logged_in">Home</b-nav-item>
            <b-nav-item tag='div' to="useraccount" v-if="is_logged_in">My Profile</b-nav-item>
            <b-nav-item tag='div' to="users" v-if="is_admin">Users</b-nav-item>
            <b-nav-item tag='div' to="register"  v-if="!is_logged_in">Register</b-nav-item>
            <b-nav-item tag='div' to="signin"  v-if="!is_logged_in">Login</b-nav-item>
            <b-nav-item tag='div' to="jobs">Jobs</b-nav-item>
            <b-nav-item tag='div' to="logout" v-if="is_logged_in">Logout</b-nav-item>

        </b-nav>

    
</template>


<script>
    let isGlobalLoggedIn = localStorage.user_id;
    let isGlobalAdmin = localStorage.isAdmin == 'true' ? true : false;

    export default {
        name: "MMenu",
        
        data() {
            return {
                is_logged_in : '',
                is_admin :  '',
            }
        },

        mounted() {
            this.is_logged_in =  localStorage.user_id
            this.is_admin =   localStorage.isAdmin == 'true' ? true : false


        },



        
    }
</script>