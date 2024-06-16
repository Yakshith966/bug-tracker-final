<template>
    <v-container>
      <!-- Create Project Modal -->
      <v-dialog v-model="createProjectDialog" max-width="500px">
        <v-card>
          <v-card-title>
            <span>Create Project</span>
            <v-spacer></v-spacer>
            <v-btn icon @click="createProjectDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitProject">
              <v-text-field
                v-model="projectForm.name"
                label="Project Name"
                required
              ></v-text-field>
              <v-textarea
                v-model="projectForm.description"
                label="Project Description"
                required
              ></v-textarea>
              <v-btn type="submit" color="primary">Create</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
  
      <!-- Edit Project Modal -->
      <v-dialog v-model="editProjectDialog" max-width="500px">
        <v-card>
          <v-card-title>
            <span>Edit Project</span>
            <v-spacer></v-spacer>
            <!-- <v-btn icon @click="editProjectDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn> -->
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateProject">
              <v-text-field
                v-model="editProjectForm.name"
                label="Project Name"
                required
              ></v-text-field>
              <v-textarea
                v-model="editProjectForm.description"
                label="Project Description"
                required
              ></v-textarea>
              <v-btn type="submit" color="primary">Save</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
  
      <!-- Assign Developers Modal -->
      <v-dialog v-model="assignDevelopersDialog" max-width="500px">
        <v-card>
          <v-card-title>
            <span>Assign Developers</span>
            <v-spacer></v-spacer>
            <!-- <v-btn icon @click="assignDevelopersDialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn> -->
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="assignDevelopers">
              <div v-for="developer in developers" :key="developer.id">
                <v-checkbox
                  v-model="selectedDevelopers"
                  :label="developer.name"
                  :value="developer.id"
                ></v-checkbox>
              </div>
              <v-btn type="submit" color="primary">Assign</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
  
      <!-- Projects Table -->
      <v-row>
      
    </v-row>
      <v-row>
        <v-col>
          <v-btn color="primary" @click="createProjectDialog = true">Create Project</v-btn>
          
          <v-data-table
            :headers="headers"
            :items="projects"
            item-key="id"
            class="elevation-1"
            :items-per-page="itemsPerPage"
            :page.sync="page"
          >
            <template v-slot:item.slNo="{ index }">
              <div>{{ (page - 1) * itemsPerPage + index + 1 }}</div>
            </template>
            <template v-slot:item.users="{ item }">
              <div>
                <v-chip
                  v-for="user in item.users"
                  :key="user.id"
                  color="indigo"
      prepend-icon="mdi-account-circle"
                >
                  {{ user.name }}
                </v-chip>
              </div>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn icon @click="openAssignDevelopersDialog(item)">
                <v-icon>mdi-account-plus</v-icon>
              </v-btn>
              <v-btn icon @click="openEditProjectDialog(item)">
                <v-icon>mdi-eye</v-icon>
              </v-btn>
              <v-btn icon @click="deleteProject(item.id)">
                <v-icon color="red">mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        createProjectDialog: false,
        editProjectDialog: false,
        assignDevelopersDialog: false,
        projectForm: {
          name: '',
          description: ''
        },
        editProjectForm: {
          id: '',
          name: '',
          description: ''
        },
        selectedDevelopers: [],
        projects: [],
        developers: [],
        currentProjectId: null,
        page: 1,
        itemsPerPage: 10,
        headers: [
          { text: 'SL No.', value: 'slNo', sortable: false, style: 'color: black;'},
          { text: 'Name', value: 'name' , sortable: true},
          { text: 'Description', value: 'description' },
          { text: 'Assigned Developers', value: 'users', sortable: false },
          { text: 'Actions', value: 'actions', sortable: false }
        ]
      };
    },
    created() {
      this.fetchProjects();
      this.fetchDevelopers();
    },
    methods: {
      fetchProjects() {
        axios.get('http://127.0.0.1:8000/api/projects', {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(response => {
          this.projects = response.data;
        }).catch(error => {
          console.error('Error fetching projects:', error);
        });
      },
      fetchDevelopers() {
        axios.get('http://127.0.0.1:8000/api/users?role=developer', {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(response => {
          this.developers = response.data;
        }).catch(error => {
          console.error('Error fetching developers:', error);
        });
      },
      submitProject() {
        axios.post('http://127.0.0.1:8000/api/projects', this.projectForm, {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(response => {
          this.projects.push(response.data);
          this.createProjectDialog = false;
          this.projectForm = { name: '', description: '' };
        }).catch(error => {
          console.error('Error creating project:', error);
        });
      },
      deleteProject(id) {
        axios.delete(`http://127.0.0.1:8000/api/projects/${id}`, {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(() => {
          this.projects = this.projects.filter(project => project.id !== id);
        }).catch(error => {
          console.error('Error deleting project:', error);
        });
      },
      openEditProjectDialog(project) {
        this.editProjectForm = { ...project };
        this.editProjectDialog = true;
      },
      updateProject() {
        axios.put(`http://127.0.0.1:8000/api/projects/${this.editProjectForm.id}`, this.editProjectForm, {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(() => {
          this.fetchProjects();
          this.editProjectDialog = false;
        }).catch(error => {
          console.error('Error updating project:', error);
        });
      },
      openAssignDevelopersDialog(project) {
        this.currentProjectId = project.id;
        this.selectedDevelopers = project.users.map(user => user.id);
        this.assignDevelopersDialog = true;
      },
      assignDevelopers() {
        axios.post(`http://127.0.0.1:8000/api/projects/${this.currentProjectId}/assign`, {
          user_ids: this.selectedDevelopers
        }, {
          headers: { 'Authorization': `Bearer ${sessionStorage.getItem('token')}` }
        }).then(() => {
          this.fetchProjects();
          this.assignDevelopersDialog = false;
        }).catch(error => {
          console.error('Error assigning developers:', error);
        });
      }
    }
  };
  </script>
  
  <style scoped>
  .v-data-table {
    margin-top: 20px;
  }
  </style>
  