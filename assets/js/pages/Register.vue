<template>
  <b-container>
    <m-menu />
    <h2>Sign-up here</h2>
    <b-alert show variant="success" v-show="success">{{ success }}</b-alert>
    <b-alert show variant="danger" v-show="error.length">{{ error }}</b-alert>


    <b-form @submit="onSubmit" v-if="show">


<b-modal id="bv-modal-example" hide-footer>
    <template slot="modal-title">
      Upload a Photo
    </template>
    <div class="d-block text-center">
      <input type="file" name="image" accept="image/*"
           style="font-size: 1.2em; padding: 10px 0;"
           @change="setImage" />
        <br/>

        <vue-cropper
          ref='cropper'
          :guides="true"
          :view-mode="2"
          drag-mode="crop"
          :auto-crop-area="0.5"
          :min-container-width="250"
          :min-container-height="180"
          :background="true"
          :rotatable="true"
          :src="imgSrc"
          alt="Source Image"
          :img-style="{ 'width': '400px', 'height': '300px' }"
          v-if="imgSrc.length"
          >
        </vue-cropper>

        <br>

        <b-button variant="primary" @click="cropImage" v-if="imgSrc != ''" style="margin-right: 40px;">Crop</b-button>
        <b-button variant="danger" @click="rotate" v-if="imgSrc != ''">Rotate</b-button>


    </div>
    <b-button class="mt-3" block @click="$bvModal.hide('bv-modal-example')">Close</b-button>
  </b-modal>
</div>


    

         <b-form-group id="input-group-2">
         <b-button id="show-btn" @click="$bvModal.show('bv-modal-example')">Upload Profile Photo</b-button>
        </b-form-group>

        <b-form-group id="input-group-2">
            <div>
                <b-img v-bind="mainProps" :src="cropImg" rounded alt="Crop image" v-if="cropImg.length"></b-img>
            </div>
        </b-form-group>



        

      <b-form-group id="input-group-2" label="Your Name:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.name"
          required
          placeholder="Enter name"
        ></b-form-input>
      </b-form-group>


      <b-form-group
        id="input-group-1"
        label="Email address:"
        label-for="input-1"
        description="Enter your unique email-address"
        >
        <b-form-input
          id="input-1"
          v-model="form.email"
          type="email"
          required
          placeholder="Enter email"
        ></b-form-input>
      </b-form-group>      


      <b-form-group id="input-group-2" label="Password:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.password"
          required
          placeholder="Enter Password"
          type="password"
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-2" label="Confirm Password:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.password1"
          required
          placeholder="Confirm Password"
          type="password"
        ></b-form-input>
      </b-form-group>


      <b-form-group id="input-group-3" label="Gender:" label-for="input-3">
        <b-form-select
          id="input-3"
          v-model="form.gender"
          :options="gender_opts"
          required
        ></b-form-select>
      </b-form-group>


      <b-form-group id="input-group-2" label="Address:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.address"
          placeholder="Enter address"
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-2" label="Describe yourself:" label-for="input-2">
        <b-form-textarea
          id="input-2"
          v-model="form.description"
          placeholder="Describe yourself"
        ></b-form-textarea>
      </b-form-group>

        
    <b-alert show variant="success" v-show="success">{{ success }}</b-alert>
    <b-alert show variant="danger" v-show="error.length">{{ error }}</b-alert>

      <b-button type="submit" variant="primary">Submit</b-button>
      
    </b-form>
   
  </b-container>
</template>

<script>

  import axios from 'axios'
  import VueCropper from 'vue-cropperjs';
  import 'cropperjs/dist/cropper.css';

  import MMenu from '../components/MMenu'

  export default {
    name: "Register",
    components: {
      VueCropper,
      MMenu
    },
    data() {
      return {
        form: {
          email: '',
          name: '',
          address: '',
          description: '',
          password: '',
          password1: '',
          gender: null,
          cropImg: '',
        },

        error: '',
        success: '',
        gender_opts: [{ text: 'Male', value: "m" }, { text: 'Female', value: "f" }],
        show: true,
        imgSrc: '',
        cropImg: '',
        mainProps: { width: 200, height: 200, class: 'm1' }
      }
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault()
        
        this.error = "";
        this.success = "";
        if(this.form.password != this.form.password1) {
            this.error = 'Password did not matched!'
        } else if(this.imgSrc != '' && this.cropImg == "") {
            this.error = 'It is required to crop the image before submitting the form!';
        }else {
            let url = "/api/createuser";

            this.form.cropImg = this.cropImg;

            axios({ method: "POST", "url": url, "data": this.form, "headers": { "content-type": "application/json" } }).then(
                result => {
                    this.error = result.data.error;
                    this.success = result.data.success;
                },
                error => {
                    console.error(error);
                }
            );
        }
      }, 

      setImage(e) {
        const file = e.target.files[0];
        if (!file.type.includes('image/')) {
          alert('Please select an image file');
          return;
        }
        if (typeof FileReader === 'function') {
          const reader = new FileReader();
          reader.onload = (event) => {
            this.imgSrc = event.target.result;
            // rebuild cropperjs with the updated source
            this.$refs.cropper.replace(event.target.result);
          };
          reader.readAsDataURL(file);
        } else {
          alert('Sorry, FileReader API not supported');
        }
      },
      cropImage(evt) {
        evt.preventDefault()
        // get image data for post processing, e.g. upload or setting image src
        this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
      },
      rotate(evt) {
        evt.preventDefault()
        // guess what this does :)
        this.$refs.cropper.rotate(90);
      },


    }
  }
</script>