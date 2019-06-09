<template>



  <b-container class="overflow-auto">
    <m-menu />
    <b-container fluid>
      <!-- User Interface controls -->

      <b-row>
        <b-col md="6" class="my-1">
          <b-form-group label-cols-sm="3" label="Filter" class="mb-0">
            <b-input-group>
              <b-form-input v-model="filter" placeholder="Type to Search"></b-form-input>
              <b-input-group-append>
                <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-col>
      </b-row>
    </b-container>   


    


    


    <b-alert show variant="success" v-show="success">{{ success }}</b-alert>

     <b-table
      show-empty
      stacked="md"
      :items="items"
      :fields="fields"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      ref="table"
      @filtered="onFiltered"
      >


      <template slot="actions" slot-scope="row">
       

        <b-button size="sm" @click="showEditModal(row.item.id)" class="mr-1">
          Modal Edit
        </b-button>

        <b-button size="sm" @click="deleteUser(row.item.id)" class="mr-1">
          Delete
        </b-button>

         <b-button size="sm" @click="row.toggleDetails">
          {{ row.detailsShowing ? 'Close Form' : 'Inline Edit' }}
        </b-button>
        
      </template>

      <template slot="gender" slot-scope="row">
         {{ row.item.gender == 'm' ? 'Male' : 'Female' }}
      </template>


      <!-- INLINE EDITOR -->
      <template slot="row-details" slot-scope="row">
        <b-alert show variant="danger" v-show="inline_error.length">{{ inline_error }}</b-alert>
        <b-form @submit="saveInline($event,row.item)">
          <b-card>
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Name:</b></b-col>
              <b-col>
                
                 <input type="hidden" v-model="row.item.id">


                <b-form-input
                  id="input-2"
                  v-model="row.item.name"
                  required
                  placeholder="Enter name"
              ></b-form-input>
              </b-col>
            </b-row>

            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Email:</b></b-col>
              <b-col>
                <b-form-input
                  id="input-2"
                  v-model="row.item.email"
                  required
                  type="email"
                  placeholder="Enter email"
              ></b-form-input>
              </b-col>
            </b-row>

            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Address:</b></b-col>
              <b-col>
                <b-form-input
                  id="input-2"
                  v-model="row.item.address"
                  placeholder="Enter address"
              ></b-form-input>
              </b-col>
            </b-row>

            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Desribe yourself:</b></b-col>
              <b-col>
                <b-form-textarea
                    id="input-2"
                    v-model="row.item.description"
                    placeholder="Describe yourself"
                  ></b-form-textarea>
              </b-col>
            </b-row>

            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Gender:</b></b-col>
              <b-col>
                <b-form-select
                  id="input-3"
                  v-model="row.item.gender"
                  :options="gender_opts"
                  required
                ></b-form-select>
              </b-col>
            </b-row>

            

            <b-button size="sm" @click="row.toggleDetails">Close Form</b-button>
            <b-button size="sm" type='submit'>Submit</b-button>
          </b-card>
        </b-form>
      </template>
      
    </b-table>

    <b-col md="3" class="my-1">
      <b-pagination
        v-model="currentPage"
        :total-rows="rows"
        :per-page="perPage"
        aria-controls="my-table"
      ></b-pagination>

      <b-col md="3" class="my-1">
      <b-form-group label-cols-sm="3" label="Per page" class="mb-0">
        <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
      </b-form-group>
      </b-col>

    </b-col>



    <!-- MODAL -->

      <b-modal ref="my-modal" hide-footer title="Edit User">
      <b-form @submit="onSubmit">

      <b-alert show variant="danger" v-show="error.length">{{ error }}</b-alert>
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
          placeholder="Enter Password"
          type="password"
        ></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-2" label="Confirm Password:" label-for="input-2">
        <b-form-input
          id="input-2"
          v-model="form.password1"
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

      
      </b-form-group>

      <b-button type="submit" variant="primary">Submit</b-button>
      
    </b-form>

    </b-modal>


  </b-container>

</template>

<script>

	import axios from 'axios'

    import MMenu from '../components/MMenu'
    export default {
        name: "User",
        components: {
          MMenu
        },
        data() {
          return {
                filter : '',   
                perPage: 10,
                currentPage: 1,
                items: [],
                sortBy: 'id',
                sortDesc: false,
                sortDirection : '',
                pageOptions: [5, 10, 15, 20, 50, 100],
                confirm: '',
                success: '',
                error: '',
                inline_error: '',
                fields: [
                  { key: 'id',  sortable: true },
                  { key: 'name',  sortable: true },
                  { key: 'email', sortable: true },
                  { key: 'address', sortable: true },
                  { key: 'description', sortable: false },                  
                  { key: 'gender', sortable: true },
                  { key: 'actions', label: 'Actions' }
                ],

                form: {
                  email: '',
                  name: '',
                  address: '',
                  description: '',
                  password: '',
                  password1: '',
                  gender: null,
                  id : '',
                },
                gender_opts: [{ text: 'Male', value: "m" }, { text: 'Female', value: "f" }],

        
          }
        },
        computed: {
          rows() {
            return this.items.length
          }
        },

        methods : {
          onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
          },

          deleteUser(id) {
            this.success = ''
            this.boxOne = ''
            this.$bvModal.msgBoxConfirm('Are you sure?')
              .then(value => {
                if(value == true) {

                  let url = "/api/delete";
                  axios({ method: "POST", "url": url, "data": {'id' : id}, "headers": { "content-type": "application/json" } }).then(
                      result => {
                        const index = this.items.findIndex(post => post.id === id) // find the post index 
                        if (~index) // if the post exists in array
                           this.items.splice(index, 1) //delete the post

                         this.success = 'ID: ' + id + ' has been deleted!';
                      },
                      error => {
                          console.error(error);
                      }
                  );


                }
              })
              .catch(err => {
                // An error occurred
              })
          },
          
          showEditModal(id) {
            this.form.id = id;

            let url = "/api/profile";
            axios({ method: "POST", "url": url, "data": {"id" : id}, "headers": { "content-type": "application/json" } }).then(
                result => {
                  this.form = result.data.data
                  this.$refs['my-modal'].show()
                },
                error => {
                    console.error(error);
                }
            );

          },
          hideModal() {
            this.$refs['my-modal'].hide()
          },

          onSubmit(evt) {
            evt.preventDefault()
            
            this.error = "";
            this.success = "";
            if((this.form.password || this.form.password1) && this.form.password != this.form.password1) {
                this.error = 'Password did not matched!'
            } else {
                let url = "/api/updateuser";
                axios({ method: "POST", "url": url, "data": this.form, "headers": { "content-type": "application/json" } }).then(
                    result => {
                        this.error = result.data.error;
                        this.success = result.data.success;

                        const index = this.items.findIndex(post => post.id === this.form.id) // find the post index 
                        if (~index) {// if the post exists in array
                           // this.form.gender =  this.form.gender == "m" ? "Male" : "Female"
                           this.items[index] = this.form
                           this.$refs.table.refresh()

                        }
                        this.$refs['my-modal'].hide()

                    },
                    error => {
                        console.error(error);
                    }
                );
            }
          },

          // INLINE EDIT FUNCTIONS
          saveInline(evt, rowData) {
            evt.preventDefault()
            this.form = rowData
            this.inline_error = "";
            this.success = "";
            let url = "/api/updateuser";
                axios({ method: "POST", "url": url, "data": this.form, "headers": { "content-type": "application/json" } }).then(
                    result => {
                        this.inline_error = result.data.error;
                        this.success = result.data.success;

                        const index = this.items.findIndex(post => post.id === this.form.id) // find the post index 
                        if (~index) {// if the post exists in array
                           // this.form.gender =  this.form.gender == "m" ? "Male" : "Female"
                           this.items[index] = this.form
                           this.$refs.table.refresh()

                        }
                        this.$refs['my-modal'].hide()

                    },
                    error => {
                        console.error(error);
                    }
                );
            
          }


        },

        mounted() {
            let url = "/api/lists";
            axios({ method: "POST", "url": url, "data": this.form, "headers": { "content-type": "application/json" } }).then(
                result => {
                  this.items = result.data.items
                },
                error => {
                    console.error(error);
                }
            );
        }, 
    }
</script>

