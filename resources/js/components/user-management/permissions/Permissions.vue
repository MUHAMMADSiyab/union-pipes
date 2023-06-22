<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="permissions"
      class="elevation-1"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('permission_delete')"
      loading-text="Loading permissions..."
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
          v-if="can('permission_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <v-text-field
          v-model="search"
          placeholder="Search"
          class="mx-4"
          append-icon="mdi-magnify"
        ></v-text-field>
      </template>

      <!-- Actions -->
      <template slot="item.actions" slot-scope="props">
        <v-btn
          x-small
          text
          color="primary"
          @click="showEditDialog(props.item.id)"
          title="Edit"
          v-if="can('permission_edit')"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          x-small
          text
          color="red darken-2"
          @click="setPermissionId(props.item.id)"
          title="Delete"
          v-if="can('permission_delete')"
        >
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Edit dialog -->
    <v-dialog v-model="editDialog" max-width="500" persistent>
      <EditPermission
        :single-permission="permission"
        v-if="permission"
        @closeDialog="closeEditDialog"
      />
    </v-dialog>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="permissionId"
      @confirmDeletion="
        permissionId
          ? handlePermissionDelete()
          : handleMultiplePermissionsDelete()
      "
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../../mixins/DatatableMixin";
import EditPermission from "./EditPermission.vue";
import Confirmation from "../../globals/Confirmation";

export default {
  mixins: [DatatableMixin],

  components: { EditPermission, Confirmation },

  data() {
    return {
      editDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Permission Name", value: "name" },
        { text: "Actions", value: "actions" },
      ],
      selectedItems: [],
      permissionId: null,
    };
  },

  methods: {
    ...mapActions({
      getPermissions: "permission/getPermissions",
      getPermission: "permission/getPermission",
      deletePermission: "permission/deletePermission",
      deleteMultiplePermissions: "permission/deleteMultiplePermissions",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getPermission(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setPermissionId(id) {
      this.permissionId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handlePermissionDelete() {
      await this.deletePermission(this.permissionId);
      this.permissionId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultiplePermissionsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultiplePermissions(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      permissions: "permission/permissions",
      permission: "permission/permission",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("permission_edit") && !this.can("permission_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getPermissions();
  },
};
</script>