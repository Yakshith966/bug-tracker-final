<!-- src/components/AdminUserApproval.vue -->

<template>
     <div>
       <v-card>
         <v-card-title>Unapproved Users</v-card-title>
         <v-card-text>
           <v-data-table
             :headers="headers"
             :items="users"
             item-key="id"
             class="elevation-1"
           >
             <template v-slot:item.actions="{ item }">
               <v-btn color="primary" @click="approveUser(item.id)">Approve</v-btn>
             </template>
           </v-data-table>
         </v-card-text>
       </v-card>
     </div>
   </template>
   
   <script>
   import axios from 'axios';
   
   export default {
     data() {
       return {
         users: [],
         headers: [
           { text: 'Name', value: 'name' },
           { text: 'Email', value: 'email' },
           { text: 'Role', value: 'role.name' },
           { text: 'Actions', value: 'actions', sortable: false },
         ],
       };
     },
     methods: {
       fetchUsers() {
         axios.get('http://127.0.0.1:8000/api/admin/users', {
           headers: {
             Authorization: `Bearer ${sessionStorage.getItem('token')}`,
           },
         }).then(response => {
           this.users = response.data;
         }).catch(error => {
           console.error('Error fetching users:', error);
         });
       },
       approveUser(userId) {
         axios.post(`http://127.0.0.1:8000/api/admin/users/${userId}/approve`, {}, {
           headers: {
             Authorization: `Bearer ${sessionStorage.getItem('token')}`,
           },
         }).then(response => {
           this.fetchUsers();
           this.$toast.success('User approved successfully');
         }).catch(error => {
           console.error('Error approving user:', error);
           this.$toast.error('Failed to approve user');
         });
       },
     },
     mounted() {
       this.fetchUsers();
     },
   };
   </script>
   