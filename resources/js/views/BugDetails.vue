<template>
  <div class="main">
    <template>
  <v-snackbar v-model="snackbar.visible" :color="snackbar.color" top>
    {{ snackbar.text }}
    <v-btn dark text @click="snackbar.visible = false">Close</v-btn>
  </v-snackbar>
</template>
     <div v-if="loading" class="text-center">
        <v-progress-circular :model-value="value" :rotate="360" :size="100" :width="15" color="primary">
          <template v-slot:default> {{ value }} % </template>
        </v-progress-circular>
      </div>
    <v-container v-else>
      
  
  
      <v-dialog v-model="bugDetailsDialog" max-width="800px">
        <v-card>
          <v-card-title>{{ bugDetails.title }}</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12">
                <p><strong>Description:</strong> {{ bugDetails.description }}</p>
                <p><strong>Status:</strong> {{ bugDetails.status }}</p>
                <p><strong>Assigned Developer:</strong> {{ bugDetails.assignedDeveloper }}</p>
                <p><strong>Project:</strong> {{ bugDetails.project }}</p>
              </v-col>
              <v-col cols="12">
                <v-row>
                  <v-col v-for="(image, index) in bugDetails.images" :key="index" cols="12" sm="6" md="4">
                    <v-img :src="image" contain></v-img>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="bugDetailsDialog = false">Close</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
  
  
      
  
      <!-- Edit Bug Modal -->
      
  
      <v-dialog v-model="imageDialog" max-width="800px">
    <v-card>
      <v-card-title>View Images</v-card-title>
      <v-card-text>
        <v-row>
          <v-col v-for="(image, index) in images" :key="index" cols="12" sm="6" md="4">
            <v-img :src="image" contain @click="openImage(index)"></v-img>
            <v-btn icon @click="downloadImage(image)">
              <v-icon color="blue" >mdi-eye</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="imageDialog = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  
      <!-- Bugs Table -->
      <v-row>
    <v-col>
      <!-- <v-btn color="primary" @click="createBugDialog = true">Create Bug</v-btn> -->
      <v-data-table :headers="headers" :items="bugs" item-key="id" class="elevation-1"  :style="{ color: 'black' }">
        <template v-slot:item.slNo="{ index }">
          {{ index + 1 + (pagination.page - 1) * pagination.itemsPerPage }}
        </template>
        <template v-slot:item.developers="{ item }">
          <div>
            <v-chip v-for="user in item.developers" :key="user.id" class="ma-2"
        color="indigo"
        prepend-icon="mdi-account-circle">
              {{ user.name }}
            </v-chip>
          </div>
  
        </template>
        <template v-slot:item.testers="{ item }">
          <div>
            <v-chip class="ma-2"
        color="indigo"
        prepend-icon="mdi-account-circle">
              {{ item.tester.name }}
            </v-chip>
          </div>
  
        </template>
        
        
       
        <template v-slot:item.actions="{ item }">
          <!-- <v-btn icon @click="openAssignDevelopersDialog(item)">
            <v-icon>mdi-account-plus</v-icon>
          </v-btn> -->
          <!-- <v-btn icon @click="openEditBugDialog(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn> -->
          <v-btn icon @click="viewBugDetails(item.id)">
                <v-icon>mdi-eye</v-icon>
              </v-btn>
          <!-- <v-btn icon @click="deleteBug(item.id)">
            <v-icon color="red">mdi-delete</v-icon>
          </v-btn> -->
          <!-- View Images Button -->
          <v-btn icon @click="viewImages(item)">
            <v-icon>mdi-image</v-icon>
          </v-btn>
        </template>
        <!-- Status Column with dynamic color -->
        <template v-slot:item.status="{ item }">
          <div class="custom-select-wrapper">
        <select v-model="item.status" @change="updateBugStatus(item, $event)" :disabled="item.status === 'closed'" class="custom-select">
          <option value="open">Open</option>
          <option value="working">Working</option>
          <option value="closed">Closed</option>
        </select>
      </div>

            </template>




         
        <template v-slot:item.priority="{ item }">
          <!-- <div :style="{ color: getStatusColor(item.status) }">{{ item.status }}</div> -->
          <v-chip :color="getPriorityColor(item.priority)" >
              {{ item.priority }}
            </v-chip>
        </template>
        <!-- Assigned Date Column -->
        <template v-slot:item.assigned_date="{ item }">
          {{ formatDate(item.created_at) }}
        </template>
      </v-data-table>
    </v-col>
  </v-row>
  
    </v-container>
  </div>
  </template>
  
  <script>
  import axios from "axios";
  import moment from "moment";
  
  
  export default {
    data() {
      return {
        snackbar: {
      visible: false,
      text: '',
      color: ''
    },
        loading: false,
        developerId:null,
        value: 0,
        imageDialog: false,
        images: [],
        bugDetailsDialog:false,
        selectedImageIndex: null,
        createBugDialog: false,
        assignDevelopersDialog: false,
        editBugDialog: false,
        bugForm: {
          name: "",
          description: "",
          project_id: null,
          priority: "low", // Default value
          bug_images: [],
        },
        editBugForm: {
          id: "",
          name: "",
          description: "",
          priority: "low", // Default value
          bug_images: [],
        },
        bugDetails: {
          title: '',
          description: '',
          status: '',
          assignedDeveloper: '',
          project: '',
          images: [],
        },
        selectedDevelopers: [],
        bugs: [],
        projects: [],
        developers: [],
        currentBugId: null,
        testerId: null,
        priorityOptions: ["low", "medium", "high"],
        headers: [
          { text: "SL No.", value: "slNo", sortable: false },
          { text: "Name", value: "name", sortable: true },
          { text: "Description", value: "description" },
          { text: "Project", value: "project.name" },
          { text: "Priority", value: "priority", sortable: true },
          { text: "Status", value: "status", sortable: true },
          { text: "Assigned Developers", value: "developers", sortable: false },
          { text: "Assigned By", value: "testers", sortable: false },
          { text: "Assigned Date", value: "assigned_date", sortable: true },
          { text: "Actions", value: "actions", sortable: false },
        ],
        pagination: {
          page: 1,
          itemsPerPage: 10,
        },
      };
    },
    watch: {
    // 'item.status': function(newVal, oldVal) {
    //   this.updateBugStatus(newVal);
    // }
  },

    mounted(){
      this.fetchBugs();
      this.fetchDevelopers();
      this.fetchProjects();
     
    },
    created() {
     
 
      this.developerId=sessionStorage.getItem('userId');
      console.log(this.developerId);
    },
    methods: {
     updateBugStatus(bug, newStatus) {
          const selectedStatus = bug.status;
          console.log(selectedStatus);
      axios
        .post(`http://127.0.0.1:8000/api/update-status/${bug.id}`, { status: selectedStatus }, {
          headers: {
            Authorization: `Bearer ${sessionStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          bug.status = newStatus;
          this.fetchBugs();
          this.showSnackbar('Bug status updated successfully', 'success');
        })
        .catch((error) => {
          console.error('Error updating bug status:', error);
          this.showSnackbar('Failed to update bug status', 'error');
        });
    },
    showSnackbar(message, color) {
    this.snackbar.text = message;
    this.snackbar.color = color;
    this.snackbar.visible = true;
  },
 
      viewImages(item) {
        this.images = item.bug_images.map(image => `http://127.0.0.1:8000/storage/${image}`);
        this.imageDialog = true;
      },
      viewBugDetails(bugId) {
        axios.get(`http://127.0.0.1:8000/api/bugs/${bugId}`, {
          headers: {
            Authorization: `Bearer ${sessionStorage.getItem("token")}`,
          },
        })
        .then(response => {
          const bug = response.data;
          this.bugDetails = {
            title: bug.name,
            description: bug.description,
            status: bug.status,
            assignedDeveloper: bug.developers.map(dev => dev.name).join(', '),
            project: bug.project.name,
            images: bug.bug_images.map(image => `http://127.0.0.1:8000/storage/${image}`),
          };
          this.bugDetailsDialog = true;
        })
        .catch(error => {
          console.error("Error fetching bug details:", error);
        });
      },
      openImage(index) {
        this.selectedImageIndex = index;
      },
      cancelBugForm() {
      this.createBugDialog = false;
      this.$refs.bugFormRef.reset(); // Reset form data
    },
      formatDate(date) {
        return moment(date).format("DD-MM-YYYY");
      },
      downloadImage(imageUrl) {
        const link = document.createElement('a');
        link.href = imageUrl;
        link.download = imageUrl.split('/').pop(); // Use the image file name for downloading
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      },
      fetchBugs() {
        this.loading = true;
        this.value = 0;
  
        // Simulate progress
        let progressInterval = setInterval(() => {
          if (this.value < 90) {
            this.value += 10;
          } else {
            clearInterval(progressInterval);
          }
        }, 100);
  
        axios
          .get(`http://127.0.0.1:8000/api/bugs/developer/${this.developerId}`, {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then((response) => {
            // this.bugs = response.data;
            this.bugs = response.data;
            this.value = 100;
          
            setTimeout(() => {
              this.loading = false;
            }, 500); // Small delay to show 100% completion
          })
          .catch((error) => {
            console.error("Error fetching bugs:", error);
          });
      },
      fetchProjects() {
        axios
          .get("http://127.0.0.1:8000/api/projects", {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then((response) => {
            this.projects = response.data;
            this.projects.forEach((item) => {
              console.log(item.name);
            });
          })
          .catch((error) => {
            console.error("Error fetching projects:", error);
          });
      },
      fetchDevelopers() {
        axios
          .get("http://127.0.0.1:8000/api/users?role=developer", {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then((response) => {
            this.developers = response.data;
          })
          .catch((error) => {
            console.error("Error fetching developers:", error);
          });
      },
      submitBug() {
        let formData = new FormData();
        this.testerId = sessionStorage.getItem("userId");
        formData.append("name", this.bugForm.name);
        formData.append("description", this.bugForm.description);
        formData.append("tester_id", this.testerId); // Use fetched testerId
        formData.append("project_id", this.bugForm.project_id);
        formData.append("priority", this.bugForm.priority);
        formData.append("status", 'open');
        for (let i = 0; i < this.bugForm.bug_images.length; i++) {
          formData.append("bug_images[]", this.bugForm.bug_images[i]);
        }
        axios
          .post("http://127.0.0.1:8000/api/bugs", formData, {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            this.bugs.push(response.data);
            this.createBugDialog = false;
            this.fetchBugs();
            this.bugForm = {
              name: "",
              description: "",
              project_id: null,
              priority: "low",
              bug_images: [],
            };
          })
          .catch((error) => {
            console.error("Error creating bug:", error);
          });
      },
      deleteBug(id) {
        axios
          .delete(`http://127.0.0.1:8000/api/bugs/${id}`, {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then(() => {
            this.bugs = this.bugs.filter((bug) => bug.id !== id);
          })
          .catch((error) => {
            console.error("Error deleting bug:", error);
          });
      },
      openAssignDevelopersDialog(bug) {
        this.currentBugId = bug.id;
        this.selectedDevelopers = bug.developers.map((user) => user.id);
        this.assignDevelopersDialog = true;
      },
      openEditBugDialog(bug) {
        this.editBugForm = { ...bug };
        this.editBugDialog = true;
      },
      async updateBug() {
        let formData = new FormData();
        console.log(this.editBugForm.name);
        formData.append("name", this.editBugForm.name);
        formData.append("description", this.editBugForm.description);
        formData.append("priority", this.editBugForm.priority);
        for (let i = 0; i < this.editBugForm.bug_images.length; i++) {
          formData.append("bug_images[]", this.editBugForm.bug_images[i]);
        }
        console.log("formData", Object.fromEntries(formData));
        await axios
          .post(
            `http://127.0.0.1:8000/api/update-bug/${this.editBugForm.id}`,
            formData,
            {
              headers: {
                Authorization: `Bearer ${sessionStorage.getItem("token")}`,
                "Content-Type": "multipart/form-data",
              },
            }
          )
          .then(() => {
            this.fetchBugs();
            this.editBugDialog = false;
            this.editBugForm = {
              name: "",
              description: "",
              priority: "low",
              bug_images: [],
            };
          })
          .catch((error) => {
            console.error("Error updating bug:", error);
          });
      },
      assignDevelopers() {
        axios
          .post(
            `http://127.0.0.1:8000/api/bugs/${this.currentBugId}/assign`,
            {
              user_ids: this.selectedDevelopers,
            },
            {
              headers: {
                Authorization: `Bearer ${sessionStorage.getItem("token")}`,
              },
            }
          )
          .then(() => {
            this.fetchBugs();
            this.assignDevelopersDialog = false;
          })
          .catch((error) => {
            console.error("Error assigning developers:", error);
          });
      },
      getStatusColor(status) {
        switch (status) {
          case "open":
            return "blue";
          case "closed":
            return "green";
          case "working":
            return "orange";
          default:
            return "black";
        }
      },
      getPriorityColor(status){
        switch (status) {
          case "low":
            return "green";
          case "high":
            return "red";
          case "medium":
            return "orange";
          default:
            return "black";
        }
  
      }
    },
  };
  </script>
  
  <style scoped>
  .blue { color: blue; }
  .green { color: green; }
  .orange { color: orange; }
  .custom-select-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

.custom-select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-color: white;
  background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="4" height="5" viewBox="0 0 4 5"><path fill="%23000000" d="M2 0l2 2H0zM0 3h4l-2 2z"/></svg>');
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 8px 10px;
  font-size: 16px;
  font-family: inherit;
  line-height: 1.5;
  color: #495057;
  background-clip: padding-box;
  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.custom-select:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.custom-select:hover {
  border-color: #888;
}

.custom-select:focus {
  border-color: #40a9ff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(24, 144, 255, 0.2);
} 
  .v-data-table {
    margin-top: 20px;
  }
  .select-wrapper {
    margin-top: 20px;
    margin-bottom: 20px;
  }
  
  .v-label {
    display: block;
    margin-bottom: 8px;
    color: rgba(0, 0, 0, 0.54);
    font-size: 1rem;
    font-weight: 400;
    line-height: 1;
    letter-spacing: 0.00938em;
  }
  
  .select-container {
    position: relative;
    width: 100%;
  }
  
  .v-input {
    display: block;
    width: calc(100% - 2rem);
    padding: 0.375rem 1.75rem 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }
  
  .v-input:focus {
    color: #495057;
    background-color: #fff;
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }
  
  .dropdown-icon {
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
    pointer-events: none;
    color: rgba(0, 0, 0, 0.54);
  }
  .text-center {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Adjust as needed */
  }
  </style>
  