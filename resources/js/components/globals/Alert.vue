<template>
  <div>
    <v-snackbar
      dark
      :color="alertData.type"
      v-model="show"
      bottom
      right
      v-if="alertData"
    >
      {{ alertData.message }}
      <v-btn color="white" text @click="show = false" plain small
        ><v-icon right>mdi-close-circle</v-icon></v-btn
      >
    </v-snackbar>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      show: true,
    };
  },

  computed: {
    ...mapGetters({ alertData: "alert/data" }),
  },
};
</script>