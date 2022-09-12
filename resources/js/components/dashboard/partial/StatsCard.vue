<template>
  <div class="d-flex justify-center align-center">
    <v-sheet :color="color" class="pa-2" rounded>
      <v-icon x-large color="white">{{ icon }}</v-icon>
    </v-sheet>
    <div class="right__content pa-1 ml-1">
      <span class="text-caption blue-grey--text lighten-1">{{ title }}</span>
      <h6 class="text-h6 blue-grey--text darken-3">{{ number }}</h6>
    </div>
  </div>
</template>

<script>
export default {
  props: ["color", "icon", "title", "number"],
};
</script>