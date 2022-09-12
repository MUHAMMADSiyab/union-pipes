<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Dispensers</h5>

      <v-row>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="dispensers"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('dispenser_delete') && !printMode"
            loading-text="Loading dispensers..."
            :footer-props="footerProps"
            v-model="selectedItems"
          >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
              props.index + 1
            }}</template>

            <!-- Logo -->
            <template
              slot="item.logo"
              slot-scope="props"
              v-if="props.item.logo"
            >
              <v-img
                :aspect-ratio="16 / 9"
                width="50"
                :src="props.item.logo"
                class="rounded"
                @click="showLogoDialog(props.item.logo)"
              ></v-img>
            </template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <v-btn
                color="error"
                small
                :disabled="!selectedItems.length"
                class="ma-2 text-right"
                @click="deleteMultiple"
                v-if="can('dispenser_delete')"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/dispensers/add"
                class="ma-2 text-right"
                v-if="can('dispenser_create')"
              >
                <v-icon left>mdi-account-plus-outline</v-icon>
                New Dispenser</v-btn
              >

              <Excel
                module="dispensers"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV
                module="dispensers"
                :ids="selectedItems.map((item) => item.id)"
              />
              <PDF
                module="dispensers"
                :ids="selectedItems.map((item) => item.id)"
              />

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
                v-if="can('dispenser_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setDispenserId(props.item.id)"
                title="Delete"
                v-if="can('dispenser_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>

          <!-- Edit dialog -->
          <v-dialog v-model="editDialog" max-width="600" persistent>
            <EditDispenser
              :single-dispenser="dispenser"
              v-if="dispenser"
              @closeDialog="closeEditDialog"
            />
          </v-dialog>

          <!-- Logo dialog -->
          <v-dialog v-model="logoDialog" width="400">
            <v-card>
              <v-img width="400" :src="currentLogo" class="rounded"></v-img>
            </v-card>
          </v-dialog>

          <!-- Confirmation -->
          <Confirmation
            ref="confirmationComponent"
            :id="dispenserId"
            @confirmDeletion="
              dispenserId
                ? handleDispenserDelete()
                : handleMultipleDispensersDelete()
            "
          />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditDispenser from "./EditDispenser.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";

export default {
  mixins: [DatatableMixin],

  components: {
    EditDispenser,
    Navbar,
    Confirmation,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      editDialog: false,
      logoDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Dispenser Name", value: "name" },
        { text: "Tank", value: "tank.name" },
        { text: "Description", value: "description" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      dispenserId: null,
      currentLogo: null,
    };
  },

  methods: {
    ...mapActions({
      getDispensers: "dispenser/getDispensers",
      getDispenser: "dispenser/getDispenser",
      deleteDispenser: "dispenser/deleteDispenser",
      deleteMultipleDispensers: "dispenser/deleteMultipleDispensers",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getDispenser(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setDispenserId(id) {
      this.dispenserId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleDispenserDelete() {
      await this.deleteDispenser(this.dispenserId);
      this.dispenserId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleDispensersDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleDispensers(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      dispensers: "dispenser/dispensers",
      dispenser: "dispenser/dispenser",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("dispenser_edit") && !this.can("dispenser_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getDispensers();
  },
};
</script>