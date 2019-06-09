<template>
   
        <b-container>
            <m-menu :key="renderComponent"/>
            <div>
            <h3>Login</h3>
           
            <b-alert show variant="danger" v-show="error.length">{{ error }}</b-alert>
                <b-form inline v-on:submit.prevent="login">
                 

                <label class="sr-only" for="inline-form-input-name">Email</label>
                <b-input
                id="inline-form-input-name"
                class="mb-2 mr-sm-2 mb-sm-0"
                placeholder="Username"
                v-model="form.username"
                ></b-input>

                <label class="sr-only" for="inline-form-input-name">Password</label>
                <b-input
                id="inline-form-input-name"
                class="mb-2 mr-sm-2 mb-sm-0"
                placeholder="Password"
                type="password"
                v-model="form.password"
                ></b-input>




                <b-button type="submit" variant="primary">Login</b-button>
            </b-form>
            </div>
        </b-container>

</template>

<script>
    import axios from 'axios'
    import MMenu from '../components/MMenu'

    
    export default {
        name: 'Login',
        data() {
            return {
                form: {
                    username: "",
                    password: "",
                },
                error : "",
                renderComponent: 0,
            }
        },
        components: {
          MMenu
        },
        methods: {
            login() {
                
                if(this.form.username != "" && this.form.password != "") {
                    let url = "/api/login";
                    axios({ method: "POST", "url": url, "data": this.form, "headers": { "content-type": "application/json" } }).then(
                        result => {
                            this.error = result.data.success == false ? "Invalid username and password" : "";

                            if(this.error == "") {
                                localStorage.user_id = result.data.user_id;
                                localStorage.secure = true;


                                // USER TYPE DETECTION WHILE LOGGING IN
                                if(result.data.user_type == "admin")
                                    localStorage.isAdmin = true;
                                else localStorage.isAdmin = false;

                                 this.renderComponent += 1; 

                                this.$router.replace({ name: "Home" });
                            }
                        },
                        error => {
                            console.error(error);
                        }
                    );
                } else {
                    this.error = 'Please provide username and password';
                }
            }
        }
    }
</script>
 