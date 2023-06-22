<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New Role</v-card-title>
    <v-card-subtitle>Add a New Role</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="add">
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

        <v-btn color="primary" type="submit">Add</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../../mixins/ValidationMixin";

export default {
  mixins: [ValidationMixin],

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
        rolePermissions: [],
      },
    };
  },

  methods: {
    ...mapActions({
      addRole: "role/addRole",
      getPermissions: "permission/getPermissions",
    }),

    remove(item) {
      const index = this.data.rolePermissions.findIndex(
        (permission) => permission.id === item.id
      );
      this.data.rolePermissions.splice(index, 1);
      this.data.rolePermissions = [...this.data.rolePermissions];
    },

    async add() {
      this.formLoading = true;
      this.data.permissions = this.data.rolePermissions.length
        ? this.data.rolePermissions.map((permission) => permission.id)
        : this.data.rolePermissions;

      await this.addRole(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.rolePermissions = [];
        // Clear the validation messages object
        this.validation.setMessages({});
      }
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