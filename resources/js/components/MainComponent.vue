<template>
  <v-app :style="{ background: $vuetify.theme.themes[theme].app_bg }">
    <v-main>
      <PrintHeader />
      <router-view> </router-view>

      <Footer />
    </v-main>
  </v-app>
</template>

<script>
import Footer from "./globals/Footer.vue";
import PrintHeader from "./globals/PrintHeader";

export default {
  components: {
    PrintHeader,
    Footer,
  },

  computed: {
    theme() {
      return this.$vuetify.theme.dark ? "dark" : "light";
    },
  },
};
</script>
