<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>Edit Role</v-card-title>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('name')"
        ></small>
        <v-text-field
          name="role-name"
          label="Role Name"
          id="role-name"
          v-model="data.name"
          dense
          outlined
        ></v-text-field>

        <small
          class="red--text"
          v-if="validation.hasErrors()"
          v-text="validation.getMessage('permissions')"
        ></small>
        <v-combobox
          :items="permissions"
          v-model="data.rolePermissions"
          item-text="name"
          item-value="id"
          label="Select permissions"
          multiple
          search
          clearable
          dense
          chips
          outlined
          clear-icon="mdi-close-circle-outline"
        >
          <template v-slot:selection="{ attrs, item, select, selected }">
            <v-chip
              v-bind="attrs"
              :input-value="selected"
              close
              @click="select"
              class="mt-2"
              color="primary"
              @click:close="remove(item)"
            >
              <strong>{{ item.name }}</strong>
            </v-chip>
          </template>
        </v-combobox>

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  props: ["singleRole"],

  mixins: [ValidationMixin],

  data() {
    const { id, name, permissions } = this.singleRole;

    return {
      formLoading: false,
      data: {
        id,
        name,
        rolePermissions: permissions,
      },
    };
  },

  methods: {
    ...mapActions({
      updateRole: "role/updateRole",
      getPermissions: "permission/getPermissions",
      getUserAbilities: "auth/getUserAbilities",
    }),

    remove(item) {
      const index = this.data.rolePermissions.findIndex(
        (permission) => permission.id === item.id
      );
      this.data.rolePermissions.splice(index, 1);
      this.data.rolePermissions = [...this.data.rolePermissions];
    },

    async update() {
      this.formLoading = true;
      this.data.permissions = this.data.rolePermissions.length
        ? this.data.rolePermissions.map((permission) => permission.id)
        : this.data.rolePermissions;

      await this.updateRole(this.data);
      await this.getUserAbilities(); // Get latest user abilities

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});
        this.closeDialog();
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },
  },

  watch: {
    singleRole: {
      handler({ id, name, permissions }) {
        this.data.id = id;
        this.data.name = name;
        this.data.rolePermissions = permissions;
      },
    },
  },

  computed: {
    ...mapGetters({
      permissions: "permission/permissions",
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    this.getPermissions();
  },
};
</script>