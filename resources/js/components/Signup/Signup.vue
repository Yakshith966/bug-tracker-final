<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card>
          <v-card-title class="headline">Signup</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="signup">
              <v-text-field v-model="name" label="Name" required></v-text-field>
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
              <div class="v-select-container">
                <label class="v-select-label">Role</label>
                <div class="v-select-wrapper">
                  <select v-model="roleId" class="v-select" required @click="fetchRoles">
                    <option disabled value="">Select a role</option>
                    <template v-for="role in roleOptions" :key="role.id">
                      <option :value="role.id">{{ role.name }}</option>
                    </template>
                  </select>
                  <div class="v-select-icon">
                    <i class="mdi mdi-chevron-down"></i>
                    <!-- Replace with your desired icon -->
                  </div>
                </div>
              </div>
              <v-btn color="primary" type="submit">Signup</v-btn>
              
            </v-form>
          </v-card-text>
        </v-card>
        <v-alert v-if="successMessage" type="success" @click="clearErrorMessage" dense>{{ successMessage }}</v-alert>
        <v-alert v-if="errorMessage" type="error" @click="clearErrorMessage" dense>
            {{ errorMessage }}
            <template v-slot:action>
              <v-btn text >Close</v-btn>
            </template>
          </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>
  
  <script>
import axios from "axios";

export default {
  name: "Signup",
  data() {
    return {
      name: "",
      email: "",
      password: "",
      roleId: null,
      errorMessage:'',
      successMessage:'',
      roleOptions: [], // Array to store role options for dropdown
    };
  },
  created() {
    // Fetch roles from the backend when the component is created
    this.fetchRoles();
  },
  methods: {
    fetchRoles() {
      axios
        .get("http://127.0.0.1:8000/api/roles")
        .then((response) => {
          // Update the roleOptions array with the fetched roles
          this.roleOptions = response.data.roles.filter(role=>role.name!='admin');
          console.log(this.roleOptions);
        })
        .catch((error) => {
          console.error("Error fetching roles:", error);
        });
    },
    signup() {
      if (!this.name || !this.email || !this.password || !this.roleId) {
        // Set error message
        this.errorMessage = "All fields are required";
        return; // Prevent form submission
      }
      axios
        .post("http://127.0.0.1:8000/api/register", {
          name: this.name,
          email: this.email,
          password: this.password,
          role_id: this.roleId, // Send the selected role_id to the backend
        })
        .then((response) => {
            this.successMessage = 'Signup successful';
        // Clear form fields
        this.name = '';
        this.email = '';
        this.password = '';
        this.roleOptions = '';
          // Handle success (e.g., save token, redirect)
        })
        .catch((error) => {
          // Handle error
        });
    },
    clearErrorMessage() {
      // Function to clear the error message
      this.errorMessage = '';
      this.successMessage='';
    },
  },
};
</script>

  <style scoped>
/* Styles for the custom select */
/* Styles for the select container */
.v-select-container {
  display: flex;
  flex-direction: column;
  margin-bottom: 30px;
}

/* Styles for the select label */
.v-select-label {
  font-size: 14px;
  margin-bottom: 4px;
  color: #333;
}

/* Styles for the select wrapper */
.v-select-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

/* Styles for the select element */
.v-select {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
  background-color: #fff;
  font-size: 16px;
  color: #333;
  appearance: none;
}

/* Styling for the dropdown icon */
.v-select-icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  pointer-events: none; /* Ensure that the icon doesn't interfere with select element */
}

/* Styles for the dropdown icon */
.v-select-icon i {
  color: #666;
}
</style>
  