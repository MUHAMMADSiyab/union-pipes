<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Banks</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-if="!printMode && can('bank_create')"
        >
          <AddBank />
        </v-col>

        <v-col v-bind="getPrintClass(8, can('bank_create'))" cols="12">
          <Banks />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import Navbar from "../navs/Navbar";
import AddBank from "./AddBank.vue";
import Banks from "./Banks.vue";

export default {
  components: {
    Navbar,
    AddBank,
    Banks,
  },
};
</script>