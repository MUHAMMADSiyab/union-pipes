<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Users</h5>

      <v-row>
        <v-col xl="4" lg="4" md="4" sm="12" cols="12" v-if="can('user_create')">
          <AddUser />
        </v-col>

        <v-col v-bind="getPrintClass(8, can('user_create'))" cols="12">
          <Users />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import Navbar from "../../navs/Navbar";
import AddUser from "./AddUser.vue";
import Users from "./Users.vue";

export default {
  components: {
    Navbar,
    AddUser,
    Users,
  },
};
</script>
    