<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card dark>
          <v-card-title>Bug Overview</v-card-title>
          <v-card-subtitle>Summary of open and closed bugs</v-card-subtitle>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-card outlined dark>
                  <v-card-title>
                    <v-icon left>mdi-bug</v-icon>
                    Open Bugs
                  </v-card-title>
                  <v-card-subtitle>Total: {{ openBugs.length }}</v-card-subtitle>
                  <v-card-text>
                    <v-list dense>
                      <v-list-item v-for="(bug, index) in openBugs" :key="index">
                        <v-list-item-content>
                          <v-list-item-title>{{ bug.name }}</v-list-item-title>
                          <v-list-item-subtitle>{{ bug.description }}</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-action>
                          <v-icon color="red">mdi-alert</v-icon>
                        </v-list-item-action>
                      </v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col cols="12" md="6">
                <v-card outlined dark>
                  <v-card-title>
                    <v-icon left>mdi-check-circle</v-icon>
                    Closed Bugs
                  </v-card-title>
                  <v-card-subtitle>Total: {{ closedBugs.length }}</v-card-subtitle>
                  <v-card-text>
                    <v-list dense>
                      <v-list-item v-for="(bug, index) in closedBugs" :key="index">
                        <v-list-item-content>
                          <v-list-item-title>{{ bug.name }}</v-list-item-title>
                          <v-list-item-subtitle>{{ bug.description }}</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-action>
                          <v-icon color="green">mdi-check</v-icon>
                        </v-list-item-action>
                      </v-list-item>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-card outlined dark>
                  <v-card-title>
                    <v-icon left>mdi-chart-bar</v-icon>
                    Bugs Chart
                  </v-card-title>
                  <v-card-text>
                    <!-- <PieChart :openBugsCount="openBugs.length" :closedBugsCount="closedBugs.length" /> -->
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import axios from 'axios';
import PieChart from '../Chart/PieChart.vue';

export default {
  name: 'Dashboard',
  data() {
    return {
      openBugs: [],
      closedBugs: [],
    };
  },
  components:{
    PieChart
  },
  methods: {
    async fetchBugs() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/bugs', {
          headers: {
            Authorization: `Bearer ${sessionStorage.getItem('token')}`,
          },
        });
        this.openBugs = response.data.filter(bug => bug.status === 'open');
        this.closedBugs = response.data.filter(bug => bug.status === 'closed');
      } catch (error) {
        console.error('Error fetching bugs:', error);
      }
    },
  },
  created() {
    this.fetchBugs();
  },
};
</script>
<style>
.headline {
  font-weight: bold;
}
</style>
