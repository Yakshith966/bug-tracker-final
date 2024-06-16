<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" md="8" lg="6">
          <v-card>
            <v-card-title class="headline">Profile</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="updateProfile">
                <v-text-field
                  v-model="name"
                  label="Name"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="email"
                  label="Email"
                  required
                  type="email"
                  readonly
                ></v-text-field>
                <v-btn color="primary" type="submit">Update Profile</v-btn>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-btn color="secondary" @click="showChangePassword = !showChangePassword">Change Password</v-btn>
            </v-card-actions>
          </v-card>
          <v-card v-if="showChangePassword" class="mt-4">
            <v-card-title class="headline">Change Password</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="changePassword">
                <v-text-field
                  v-model="currentPassword"
                  label="Current Password"
                  required
                  type="password"
                ></v-text-field>
                <v-text-field
                  v-model="newPassword"
                  label="New Password"
                  required
                  type="password"
                ></v-text-field>
                <v-text-field
                  v-model="confirmNewPassword"
                  label="Confirm New Password"
                  required
                  type="password"
                ></v-text-field>
                <v-btn color="primary" type="submit">Change Password</v-btn>
                <v-alert v-if="errorMessage" type="error" dense>{{ errorMessage }}</v-alert>
                <v-alert v-if="successMessage" type="success" dense>{{ successMessage }}</v-alert>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'ProfileView',
    data() {
      return {
        name: '',
        email: '',
        currentPassword: '',
        newPassword: '',
        confirmNewPassword: '',
        showChangePassword: false,
        errorMessage: '',
        successMessage: '',
      };
    },
    created() {
      this.fetchProfile();
    },
    methods: {
      fetchProfile() {
        axios.get('http://127.0.0.1:8000/api/profile', {
          headers: {
            'Authorization': `Bearer ${sessionStorage.getItem('token')}`
          }
        })
        .then(response => {
          this.name = response.data.name;
          this.email = response.data.email;
        })
        .catch(error => {
          console.error('Error fetching profile:', error);
        });
      },
      updateProfile() {
        axios.put('http://127.0.0.1:8000/api/profile', {
          name: this.name
        }, {
          headers: {
            'Authorization': `Bearer ${sessionStorage.getItem('token')}`
          }
        })
        .then(response => {
          this.successMessage = 'Profile updated successfully';
          this.errorMessage = '';
        })
        .catch(error => {
          this.errorMessage = 'Error updating profile';
          this.successMessage = '';
        });
      },
      changePassword() {
        if (this.newPassword !== this.confirmNewPassword) {
          this.errorMessage = 'Passwords do not match';
          return;
        }
        axios.post('http://127.0.0.1:8000/api/change-password', {
          current_password: this.currentPassword,
          new_password: this.newPassword
        }, {
          headers: {
            'Authorization': `Bearer ${sessionStorage.getItem('token')}`
          }
        })
        .then(response => {
          this.successMessage = 'Password changed successfully';
          this.errorMessage = '';
          this.currentPassword = '';
          this.newPassword = '';
          this.confirmNewPassword = '';
        })
        .catch(error => {
          this.errorMessage = 'Error changing password';
          this.successMessage = '';
        });
      }
    }
  };
  </script>
  
  <style scoped>
  .mt-4 {
    margin-top: 16px;
  }
  </style>
  