<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="users"
      class="elevation-1"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      show-select
      loading-text="Loading users..."
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
          v-if="can('user_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <v-text-field
          v-model="search"
          placeholder="Search"
          class="mx-4"
          append-icon="mdi-magnify"
        ></v-text-field>
      </template>

      <!-- Role(s) -->
      <template slot="item.roles" slot-scope="props">
        <v-chip
          color="primary"
          class="mx-1 my-1"
          small
          v-for="(role, i) in props.item.roles"
          :key="i"
        >
          {{ role.name }}
        </v-chip>
      </template>

      <!-- Actions -->
      <template slot="item.actions" slot-scope="props">
        <v-btn
          x-small
          text
          color="primary"
          @click="showEditDialog(props.item.id)"
          v-if="can('user_edit')"
          title="Edit"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          x-small
          text
          color="red darken-2"
          @click="setUserId(props.item.id)"
          v-if="can('user_delete')"
          title="Delete"
        >
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Edit dialog -->
    <v-dialog v-model="editDialog" max-width="500" persistent>
      <EditUser
        :single-user="user"
        v-if="user"
        @closeDialog="closeEditDialog"
      />
    </v-dialog>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="userId"
      @confirmDeletion="
        userId ? handleUserDelete() : handleMultipleUsersDelete()
      "
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../../mixins/DatatableMixin";
import EditUser from "./EditUser.vue";
import Confirmation from "../../globals/Confirmation";

export default {
  mixins: [DatatableMixin],

  components: { EditUser, Confirmation },

  data() {
    return {
      editDialog: false,
      authToken: localStorage.token,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Name", value: "name" },
        { text: "Email", value: "email" },
        { text: "Role(s)", value: "roles" },
        { text: "Actions", value: "actions" },
      ],
      selectedItems: [],
      userId: null,
    };
  },

  methods: {
    ...mapActions({
      getUsers: "user/getUsers",
      getUser: "user/getUser",
      deleteUser: "user/deleteUser",
      deleteMultipleUsers: "user/deleteMultipleUsers",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getUser(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setUserId(id) {
      this.userId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleUserDelete() {
      await this.deleteUser(this.userId);
      this.userId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleUsersDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleUsers(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      users: "user/users",
      user: "user/user",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("user_edit") && !this.can("user_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    this.getUsers();
  },
};
</script>