<!-- src/components/AdminUserManagement.vue -->

<template>
     <v-container>
       <v-tabs v-model="tab">
         <v-tab>
           Developers
         </v-tab>
         <v-tab>
           Testers
         </v-tab>
       </v-tabs>
   
       <v-tabs-items v-model="tab">
         <v-tab-item>
           <v-card>
             <v-card-title>Developers</v-card-title>
             <v-card-text>
               <v-data-table
                 :headers="headers"
                 :items="developers"
                 item-key="id"
                 class="elevation-1"
               >
               </v-data-table>
             </v-card-text>
           </v-card>
         </v-tab-item>
   
         <v-tab-item>
           <v-card>
             <v-card-title>Testers</v-card-title>
             <v-card-text>
               <v-data-table
                 :headers="headers"
                 :items="testers"
                 item-key="id"
                 class="elevation-1"
               >
               </v-data-table>
             </v-card-text>
           </v-card>
         </v-tab-item>
       </v-tabs-items>
     </v-container>
   </template>
   
   <script>
   import axios from 'axios';
   
   export default {
     data() {
       return {
         tab: 0,
         developers: [],
         testers: [],
         headers: [
           { text: 'Name', value: 'name' },
           { text: 'Email', value: 'email' },
           { text: 'Role', value: 'role.name' }
         ],
       };
     },
     methods: {
       fetchDevelopers() {
         axios.get('http://127.0.0.1:8000/api/admin/users/developers', {
           headers: {
             Authorization: `Bearer ${sessionStorage.getItem('token')}`,
           },
         }).then(response => {
           this.developers = response.data;
         }).catch(error => {
           console.error('Error fetching developers:', error);
         });
       },
       fetchTesters() {
         axios.get('http://127.0.0.1:8000/api/admin/users/testers', {
           headers: {
             Authorization: `Bearer ${sessionStorage.getItem('token')}`,
           },
         }).then(response => {
           this.testers = response.data;
         }).catch(error => {
           console.error('Error fetching testers:', error);
         });
       },
     },
     watch: {
       tab(newTab) {
         if (newTab === 0 && this.developers.length === 0) {
           this.fetchDevelopers();
         } else if (newTab === 1 && this.testers.length === 0) {
           this.fetchTesters();
         }
       }
     },
     mounted() {
       this.fetchDevelopers();
     },
   };
   </script>
   