<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="headline">Login</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="login">
              <v-text-field
                v-model="email"
                label="Email"
                required
                type="email"
              ></v-text-field>
              <v-text-field
                v-model="password"
                label="Password"
                required
                type="password"
              ></v-text-field>
              <v-btn color="primary" type="submit">Login</v-btn>
              <!-- Display error message if any -->
              <v-alert v-if="errorMessage" type="error" dense>{{ errorMessage }}</v-alert>
              <!-- Display success message if login is successful -->
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
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '', // Data property to store error message
      successMessage: '', // Data property to store success message
    };
  },
  methods: {
    login() {
      axios.post('http://127.0.0.1:8000/api/login', {
        email: this.email,
        password: this.password,
      }).then(response => {
        // Clear error message if any
        this.errorMessage = '';
        // Set success message
        this.successMessage = 'Login successful';
        const token = response.data.token;
        const userId=response.data.id;
        const role=response.data.role;
        console.log('role'+role);
  
        // Store the token in session storage
        sessionStorage.setItem('token', token);
        sessionStorage.setItem('userId', userId);
        sessionStorage.setItem('role', role);
        console.log('Token set in sessionStorage:', token);
        console.log('UserId set in sessionStorage:', userId);
        console.log('Role set in sessionStorage:', role);
        // Optionally redirect to a different page
        this.$router.push('/dashboard');
      }).catch(error => {
        // Handle error
        this.errorMessage = 'Invalid email or password';
      });
    },
  },
};
</script>


