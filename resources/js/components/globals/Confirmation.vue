<template>
  <v-dialog v-model="dialog" max-width="400" width="400" persistent>
    <v-card>
      <v-card-title primary-title>Confirm Deletion</v-card-title>
      <v-card-text class="mt-2">
        {{
          id
            ? "Are you sure you want to permanently delete this item ?"
            : "Are you sure you want to permanently delete selected item(s) ?"
        }}
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>

        <v-btn color="grey darken-1" text @click="dialog = false">Cancel</v-btn>

        <v-btn color="error darken-1" text @click="handleConfirmation"
          >Delete</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: ["id"],

  data() {
    return {
      dialog: false,
    };
  },
  methods: {
    setDialog(value) {
      this.dialog = value;
    },
    handleConfirmation() {
      this.$emit("confirmDeletion");
    },
  },
};
</script>