<template>
  <v-row class="d-none d-print-block" v-if="app_setting">
    <v-col xl="3" lg="3" md="3" sm="12" xs="12">
      <v-img
        :src="app_setting.app_logo"
        max-width="120"
        max-height="60"
        class="mx-auto"
      ></v-img>
    </v-col>
    <v-col xl="9" lg="9" md="9" sm="12" xs="12" class="text-center">
      <h1 class="text-heading-4">{{ app_setting.app_name }}</h1>
      <p>Address: {{ app_setting.address }}</p>
      <p>
        Phone: {{ app_setting.phone }} Fax: {{ app_setting.fax }} Email:
        {{ app_setting.email }}
      </p>
    </v-col>
  </v-row>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  methods: {
    ...mapActions({ getAppSetting: "setting/getAppSetting" }),
  },

  computed: {
    ...mapGetters({ app_setting: "setting/app_setting" }),
  },

  mounted() {
    this.getAppSetting();
  },
};
</script>