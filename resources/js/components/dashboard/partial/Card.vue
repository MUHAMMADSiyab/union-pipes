<template>
  <div class="d-flex align-center">
    <v-sheet :color="color" class="pa-2" rounded>
      <v-icon x-large color="white">{{ icon }}</v-icon>
    </v-sheet>
    <div class="right__content pa-1 ml-1">
      <span class="text-caption blue-grey--text lighten-1">{{ title }}</span>
      <h4 class="text-h4 blue-grey--text darken-3">{{ number }}</h4>
    </div>
  </div>
</template>

<script>
export default {
  props: ["color", "icon", "title", "number"],
};
</script>