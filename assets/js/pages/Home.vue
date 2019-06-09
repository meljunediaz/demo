<template>
	

	<b-container class="overflow-auto">
		<m-menu />
		
		<h2>This is my first DEMO Application with Symfony + Vue JS</h2>
		<p>Here are the list of registered Users in the System</p>

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
	      @filtered="onFiltered">

	       <template slot="gender" slot-scope="row">
	         {{ row.item.gender == 'm' ? 'Male' : 'Female' }}
	      </template>

		</b-table>


		<b-col md="3" class="my-1">
	      <b-pagination
	        v-model="currentPage"
	        :total-rows="rows"
	        :per-page="perPage"
	        aria-controls="my-table"
	      ></b-pagination>
	    </b-col>


	    <b-col md="3" class="my-1">
	      <b-form-group label-cols-sm="3" label="Per page" class="mb-0">
	        <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
	      </b-form-group>
	    </b-col>


  </b-container>


</template>

<script>

	import axios from 'axios'
	import MMenu from '../components/MMenu'

    export default {
        name: "Home",
        components : {
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
                pageOptions: [5, 10, 15, 20, 50, 100],
                fields: [
                  { key: 'id',  sortable: true },
                  { key: 'name',  sortable: true },
                  { key: 'email', sortable: true },
                  { key: 'gender', sortable: true },
                  { key: 'address', sortable: true },
                  { key: 'description', sortable: false },
                ],
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