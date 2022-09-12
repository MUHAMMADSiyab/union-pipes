<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Permissions</h5>

      <v-row>
        <!-- <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-if="can('permission_create')"
        >
          <AddPermission />
        </v-col> -->

        <v-col cols="12">
          <Permissions />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import Navbar from "../../navs/Navbar";
import AddPermission from "./AddPermission.vue";
import Permissions from "./Permissions.vue";

export default {
  components: {
    Navbar,
    AddPermission,
    Permissions,
  },
};
</script>
    