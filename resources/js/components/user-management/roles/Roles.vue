<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="roles"
      class="elevation-1"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('role_delete')"
      loading-text="Loading roles..."
      :footer-props="footerProps"
      v-model="selectedItems"
    >
      <!-- S# -->
      <template slot="item.sno" slot-scope="props">{{
        props.index + 1
      }}</template>

      <!-- Top -->
      <template v-slot:top v-if="!printMode">
        <v-btn
          color="error"
          small
          :disabled="!selectedItems.length"
          class="ma-2 text-right"
          @click="deleteMultiple"
          v-if="can('role_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <v-text-field
          v-model="search"
          placeholder="Search"
          class="mx-4"
          append-icon="mdi-magnify"
        ></v-text-field>
      </template>

      <!-- Permissions -->
      <template slot="item.permissions" slot-scope="props">
        <v-btn
          x-small
          color="light"
          @click="showPermissions(props.item.permissions, props.item.name)"
          title="Edit"
          >View Permissions
        </v-btn>
      </template>

      <!-- Actions -->
      <template slot="item.actions" slot-scope="props">
        <v-btn
          x-small
          text
          color="primary"
          @click="showEditDialog(props.item.id)"
          v-if="can('role_edit')"
          title="Edit"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          x-small
          text
          color="red darken-2"
          @click="setRoleId(props.item.id)"
          v-if="can('role_delete')"
          title="Delete"
        >
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Edit dialog -->
    <v-dialog v-model="editDialog" max-width="500" persistent>
      <EditRole
        :single-role="role"
        v-if="role"
        @closeDialog="closeEditDialog"
      />
    </v-dialog>

    <!-- Permissions dialog -->
    <v-dialog v-model="permissionsDialog" max-width="500">
      <RolePermissionsDialog
        :permissions="currentRolePermissions"
        :role="currentRoleName"
      />
    </v-dialog>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="roleId"
      @confirmDeletion="
        roleId ? handleRoleDelete() : handleMultipleRolesDelete()
      "
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../../mixins/DatatableMixin";
import EditRole from "./EditRole.vue";
import Confirmation from "../../globals/Confirmation";
import RolePermissionsDialog from "./partial/RolePermissionsDialog.vue";

export default {
  mixins: [DatatableMixin],

  components: { EditRole, Confirmation, RolePermissionsDialog },

  data() {
    return {
      editDialog: false,
      permissionsDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Role Name", value: "name" },
        { text: "Permissions", value: "permissions" },
        { text: "Actions", value: "actions" },
      ],
      selectedItems: [],
      roleId: null,
      currentRolePermissions: [],
      currentRoleName: null,
    };
  },

  methods: {
    ...mapActions({
      getRoles: "role/getRoles",
      getRole: "role/getRole",
      deleteRole: "role/deleteRole",
      deleteMultipleRoles: "role/deleteMultipleRoles",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getRole(id);
    },

    showPermissions(permissions, roleName) {
      this.currentRolePermissions = permissions;
      this.currentRoleName = roleName;
      this.permissionsDialog = true;
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setRoleId(id) {
      this.roleId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleRoleDelete() {
      await this.deleteRole(this.roleId);
      this.roleId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleRolesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleRoles(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      roles: "role/roles",
      role: "role/role",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("role_edit") && !this.can("role_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    this.getRoles();
  },
};
</script>