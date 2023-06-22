<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Roles</h5>

      <v-row>
        <v-col xl="4" lg="4" md="4" sm="12" cols="12" v-if="can('role_create')">
          <AddRole />
        </v-col>

        <v-col v-bind="getPrintClass(8, can('role_create'))" cols="12">
          <Roles />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import Navbar from "../../navs/Navbar";
import AddRole from "./AddRole.vue";
import Roles from "./Roles.vue";

export default {
  components: {
    Navbar,
    AddRole,
    Roles,
  },
};
</script>
    