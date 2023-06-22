<template>
  <v-card>
    <v-card>
      <v-card-title primary-title>{{ role }}'s Permission</v-card-title>
      <v-card-text>
        <v-chip
          color="primary"
          class="mx-1 my-1"
          v-for="(permission, i) in permissions"
          :key="i"
          >{{ permission.name }}</v-chip
        >
      </v-card-text>
    </v-card>
  </v-card>
</template>

<script>
export default {
  props: ["permissions", "role"],

  watch: {
    permissions: {
      handler(permissions) {
        this.permissions = permissions;
      },
    },
    role: {
      handler(role) {
        this.role = role;
      },
    },
  },
};
</script>