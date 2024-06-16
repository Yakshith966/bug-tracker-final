<template>
     <div v-if="loading" class="text-center">
        <v-progress-circular :model-value="value" :rotate="360" :size="100" :width="15" color="primary">
          <template v-slot:default> {{ value }} % </template>
        </v-progress-circular>
      </div>
    <v-container v-else>
      <!-- Create Bug Modal -->
      <v-dialog v-model="createBugDialog" max-width="500px">
        <v-card>
          <v-card-title>Create Task</v-card-title>
          <v-card-text>
            <v-form ref="bugFormRef" @submit.prevent="submitBug">
              <v-text-field
                v-model="bugForm.name"
                label="Task Name"
                required
              ></v-text-field>
              <v-textarea
                v-model="bugForm.description"
                label="Task Description"
                required
              ></v-textarea>
              <div class="select-wrapper">
                <label for="project" class="v-label">Project</label>
                <div class="select-container">
                  <select
                    v-model="bugForm.project_id"
                    id="project"
                    class="v-input"
                    required
                  >
                    <option value="" disabled selected>Select the project</option>
                    <option
                      v-for="project in projects"
                      :key="project.id"
                      :value="project.id"
                    >
                      {{ project.name }}
                    </option>
                  </select>
                  <i class="dropdown-icon mdi mdi-menu-down"></i>
                </div>
              </div>
              <!-- <v-select
                v-model="bugForm.priority"
                :items="priorityOptions"
                label="Priority"
                required
              ></v-select> -->
              <v-file-input
                v-model="bugForm.task_images"
                label="Task Images"
                multiple
                accept="image/*"
              ></v-file-input>
              <v-btn type="submit" color="primary">Create</v-btn>
              <v-btn color="secondary" @click="cancelBugForm"
                >Cancel</v-btn
              >
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
  
  
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
  
  
      <!-- Assign Developers Modal -->
      <v-dialog v-model="assignDevelopersDialog" max-width="500px">
    <v-card>
      <v-card-title>Assign Developer</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="assignDeveloper()">
          <v-radio-group v-model="selectedDeveloper" row>
            <v-radio
              v-for="developer in developers"
              :key="developer.id"
              :label="developer.name"
              :value="developer.id"
            ></v-radio>
          </v-radio-group>
          <v-btn type="submit" color="primary">Assign</v-btn>
          <v-btn color="secondary" @click="assignDevelopersDialog = false">Cancel</v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
  
      <!-- Edit Bug Modal -->
      <v-dialog v-model="editBugDialog" max-width="500px">
        <v-card>
          <v-card-title>Edit Bug</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateBug">
              <v-text-field
                v-model="editBugForm.name"
                label="Task Name"
                required
              ></v-text-field>
              <v-textarea
                v-model="editBugForm.description"
                label="Task Description"
                required
              ></v-textarea>
              <div class="select-wrapper">
                <label for="project" class="v-label">Project</label>
                <div class="select-container">
                  <select
                    v-model="editBugForm.project_id"
                    id="project"
                    class="v-input"
                    required
                  >
                    <option value="" disabled selected>Select the project</option>
                    <option
                      v-for="project in projects"
                      :key="project.id"
                      :value="project.id"
                    >
                      {{ project.name }}
                    </option>
                  </select>
                  <i class="dropdown-icon mdi mdi-menu-down"></i>
                </div>
              </div>
              <v-text-field
                v-model="editBugForm.status"
                label="Status"
                required
              ></v-text-field>
              <v-file-input
                v-model="editBugForm.task_images"
                label="Task Images"
                multiple
                accept="image/*"
              ></v-file-input>
              <v-btn type="submit" color="primary">Save</v-btn>
              <v-btn color="secondary" @click="editBugDialog = false"
                >Cancel</v-btn
              >
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>

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
      <v-btn color="primary" @click="createBugDialog = true">Create Task</v-btn>
      <v-data-table :headers="headers" :items="bugs" item-key="id" class="elevation-1"  :style="{ color: 'black' }">
        <template v-slot:item.slNo="{ index }">
          {{ index + 1 + (pagination.page - 1) * pagination.itemsPerPage }}
        </template>
       
        
        
       
        <template v-slot:item.actions="{ item }">
          <v-btn icon @click="openAssignDevelopersDialog(item)">
            <v-icon>mdi-account-plus</v-icon>
          </v-btn>
          <v-btn icon @click="openEditBugDialog(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon @click="viewBugDetails(item.id)">
                <v-icon>mdi-eye</v-icon>
              </v-btn>
          <v-btn icon @click="deleteBug(item.id)">
            <v-icon color="red">mdi-delete</v-icon>
          </v-btn>
          <!-- View Images Button -->
          <v-btn icon @click="viewImages(item)">
            <v-icon>mdi-image</v-icon>
          </v-btn>
        </template>
        <!-- Status Column with dynamic color -->
        <template v-slot:item.status="{ item }">
          <!-- <div :style="{ color: getStatusColor(item.status) }">{{ item.status }}</div> -->
          <v-chip :color="getStatusColor(item.status)" >
              {{ item.status }}
            </v-chip>
        </template>
         <template v-slot:item.tester_name="{ item }" >
          <div v-if="item.tester">
    <v-chip class="ma-2" color="indigo" prepend-icon="mdi-account-circle">
      {{ item.tester.name }}
    </v-chip>
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
  </template>
  
  <script>

  import moment from "moment";
import axios from 'axios';
  
  
  export default {
    data() {
      return {
        loading: false,
        value: 0,
        imageDialog: false,
        images: [],
        bugDetailsDialog:false,
        selectedImageIndex: null,
        createBugDialog: false,
        selectedDeveloper:null,
        assignDevelopersDialog: false,
        editBugDialog: false,
        currenTaskId:false,
        bugForm: {
          name: "",
          description: "",
          project_id: null,
          priority: "low", // Default value
          task_images: [],
        },
        editBugForm: {
          id: "",
          name: "",
          description: "",
          project_id: null,
          status: "", // Default value
          task_images: [],
        },
        bugDetails: {
          project_id:null,
          title: '',
          description: '',
          status: '',
          assignedTester: '',
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
          { text: "Status", value: "status", sortable: true },
          { text: "Assigned Developers", value: "tester_name", sortable: false },
          { text: "Assigned Date", value: "assigned_date", sortable: true },
          { text: "Actions", value: "actions", sortable: false },
        ],
        pagination: {
          page: 1,
          itemsPerPage: 10,
        },
      };
    },
    created() {
      this.fetchBugs();
      this.fetchDevelopers();
      this.fetchProjects();
    },
    methods: {
      viewImages(item) {
  try {
    // Check if task_images is not null and is a valid JSON string
    const taskImages = item.task_images ? item.task_images : [];

    // Map the images to their full URL
    this.images = taskImages.map(image => `http://127.0.0.1:8000/storage/${image}`);
    this.imageDialog = true;
  } catch (error) {
    console.error("Error parsing task images:", error);
    this.images = [];
    this.imageDialog = true;
  }
},
      viewBugDetails(bugId) {
        axios.get(`http://127.0.0.1:8000/api/tasks/${bugId}`, {
          headers: {
            Authorization: `Bearer ${sessionStorage.getItem("token")}`,
          },
        })
        .then(response => {
          const bug = response.data;
          const taskImages = bug.task_images ? bug.task_images : [];

this.bugDetails = {
  title: bug.name,
  description: bug.description,
  status: bug.status,
  assignedDeveloper: bug.tester ? bug.tester.name : null,
  project: bug.project.name,
  images: taskImages.map(image => `http://127.0.0.1:8000/storage/${image}`),
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
          .get("http://127.0.0.1:8000/api/tasks", {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then((response) => {
            // this.bugs = response.data;
            this.bugs = response.data;
           this.bugs.forEach((item)=>{
            if(item.tester!=null)
            {
              console.log(item.tester.name);
            }
            
           })
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
          .get("http://127.0.0.1:8000/api/users?role=tester", {
            headers: {
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
            },
          })
          .then((response) => {
            this.developers = response.data;
          })
          .catch((error) => {
            console.error("Error fetching Testers:", error);
          });
      },
      submitBug() {
        let formData = new FormData();
        this.userId = sessionStorage.getItem("userId");
        formData.append("name", this.bugForm.name);
        formData.append("description", this.bugForm.description);
        formData.append("developer_id", this.userId);
        formData.append("project_id", this.bugForm.project_id);
        formData.append("status", 'open');
        for (let i = 0; i < this.bugForm.task_images.length; i++) {
          formData.append("task_images[]", this.bugForm.task_images[i]);
        }
        axios
          .post("http://127.0.0.1:8000/api/tasks", formData, {
            headers: {
              
              Authorization: `Bearer ${sessionStorage.getItem("token")}`,
             
              
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
              task_images: [],
            };
          })
          .catch((error) => {
            console.error("Error creating Task:", error);
          });
      },
      deleteBug(id) {
        axios
          .delete(`http://127.0.0.1:8000/api/tasks/${id}`, {
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
        // const data=JSON.parse(bug);
        console.log(bug);
       
        this.currenTaskId = bug.id;
        if(bug.tester)
        {
          this.selectedDeveloper = bug.tester.id;
        }
          this.assignDevelopersDialog = true;
      },
      openEditBugDialog(bug) {
        this.editBugForm = { ...bug };
        console.log(this.editBugForm.task_images);
        this.editBugDialog = true;
      },
      async updateBug() {
        let formData = new FormData();
        console.log(this.editBugForm.name);
        formData.append("name", this.editBugForm.name);
        formData.append("description", this.editBugForm.description);
        formData.append("status", this.editBugForm.status);
        formData.append("project_id", this.editBugForm.project_id);
        for (let i = 0; i < this.editBugForm.task_images.length; i++) {
          formData.append("task_images[]", this.editBugForm.task_images[i]);
        }
        console.log("formData", Object.fromEntries(formData));
        await axios
          .post(
            `http://127.0.0.1:8000/api/update-task/${this.editBugForm.id}`,
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
              id:null,
              name: "",
              description: "",
              status:"",
              project_id:"",
              task_images: [],
            };
          })
          .catch((error) => {
            // console.error("Error updating bug:", error);
          });
      },
      assignDeveloper() {
        console.log(this.currenTaskId);
        axios
          .post(
            `http://127.0.0.1:8000/api/tasks/${this.currenTaskId}/assign`,
            {
              tester_id: this.selectedDeveloper,
            },
            {
              headers: {
                Authorization: `Bearer ${sessionStorage.getItem("token")}`,
              },
            }
          )
          .then(() => {
            this.fetchBugs();
            this.selectedDeveloper=null,
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
  