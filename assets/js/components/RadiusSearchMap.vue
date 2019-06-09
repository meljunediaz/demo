  <template>
    <!-- <m-menu /> -->

    <b-container>
    <div>

    <div v-if="is_admin">
      <b-alert show variant="danger" v-show="error.length">{{ error }}</b-alert>
      <b-alert show variant="success" v-show="success">{{ success }}</b-alert>

      <!-- Styled -->
      <b-form-file 
        v-model="file"
        :state="Boolean(file)"
        placeholder="Choose a file..."
        drop-placeholder="Drop file here..."
        accept=".csv"
      ></b-form-file>
      <div class="mt-3">Selected file: {{ file ? file.name : '' }}</div>

      <b-button size="sm"  class="mr-1" @click="uploadCSV">
        Upload <b-spinner label="Spinning" v-if="isUploading"></b-spinner>
      </b-button>


      <br>


      <b-progress class="mt-2" :max="max" show-value>
        <b-progress-bar :value="uploadPercentage" variant="success"></b-progress-bar>
      </b-progress>
     
      <br>
    </div>

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
          <!-- <b-button size="sm" @click="navigate(row.item.long,row.item.lat)" class="mr-1">
            Navigate
          </b-button> -->

          <social-sharing url="https://vuejs.org/"
            :title="'Check out and apply for a job that might be near you. \nTitle :' +  row.item.name + '\nDescription: ' + row.item.description + '\n' + row.item.distance + ' away from my location'"
             :description="'Check out and apply for a job that might be near you. \nTitle :' +  row.item.name + '\nDescription: ' + row.item.description + '\n' + row.item.distance + ' away from my location'"
            :quote="'Check out and apply for a job that might be near you. \nTitle : ' +  row.item.name + '\nDescription: ' + row.item.description + '\n' + row.item.distance + ' away from my location'"
            hashtags="vuejs,jobs"
            
            inline-template>


         <div>
            <network network="facebook">
              <b-button size="sm"  class="mr-1">
                facebook
              </b-button>
            </network>

            

            <network network="twitter">
              <b-button size="sm"  class="mr-1">
                twitter
              </b-button>
            </network>

            <network network="skype">
              <b-button size="sm"  class="mr-1">
                skype
              </b-button>
            </network>


          </div>

          </social-sharing>


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
    </div>


  </b-container>
  </template>

  <script>

  import axios from 'axios'
  import SocialSharing from 'vue-social-sharing'
  // import MMenu from '../components/MMenu'

  export default {
    name: "RadiusSearchMap",
    components: {
      SocialSharing,
      // MMenu
    },
    data() {
      return {
        // default to Montreal to keep it simple
        // change this to whatever makes sense
        // center: {lat:8.4825633, long:124.6614407},
        latLng: {lat:8.4825633, long:124.6614407},
        center: {lat:8.4825633, long:124.6614407},

        markers: [],
        places: [],
        currentPlace: null,

        // Pagination variables
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
        fields: [
          { key: 'id',  sortable: true },
          { 'label' : 'Title', key: 'description', sortable: true },
          { key: 'address',  sortable: true },                 
          { key: 'date_posted', sortable: true },              
          { key: 'distance', sortable: true },
          { key: 'actions', label: 'Share on' }
        ],

        // file,
        file : null,
        uploadPercentage: 0,
        max: 100,
        isUploading : false,
        is_admin : localStorage.isAdmin == 'true' ? true : false, 
      };
    },
    computed: {
      rows() {
        return this.items.length
      }
    },

    mounted() {
      // this.geolocate();
      this.loadList();
      
    },

    methods: {
      loadList() {
        let url = "/api/listsjobs";
        axios({ method: "POST", "url": url, "data": this.center, "headers": { "content-type": "application/json" } }).then(
            result => {
              this.items = result.data.items
            },
            error => {
                console.error(error);
            }
        );
      },

      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length
        this.currentPage = 1
      },
      // receives a place object via the autocomplete component
      geolocate: function() {
        navigator.geolocation.getCurrentPosition(position => {
          this.center = {
            lat: parseFloat(position.coords.latitude),
            lng: parseFloat(position.coords.longitude)
          };
        });
      },
      navigate(lo, la) {
        this.latLng = 
            {lat:parseFloat(la), long:parseFloat(lo)}
            const url = 'https://www.google.com/maps/@'+la+','+lo+',15z'
            window.open(
              url,
              '_blank' // <- This is what makes it open in a new window.
            );


        this.center = 
            {lat:parseFloat(la), long:parseFloat(lo)}
            
      },
      uploadCSV() {
          
        this.isUploading = true;

        this.error = "";
        this.success = "";
        this.uploadPercentage = 0;
        if(!this.file ) {
          this.error = "Please upload a file";

          this.isUploading = false;
          return;
        }


        if(this.file.name.indexOf(".csv") == -1) {

          this.isUploading = false;
          this.error = "Only accepts CSV file";
          return;
        }


        let formData = new FormData();
        formData.append('file', this.file);

        let url = "/api/upload";
        axios.post(

        url,
        
        formData,
        
        {
          headers: {
              'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: function( progressEvent ) {
            this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
          }.bind(this) 

        },

      ).then(
            result => {
              // this.items = result.data.items
              this.success = "File successfully uploaded! "+result.data.count+ " data was successfully processed!";
              this.isUploading = false;

              this.loadList();
              this.$refs.table.refresh()
            },
            error => {
                this.error =  "Something is wrong with the File, please check file format.";
                this.isUploading = false;
            }
        );

      }
    }
  };
  </script>