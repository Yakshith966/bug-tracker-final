<template>
  <v-navigation-drawer v-model="drawer" app>
    <div class="company-name">Intellogics Software</div>
    <v-list>
      <v-list-item-group v-model="selectedItem">
        <v-list-item v-for="(item, index) in items" :key="index" @click="navigate(item.route)">
          <v-list-item-content>
            <v-list-item-icon>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </v-navigation-drawer>
  <v-app-bar app>
    <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
    <v-toolbar-title>Dashboard</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-btn icon @click="toggleNotifications">
      <v-badge
        :content="unreadNotificationsCount"
        color="red"
        overlap
      >
        <v-icon>mdi-bell</v-icon>
      </v-badge>
    </v-btn>
    <v-btn icon @click="logoutDialog = true">
      <v-icon>mdi-logout</v-icon>
    </v-btn>
  </v-app-bar>

  <!-- Logout Dialog -->
  <v-dialog v-model="logoutDialog" max-width="290">
    <v-card>
      <v-card-title class="headline">Confirm Logout</v-card-title>
      <v-card-text>Are you sure you want to logout?</v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="green darken-1" text @click="logoutDialog = false">Cancel</v-btn>
        <v-btn color="red darken-1" text @click="logout">Logout</v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Notification Dialog -->
  <v-dialog v-model="notificationDialog" max-width="500">
  <v-card>
    <v-card-title class="headline">Notifications</v-card-title>
    <v-card-text>
      <v-list dense>
        <v-list-item
          v-for="notification in notifications"
          :key="notification.id"
          @click="markAsRead(notification.id)"
        >
          <v-list-item-content>
            <v-list-item-action>
            <v-icon :color="notification.read_at ? 'grey' : 'blue'">mdi-bell-ring</v-icon>
          </v-list-item-action>
          <v-list-item-title :style="{ color: 'black' }">
            {{ notification.title }} has been changed the status to {{ notification.status }} on {{ notification.date }}
          </v-list-item-title>
          </v-list-item-content>
          
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="notificationDialog = false">Close</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Sidebar',
  data() {
    return {
      drawer: true,
      selectedItem: 0,
      logoutDialog: false,
      notificationDialog: false,
      items: [], // Initialize empty items array
      notifications: [],
    };
  },
  mounted() {
    this.fetchUserRole();
    this.fetchNotifications();
  },
  computed: {
    unreadNotificationsCount() {
      return this.notifications.filter(notification => !notification.read_at).length;
    },
  },
  methods: {
    navigate(route) {
      this.$router.push(route);
    },
    logout() {
      axios.post('http://127.0.0.1:8000/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${sessionStorage.getItem('token')}`
        }
      }).then(response => {
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('userId');
        sessionStorage.removeItem('role');
        this.$router.push('/login');
      }).catch(error => {
        console.error('Error logging out:', error);
      });
    },
    toggleNotifications() {
      this.notificationDialog = true;
      this.fetchNotifications();
    },
    fetchNotifications() {
      axios.get('http://127.0.0.1:8000/api/notifications')
        .then(response => {
          this.notifications = response.data;
        });
    },
    markAsRead(notificationId) {
      axios.post(`http://127.0.0.1:8000/api/notifications/${notificationId}/mark-as-read`)
        .then(() => {
          const notificationIndex = this.notifications.findIndex(notification => notification.id === notificationId);
          this.fetchNotifications();
          if (notificationIndex !== -1) {
            this.$set(this.notifications[notificationIndex], 'read_at', new Date().toISOString());
          }
        });
    },
    fetchUserRole() {
      // Assuming the user's role is stored in sessionStorage
      const userRole = sessionStorage.getItem('role');
      this.setSidebarItems(userRole);
    },
    setSidebarItems(role) {
      if (role === 'tester') {
        this.items = [
          { title: 'Dashboard', icon: 'mdi-view-dashboard', route: '/dashboard' },
          { title: 'Projects', icon: 'mdi-cog', route: '/project-management' },
          { title: 'Bug Management', icon: 'mdi-bug', route: '/bug-management' },
          { title: 'Task Details', icon: 'mdi-bug', route: '/task-details' },
          { title: 'Profile', icon: 'mdi-account', route: '/profile' },
        ];
      } else if (role === 'developer') {
        this.items = [
          { title: 'Dashboard', icon: 'mdi-view-dashboard', route: '/dashboard' },
          { title: 'Bug Details', icon: 'mdi-bug', route: '/bug-details' },
          { title: 'Task Management', icon: 'mdi-account', route: '/task-management' },
          { title: 'Profile', icon: 'mdi-account', route: '/profile' },
          
        ];
      } else if(role === 'admin'){
        this.items = [
          { title: 'Dashboard', icon: 'mdi-view-dashboard', route: '/dashboard' },        
          { title: 'User Management', icon: 'mdi-view-dashboard', route: '/admin-approval' },        
          { title: 'User Details', icon: 'mdi-view-dashboard', route: '/admin-user-management' }, 
          { title: 'Projects', icon: 'mdi-cog', route: '/project-management' },
          { title: 'Bug Management', icon: 'mdi-bug', route: '/bug-management' },
          { title: 'Task Management', icon: 'mdi-account', route: '/task-management' },
          { title: 'Profile', icon: 'mdi-account', route: '/profile' },

        ];


      }
    },
  },
};

</script>

<style scoped>
.company-name {
  padding: 16px;
  font-size: 18px;
  font-weight: bold;
  color: #ffffff;
  background-color: #121212;
}
.v-navigation-drawer {
  background-color: #121212;
}
.v-list-item {
  transition: background-color 0.2s ease;
}
.v-list-item:hover {
  background-color: #b43535;
}
.v-list-item .v-icon {
  color: #ffffff;
}
.v-list-item .v-list-item-title {
  color: #ffffff;
}
</style>
