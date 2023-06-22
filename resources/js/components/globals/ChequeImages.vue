<template>
  <!-- Cheque Images -->
  <v-card>
    <v-card-title primary-title>Cheque Images </v-card-title>

    <v-card-text>
      <div class="mt-1 mb-1">
        <v-img
          v-for="(image, i) in currentChequeImages"
          :key="i"
          :aspect-ratio="2"
          class="mb-3"
          :src="image"
        >
        </v-img>
      </div>

      <v-card-actions>
        <v-btn color="secondary" @click="closeDialog" class="mt-3">Close</v-btn>
      </v-card-actions>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["currentChequeImages"],

  methods: {
    closeDialog() {
      this.$emit("closeDialog");
    },
  },
};
</script>