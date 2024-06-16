import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/login/Login.vue';
import Signup from './components/Signup/Signup.vue';
import Dashboard from './components/dashboard/Dashboard.vue';
import MainLayout from './layouts/MainLayout.vue';
import Profile from './components/profile/Profile.vue';
import ProjectManagement from './views/ProjectManagement.vue';
import BugManagement from './views/BugManagement.vue';
import BugDetails from './views/BugDetails.vue';
import TaskManagement from './components/Task/TaskManagement.vue';
import TaskDetails from './views/TaskDetails.vue';
import AdminApproval from './views/AdminApproval.vue';
import AdminUserManagement from './views/AdminUserManagement.vue';

const commonRoutes = [
  { path: '/login', component: Login },
  { path: '/signup', component: Signup },
];

const testerRoutes = [
  { path: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: 'profile', component: Profile, meta: { requiresAuth: true } },
  { path: 'project-management', component: ProjectManagement, meta: { requiresAuth: true } },
  { path: 'bug-management', component: BugManagement, meta: { requiresAuth: true } },
  { path: 'task-details', component: TaskDetails, meta: { requiresAuth: true } },
];

const developerRoutes = [
  { path: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: 'bug-details', component: BugDetails, meta: { requiresAuth: true } },
  { path: 'profile', component: Profile, meta: { requiresAuth: true } },
  { path: 'task-management', component: TaskManagement, meta: { requiresAuth: true } },
];

const adminRoutes = [
  { path: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: 'admin-approval', component: AdminApproval, meta: { requiresAuth: true } },
  { path: 'admin-user-management', component: AdminUserManagement, meta: { requiresAuth: true } },
  { path: 'project-management', component: ProjectManagement, meta: { requiresAuth: true } },
  { path: 'bug-management', component: BugManagement, meta: { requiresAuth: true } },
  { path: 'task-management', component: TaskManagement, meta: { requiresAuth: true } },
  { path: 'profile', component: Profile, meta: { requiresAuth: true } },
];

const routes = [
  ...commonRoutes,
  {
    path: '/',
    component: MainLayout,
    redirect: '/login',
    meta: { requiresAuth: true },
    children: [], // Initially empty, will be populated based on role
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Function to add routes based on role
const addRoleBasedRoutes = (userRole) => {
  let roleRoutes = [];

  if (userRole === 'tester') {
    roleRoutes = testerRoutes;
  } else if (userRole === 'developer') {
    roleRoutes = developerRoutes;
  } else if (userRole === 'admin') {
    roleRoutes = adminRoutes;
  }

  router.addRoute({
    path: '/',
    component: MainLayout,
    redirect: '/dashboard',
    meta: { requiresAuth: true },
    children: roleRoutes,
  });

  router.hasAddedRoutes = true;
};

// Check authentication and role on initial load
const initialToken = sessionStorage.getItem('token');
const initialRole = sessionStorage.getItem('role');

if (initialToken && initialRole) {
  addRoleBasedRoutes(initialRole);
}

// Navigation guard for routes that require authentication
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!sessionStorage.getItem('token'); // Check sessionStorage for the token
  const userRole = sessionStorage.getItem('role'); // Get the user role from sessionStorage

  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next('/login');
  } else {
    if (isAuthenticated && !router.hasAddedRoutes) {
      addRoleBasedRoutes(userRole);
      next({ ...to, replace: true }); // Ensure the navigation happens to the correct route
    } else {
      next();
    }
  }
});

export default router;
